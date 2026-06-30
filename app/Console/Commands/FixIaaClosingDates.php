<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Iaa;
use App\Models\IaaLog;

class FixIaaClosingDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iaa:fix-closing-dates {--force : Veritabanını gerçekten günceller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geçmişte kapanmış olan Talep ve Hatalı Bildirim dosyalarının kapanış tarihlerini kalite onay tarihlerine göre yeniden hesaplar.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        $this->info("Eski kayıtlar taranıyor...");

        $iaalar = Iaa::whereIn('durum', ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi'])->get();

        if ($iaalar->isEmpty()) {
            $this->info('Taranacak geçmiş kayıt bulunamadı.');
            return;
        }

        $tableData = [];
        $updates = [];

        foreach ($iaalar as $iaa) {
            $eskiTarih = $iaa->tamamlanma_tarihi ? \Carbon\Carbon::parse($iaa->tamamlanma_tarihi)->format('Y-m-d H:i') : 'Yok';
            $yeniTarih = null;

            if ($iaa->durum == 'talep_olarak_kapatildi') {
                $tarih = $iaa->talep_direktor_at;
                if (!$tarih) {
                    $log = IaaLog::where('iaa_id', $iaa->id)
                                 ->where('eylem', 'Kalite Onayı (Talep)')
                                 ->orderBy('created_at', 'desc')
                                 ->first();
                    if ($log) {
                        $tarih = $log->created_at;
                    }
                }
                $yeniTarih = $tarih;
            } elseif ($iaa->durum == 'hatali_bildirim_olarak_kapatildi') {
                $yeniTarih = $iaa->hatali_bildirim_direktor_at ?? $iaa->hatali_bildirim_kalite_at;
            }

            if ($yeniTarih) {
                $yeniTarihFormatli = \Carbon\Carbon::parse($yeniTarih)->format('Y-m-d H:i');

                // Eğer eski ve yeni tarih arasında dakika bazında bile fark varsa listeye ekle
                if ($eskiTarih !== $yeniTarihFormatli) {
                    $tableData[] = [
                        $iaa->id,
                        $iaa->durum == 'talep_olarak_kapatildi' ? 'Talep Olarak Kapatıldı' : 'Hatalı Bildirim',
                        $eskiTarih,
                        $yeniTarihFormatli
                    ];
                    
                    $updates[] = [
                        'iaa' => $iaa,
                        'yeni_tarih' => $yeniTarih
                    ];
                }
            }
        }

        if (empty($tableData)) {
            $this->info("Değiştirilmesi gereken herhangi bir hatalı tarih bulunamadı. Her şey güncel.");
            return;
        }

        $this->table(['Proje ID', 'Kapanış Türü', 'Mevcut (Hatalı) Tarih', 'Olması Gereken Yeni Tarih'], $tableData);

        if (!$force) {
            $this->warn("\nYukarıdaki liste sadece bir ÖNİZLEMEDİR (Dry Run). Veritabanında şu an HİÇBİR değişiklik yapılmadı.");
            $this->info("Eğer listeyi inceleyip doğruluğunu onaylıyorsanız, değişiklikleri uygulamak için şu komutu çalıştırın:");
            $this->line("php artisan iaa:fix-closing-dates --force");
        } else {
            $this->info("\nVeritabanı güncelleniyor...");
            foreach ($updates as $update) {
                $update['iaa']->update([
                    'tamamlanma_tarihi' => $update['yeni_tarih']
                ]);

                // Eğer bu bir müşteri şikayetiyse, onun da kapanış tarihlerini senkronize et
                if ($update['iaa']->musteriSikayeti) {
                    $update['iaa']->musteriSikayeti->update([
                        'musteri_onay_tarihi' => $update['yeni_tarih'],
                        'kurul_onay_tarihi' => $update['yeni_tarih']
                    ]);
                }
            }
            $this->info(count($updates) . " adet kaydın (ve varsa bağlı Müşteri Şikayetlerinin) tarihi başarıyla güncellendi!");
        }
    }
}
