<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Setting;
use App\Notifications\DogumGunuNotification;
use Illuminate\Support\Facades\Notification;

class ProcessBirthdays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthdays:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bugün doğum günü olan personelleri bulur ve bildirim gönderir.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $birthdayIsActive = Setting::where('key', 'birthday_is_active')->first()?->value ?? '1';
        if ($birthdayIsActive !== '1') {
            $this->info('Doğum günü sistemi ayarlardan kapatılmış.');
            return;
        }

        // Ek ayarları al
        $notifyLeader = Setting::where('key', 'birthday_notify_leader')->first()?->value ?? '1';
        $notifyDirector = Setting::where('key', 'birthday_notify_director')->first()?->value ?? '1';
        $notifyColleagues = Setting::where('key', 'birthday_notify_colleagues')->first()?->value ?? '1';
        $blockList = json_decode(Setting::where('key', 'birthday_block_list')->first()?->value ?? '[]', true);

        $today = now();
        $this->info($today->format('d F') . ' için doğum günü kontrolleri başlatıldı...');

        // Bugün doğum günü olan personeller (Müşteri ve MT hariç)
        $birthdayUsers = User::where('is_personnel', true)
            ->whereNotNull('dogum_tarihi')
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['Müşteri Temsilcisi', 'Müşteri']);
            })
            ->whereMonth('dogum_tarihi', $today->month)
            ->whereDay('dogum_tarihi', $today->day)
            ->get();

        if ($birthdayUsers->isEmpty()) {
            $this->info('Bugün doğum günü olan kimse bulunamadı.');
            return;
        }

        foreach ($birthdayUsers as $user) {
            // Muafiyet listesinde mi kontrol et
            if (in_array($user->id, $blockList)) {
                $this->warn('Muafiyet listesinde: ' . $user->name . ' (Bildirim gönderilmeyecek)');
                continue;
            }

            $this->info('İşleniyor: ' . $user->name);

            // 1. Kendisine bildirim
            $user->notify(new DogumGunuNotification($user, 'self'));

            // 2. Bölüm Liderine bildirim
            if ($user->bolum_id) {
                $bolum = $user->bolum;
                
                if ($notifyLeader == '1') {
                    $liderler = User::where('bolum_id', $user->bolum_id)
                        ->whereHas('roles', function($q) { $q->whereIn('name', ['Bölüm Lideri', 'Bölüm Lider Yardımcısı']); })
                        ->get();
                    
                    foreach ($liderler as $lider) {
                        if ($lider->id !== $user->id) {
                            $lider->notify(new DogumGunuNotification($user, 'manager'));
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
                        // Lider veya yardımcı ise tekrar gönderme
                        if (in_array($colleague->id, $liderIds)) continue;
                        $colleague->notify(new DogumGunuNotification($user, 'colleague'));
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
                        $direktor->notify(new DogumGunuNotification($user, 'manager'));
                    }
                }
            }
        }

        $this->info('Tüm bildirimler başarıyla gönderildi.');
    }
}
