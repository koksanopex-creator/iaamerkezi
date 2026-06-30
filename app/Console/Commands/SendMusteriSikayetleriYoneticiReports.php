<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MusteriSikayetiYoneticiRaporKurali;
use App\Models\User;
use App\Models\MusteriSikayeti;
use App\Notifications\MusteriSikayetiManagerReportNotification;
use Carbon\Carbon;

class SendMusteriSikayetleriYoneticiReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sikayet:yonetici-raporlari-gonder';

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
        $now = Carbon::now();
        $currentTime = $now->format('H:i');
        $currentDay = $now->format('l'); // e.g. 'Monday'
        
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

        // Aktif ve saati gelen kuralları bul
        $kurallar = MusteriSikayetiYoneticiRaporKurali::where('aktif', true)
            ->where(function($q) use ($currentTime) {
                // Saat toleransı bırakabiliriz (cron 5 dk da bir çalışırsa diye)
                $q->whereRaw("TIME_FORMAT(saat, '%H:%i') = ?", [$currentTime]);
            })
            ->get();

        foreach ($kurallar as $kural) {
            $gonder = false;

            if ($kural->siklik === 'gunluk') {
                $gonder = true;
            } elseif ($kural->siklik === 'haftalik') {
                $haftaninGunleri = is_array($kural->haftanin_gunleri) ? $kural->haftanin_gunleri : json_decode($kural->haftanin_gunleri, true);
                if (is_array($haftaninGunleri) && in_array($currentDayTr, $haftaninGunleri)) {
                    $gonder = true;
                }
            } elseif ($kural->siklik === 'aylik') {
                if ($now->day === 1) {
                    $gonder = true;
                }
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

        // 1. Tüm Yöneticileri Bul
        $yoneticiler = User::role([
            'Müşteri Şikayeti Kurulu Yöneticisi',
            'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi',
            'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'
        ])->get();

        $yediGunOnce = now()->subDays(7);

        foreach ($yoneticiler as $yonetici) {
            // Yöneticinin Yetki Alanındaki Kullanıcılar
            if ($yonetici->hasRole('Müşteri Şikayeti Kurulu Yöneticisi')) {
                $kurulUyeleri = User::role([
                    'Müşteri Şikayeti Kurulu', 
                    'Müşteri Şikayeti Kurulu - Yurt İçi', 
                    'Müşteri Şikayeti Kurulu - Yurt Dışı'
                ])->get();
            } elseif ($yonetici->hasRole('Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi')) {
                $kurulUyeleri = User::role(['Müşteri Şikayeti Kurulu - Yurt İçi'])->get();
            } elseif ($yonetici->hasRole('Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı')) {
                $kurulUyeleri = User::role(['Müşteri Şikayeti Kurulu - Yurt Dışı'])->get();
            } else {
                continue;
            }

            $ekipPerformansi = [];

            foreach ($kurulUyeleri as $uye) {
                $baseQuery = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $uye->id);
                
                $toplam = (clone $baseQuery)->count();
                $hataliBildirim = (clone $baseQuery)->whereHas('iaaProjesi', function($q) {
                    $q->where('durum', 'hatali_bildirim_olarak_kapatildi');
                })->count();
                $talepKapanan = (clone $baseQuery)->whereHas('iaaProjesi', function($q) {
                    $q->where('durum', 'talep_olarak_kapatildi');
                })->count();
                $son7Gun = (clone $baseQuery)->where('created_at', '>=', $yediGunOnce)->count();

                $ekipPerformansi[] = (object)[
                    'name' => $uye->name,
                    'toplam' => $toplam,
                    'hatali_bildirim' => $hataliBildirim,
                    'talep_kapanan' => $talepKapanan,
                    'son_7_gun' => $son7Gun
                ];
            }

            // Eğer ekipte kimse yoksa gönderme
            if (count($ekipPerformansi) > 0) {
                $yonetici->notify(new MusteriSikayetiManagerReportNotification($kural, $ekipPerformansi));
                $this->info("-> {$yonetici->name} için bildirim gönderildi.");
            }
        }

        $kural->update(['son_calisma_tarihi' => now()]);
    }
}
