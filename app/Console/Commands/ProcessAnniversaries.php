<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Setting;
use App\Notifications\YildonumuNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ProcessAnniversaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anniversaries:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bugün işe giriş yıldönümü olan personelleri tespit eder ve bildirim gönderir.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isActive = Setting::where('key', 'anniversary_is_active')->first()?->value ?? '1';
        if ($isActive !== '1') {
            $this->warn('İş yıldönümü sistemi kapalı.');
            return;
        }

        $notifyLeader = Setting::where('key', 'anniversary_notify_leader')->first()?->value ?? '1';
        $notifyDirector = Setting::where('key', 'anniversary_notify_director')->first()?->value ?? '1';
        $notifyColleagues = Setting::where('key', 'anniversary_notify_colleagues')->first()?->value ?? '1';
        $blockList = json_decode(Setting::where('key', 'anniversary_block_list')->first()?->value ?? '[]', true);

        $today = now();
        $this->info($today->format('d F') . ' için iş yıldönümü kontrolleri başlatıldı...');

        // Bugün işe giriş yıldönümü olan personeller
        // Not: hire_date (ise_giris_tarihi) alanına göre kontrol edilir. 
        // Bazı veritabanlarında 'hire_date' bazıları 'ise_giris_tarihi' olabilir. 
        // Genellikle hire_date kullanılıyor.
        
        $anniversaryUsers = User::where('is_personnel', true)
            ->whereNotNull('hire_date')
            ->whereMonth('hire_date', $today->month)
            ->whereDay('hire_date', $today->day)
            ->get();

        if ($anniversaryUsers->isEmpty()) {
            $this->info('Bugün iş yıldönümü olan kimse bulunamadı.');
            return;
        }

        foreach ($anniversaryUsers as $user) {
            // Kaçıncı yıl?
            $hireDate = Carbon::parse($user->hire_date);
            $years = $today->year - $hireDate->year;

            // 0. yıl (bugün girmişse) bildirim gönderilmez veya özel gönderilir. Genellikle 1. yıldan başlar.
            if ($years <= 0) continue;

            // Muafiyet listesinde mi kontrol et
            if (in_array($user->id, $blockList)) {
                $this->warn('Muafiyet listesinde: ' . $user->name . ' (Bildirim gönderilmeyecek)');
                continue;
            }

            $this->info('İşleniyor: ' . $user->name . ' (' . $years . '. Yıl)');

            // 1. Kendisine bildirim
            $user->notify(new YildonumuNotification($user, 'self', $years));

            // 2. Bölüm Liderine bildirim
            if ($user->bolum_id) {
                $bolum = $user->bolum;
                
                if ($notifyLeader == '1') {
                    $liderler = User::where('bolum_id', $user->bolum_id)
                        ->whereHas('roles', function($q) { $q->whereIn('name', ['Bölüm Lideri', 'Bölüm Lider Yardımcısı']); })
                        ->get();
                    
                    foreach ($liderler as $lider) {
                        if ($lider->id !== $user->id) {
                            $lider->notify(new YildonumuNotification($user, 'manager', $years));
                        }
                    }
                }

                // 3. Bölüm arkadaşları
                if ($notifyColleagues == '1') {
                    $colleagues = User::where('bolum_id', $user->bolum_id)
                        ->where('id', '!=', $user->id)
                        ->where('is_personnel', true)
                        ->get();
                    
                    $liderIds = isset($liderler) ? $liderler->pluck('id')->toArray() : [];
                    foreach ($colleagues as $colleague) {
                        if (in_array($colleague->id, $liderIds)) continue;
                        $colleague->notify(new YildonumuNotification($user, 'colleague', $years));
                    }
                }

                // 4. Direktör
                if ($notifyDirector == '1') {
                    $direktor = null;
                    if ($bolum && $bolum->director_id) {
                        $direktor = User::find($bolum->director_id);
                    } else {
                        $direktor = User::where('bolum_id', $user->bolum_id)
                            ->whereHas('roles', function($q) { $q->where('name', 'Direktör'); })
                            ->first();
                    }

                    if ($direktor && $direktor->id !== $user->id) {
                        $direktor->notify(new YildonumuNotification($user, 'manager', $years));
                    }
                }
            }
        }

        $this->info('İşlem tamamlandı.');
    }
}
