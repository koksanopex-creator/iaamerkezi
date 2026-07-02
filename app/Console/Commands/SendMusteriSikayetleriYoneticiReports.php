<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MusteriSikayetiYoneticiRaporKurali;
use App\Models\User;
use App\Models\MusteriSikayeti;
use App\Notifications\MusteriSikayetiManagerReportNotification;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Notification;

class SendMusteriSikayetleriYoneticiReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sikayet:yonetici-raporlari-gonder {--kural_id= : Özel olarak çalıştırılacak kural ID\'si (Anlık Gönderim İçin)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Müşteri Şikayeti Kurul Yöneticilerine performans raporlarını gönderir.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $kuralId = $this->option('kural_id');

        if ($kuralId) {
            $kural = MusteriSikayetiYoneticiRaporKurali::find($kuralId);
            if ($kural) {
                $this->info("Anlık rapor gönderimi başlatıldı: " . $kural->ad);
                $this->processRule($kural);
            } else {
                $this->error("Kural bulunamadı.");
            }
            return;
        }

        $now = Carbon::now();
        $currentTime = $now->format('H:i');
        $currentDay = $now->format('l'); 
        
        $gunlerTurkce = [
            'Monday' => 'Pazartesi',
            'Tuesday' => 'Sali',
            'Wednesday' => 'Carsamba',
            'Thursday' => 'Persembe',
            'Friday' => 'Cuma',
            'Saturday' => 'Cumartesi',
            'Sunday' => 'Pazar'
        ];
        $currentDayTr = $gunlerTurkce[$currentDay];
        
        $this->info("Rapor gönderimi başlatıldı. Saat: $currentTime, Gün: $currentDayTr");

        $kurallar = MusteriSikayetiYoneticiRaporKurali::where('aktif', true)
            ->where(function($q) use ($currentTime) {
                $q->whereRaw("TIME_FORMAT(saat, '%H:%i') = ?", [$currentTime]);
            })
            ->get();

        foreach ($kurallar as $kural) {
            $gonder = false;
            $periyot = $kural->periyot ?? 1;

            if ($kural->siklik === 'gunluk') {
                $daysPassed = $now->copy()->startOfDay()->diffInDays($kural->created_at->copy()->startOfDay());
                if ($daysPassed % $periyot === 0) {
                    $gonder = true;
                }
            } elseif ($kural->siklik === 'haftalik') {
                $weeksPassed = $now->copy()->startOfWeek()->diffInWeeks($kural->created_at->copy()->startOfWeek());
                if ($weeksPassed % $periyot === 0) {
                    $haftaninGunleri = is_array($kural->haftanin_gunleri) ? $kural->haftanin_gunleri : json_decode($kural->haftanin_gunleri, true);
                    if (is_array($haftaninGunleri) && in_array($currentDayTr, $haftaninGunleri)) {
                        $gonder = true;
                    }
                }
            } elseif ($kural->siklik === 'aylik') {
                $monthsPassed = $now->copy()->startOfMonth()->diffInMonths($kural->created_at->copy()->startOfMonth());
                if ($monthsPassed % $periyot === 0) {
                    $ayinGunleri = is_array($kural->ayin_gunleri) ? $kural->ayin_gunleri : json_decode($kural->ayin_gunleri, true);
                    if (empty($ayinGunleri)) {
                        $ayinGunleri = [1];
                    }
                    
                    if (in_array($now->day, $ayinGunleri) || (in_array('son_gun', $ayinGunleri) && $now->day === $now->daysInMonth)) {
                        $gonder = true;
                    }
                }
            }

            if ($gonder && $kural->son_calisma_tarihi && $now->isSameDay($kural->son_calisma_tarihi)) {
                $gonder = false;
            }

            if ($gonder) {
                $this->processRule($kural);
            }
        }

