<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Iaa;
use App\Models\MusteriSikayeti;
use App\Models\IaaLog;
use Illuminate\Support\Facades\DB;

class FixProjectDataConsistency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iaa:fix-data-consistency {--dry-run : Sadece yapılacakları gösterir, veriyi değiştirmez}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Şikayet ve Proje tabloları arasındaki durum ve tarih tutarsızlıklarını düzeltir.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info('DRY RUN MODU: Değişiklik yapılmayacak.');
        }

        $this->info('Veri tutarlılığı kontrolü başlatılıyor...');

        // 1. Durum Senkronizasyonu (Proje Kapalıysa Şikayeti Kapat)
        $this->syncStatuses($dryRun);

        // 2. Tarih Senkronizasyonu (Loglardan eksik tarihleri tamamla)
        $this->syncDates($dryRun);

        // 3. Bölüm Senkronizasyonu
        $this->syncDepartments($dryRun);

        $this->info('İşlem tamamlandı.');
    }

    private function syncStatuses($dryRun)
    {
        $this->comment('1. Durum senkronizasyonu kontrol ediliyor...');
        
        $mismatched = MusteriSikayeti::whereNotNull('iaa_id')
            ->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])
            ->whereHas('iaaProjesi', function($q) {
                $q->whereIn('durum', ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi']);
            })->get();

        if ($mismatched->isEmpty()) {
            $this->line('Durum tutarsızlığı bulunmadı.');
            return;
        }

        foreach ($mismatched as $s) {
            $this->warn("Şikayet ID: {$s->id} - Durum: {$s->musteri_durum} (Proje kapalı ama şikayet açık)");
            if (!$dryRun) {
                $s->update(['musteri_durum' => 'Kapatıldı']);
                $this->info('-> Kapatıldı olarak güncellendi.');
            }
        }
    }

    private function syncDates($dryRun)
    {
        $this->comment('2. Tarih senkronizasyonu (Log bazlı düzeltme) kontrol ediliyor...');
        
        // Tüm kapalı projeleri kontrol et (Tarihi olsun olmasın)
        $projects = Iaa::whereIn('durum', ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi'])->get();

        foreach ($projects as $p) {
            $logDate = IaaLog::where('iaa_id', $p->id)
                ->whereIn('eylem', ['Direktör Onayı Verildi', 'Bölüm Onayı Verildi', 'Proje Onaylandı', 'Hatalı Bildirim Onaylandı', 'Talep Onaylandı'])
                ->latest()
                ->value('created_at');

            if ($logDate) {
                // Mevcut tarih ile log tarihi arasında fark var mı? (Saniye hassasiyeti olmadan kontrol için formatlıyoruz)
                $currentDate = $p->tamamlanma_tarihi ?? $p->onaylanma_tarihi;
                $hasDifference = !$currentDate || ($logDate->format('Y-m-d H:i') != \Carbon\Carbon::parse($currentDate)->format('Y-m-d H:i'));

                if ($hasDifference) {
                    $oldDateText = $currentDate ? \Carbon\Carbon::parse($currentDate)->format('d.m.Y H:i') : 'Boş';
                    $this->warn("Proje ID: {$p->id} - Tarih Güncelleniyor: [Eski: {$oldDateText}] -> [Yeni (Log): {$logDate->format('d.m.Y H:i')}]");
                    
                    if (!$dryRun) {
                        $p->update([
                            'tamamlanma_tarihi' => $logDate,
                            'onaylanma_tarihi' => $logDate // İki alanı da senkron tutalım
                        ]);
                    }
                }
            }
        }
    }

    private function syncDepartments($dryRun)
    {
        $this->comment('3. Bölüm senkronizasyonu kontrol ediliyor...');
        
        $mismatched = Iaa::whereHas('musteriSikayeti.sikayetKategori')
            ->get()
            ->filter(function($p) {
                return $p->bolum_id != $p->musteriSikayeti->sikayetKategori->bolum_id;
            });

        if ($mismatched->isEmpty()) {
            $this->line('Bölüm tutarsızlığı bulunmadı.');
            return;
        }

        foreach ($mismatched as $p) {
            $correctBolumId = $p->musteriSikayeti->sikayetKategori->bolum_id;
            $this->warn("Proje ID: {$p->id} - Mevcut Bölüm: {$p->bolum_id}, Olması Gereken: {$correctBolumId}");
            if (!$dryRun) {
                $p->update(['bolum_id' => $correctBolumId]);
                $this->info('-> Bölüm ID güncellendi.');
            }
        }
    }
}