        $this->info("İşlem tamamlandı.");
    }

    protected function processRule($kural)
    {
        $this->info("Kural işleniyor: " . $kural->ad);
        $yediGunOnce = now()->subDays(7);

        // --- BÖLÜM 1: OTOMATİK DİNAMİK YÖNETİCİLER (Mevcut Mantık) ---
        // Bu yöneticiler kendi yetki alanlarındaki (kapsamlarındaki) kullanıcıları otomatik görür
        $yoneticiler = User::role([
            'Müşteri Şikayeti Kurulu Yöneticisi',
            'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi',
            'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'
        ])->get();

        foreach ($yoneticiler as $yonetici) {
            if ($yonetici->hasRole('Müşteri Şikayeti Kurulu Yöneticisi')) {
                $kurulUyeleri = User::role(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı'])->get();
            } elseif ($yonetici->hasRole('Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi')) {
                $kurulUyeleri = User::role(['Müşteri Şikayeti Kurulu - Yurt İçi'])->get();
            } elseif ($yonetici->hasRole('Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı')) {
                $kurulUyeleri = User::role(['Müşteri Şikayeti Kurulu - Yurt Dışı'])->get();
            } else {
                continue;
            }

            $ekipPerformansi = $this->performansHesapla($kurulUyeleri, $yediGunOnce);

            if (count($ekipPerformansi) > 0) {
                $yonetici->notify(new MusteriSikayetiManagerReportNotification($kural, $ekipPerformansi));
                $this->info("-> Dinamik Yönetici {$yonetici->name} için bildirim gönderildi.");
            }
        }

        // --- BÖLÜM 2: EKSTRA ALICILAR (Seçili Roller, Kullanıcılar ve Harici E-postalar) ---
        $ekstraAlicilar = collect();

        // 1. Rollerdeki Kullanıcılar
        if (!empty($kural->alicilar['roller'])) {
            foreach ($kural->alicilar['roller'] as $roleId) {
                $role = Role::find($roleId);
                if ($role) {
                    $usersWithRole = User::role($role->name)->get();
                    foreach ($usersWithRole as $u) {
                        $ekstraAlicilar->push($u);
                    }
                }
            }
        }

        // 2. Doğrudan Seçilen Kullanıcılar
        if (!empty($kural->alicilar['users'])) {
            $directUsers = User::whereIn('id', $kural->alicilar['users'])->get();
            foreach ($directUsers as $u) {
                $ekstraAlicilar->push($u);
            }
        }

        // 3. Harici E-postalar
        $hariciEmailler = collect();
        if (!empty($kural->alicilar['emails'])) {
            $external = is_array($kural->alicilar['emails']) ? $kural->alicilar['emails'] : explode(',', $kural->alicilar['emails']);
            foreach ($external as $email) {
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $hariciEmailler->push($email);
                }
            }
        }

        // Tekilleştirme (Kullanıcılar ID'ye göre)
        $ekstraAlicilar = $ekstraAlicilar->unique('id');
        $hariciEmailler = $hariciEmailler->unique();

        // Ekstra alıcıların göreceği kapsamı belirle
        $kapsamRolleri = [];
        if ($kural->rapor_kapsami === 'tum_kurul') {
            $kapsamRolleri = ['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı'];
        } elseif ($kural->rapor_kapsami === 'yurt_ici_kurul') {
            $kapsamRolleri = ['Müşteri Şikayeti Kurulu - Yurt İçi'];
        } elseif ($kural->rapor_kapsami === 'yurt_disi_kurul') {
            $kapsamRolleri = ['Müşteri Şikayeti Kurulu - Yurt Dışı'];
        }

        if (count($kapsamRolleri) > 0) {
            $hedefUyeler = User::role($kapsamRolleri)->get();
            $hedefPerformansi = $this->performansHesapla($hedefUyeler, $yediGunOnce);

            if (count($hedefPerformansi) > 0) {
                // Kullanıcılara Bildirim Gönder (Veritabanı + Mail)
                foreach ($ekstraAlicilar as $user) {
                    // Yönetici listesinde zaten gönderilmişse atla
                    if ($yoneticiler->contains('id', $user->id)) continue; 
                    
                    $user->notify(new MusteriSikayetiManagerReportNotification($kural, $hedefPerformansi));
                    $this->info("-> Seçili Kullanıcı {$user->name} için bildirim gönderildi.");
                }

                // Harici e-postalara sadece Mail gönder
                foreach ($hariciEmailler as $email) {
                    Notification::route('mail', $email)
                        ->notify(new MusteriSikayetiManagerReportNotification($kural, $hedefPerformansi));
                    $this->info("-> Harici e-posta {$email} için bildirim gönderildi.");
                }
            }
        }

        $kural->update(['son_calisma_tarihi' => now()]);
    }

    private function performansHesapla($kurulUyeleri, $yediGunOnce)
    {
        $ekipPerformansi = [];

        foreach ($kurulUyeleri as $uye) {
            $baseQuery = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $uye->id);
            
            $toplam = (clone $baseQuery)->count();
            $cozumlenen = (clone $baseQuery)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])->count();
            $iptalRed = (clone $baseQuery)->whereIn('musteri_durum', ['İptal Edildi', 'Reddedildi', 'Tamamlanması Reddedildi'])->count();
            $son7Gun = (clone $baseQuery)->where('created_at', '>=', $yediGunOnce)->count();

            $ekipPerformansi[] = (object)[
                'name' => $uye->name,
                'toplam' => $toplam,
                'cozumlenen' => $cozumlenen,
                'iptal_red' => $iptalRed,
                'son_7_gun' => $son7Gun
            ];
        }

        return $ekipPerformansi;
    }
}
