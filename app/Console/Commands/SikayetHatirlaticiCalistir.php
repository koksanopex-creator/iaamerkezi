<?php

namespace App\Console\Commands;

use App\Models\SikayetHatirlaticiKurali;
use App\Models\MusteriSikayeti;
use App\Models\User;
use App\Notifications\SikayetHatirlatmaBildirimi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SikayetHatirlaticiCalistir extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sikayet-hatirlatici-calistir';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatik şikayet hatırlatıcı kurallarını denetler ve bildirim gönderir.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Otomatik Hatırlatıcı Scheduler Başladı.');

        $kurallar = SikayetHatirlaticiKurali::where('aktif', true)->get();
        $simdi = now();

        foreach ($kurallar as $kural) {
            // Zaman Kontrolü
            if (!$this->zamanUygunMu($kural, $simdi)) {
                continue;
            }

            $sikayetler = MusteriSikayeti::whereIn('musteri_durum', $kural->proje_durumlari)
                ->whereNotIn('musteri_durum', ['Kapatıldı', 'Çözümlendi'])
                ->get();

            foreach ($sikayetler as $sikayet) {
                $buKullanicilaraBildir = $this->aliciKullanicilariTespitEt($kural, $sikayet);

                foreach ($buKullanicilaraBildir as $user) {
                    try {
                        $user->notify(new \App\Notifications\OtomatikSikayetHatirlatmaBildirimi($sikayet, $kural));
                    } catch (\Exception $e) {
                        Log::error("Otomatik hatırlatma gönderilemedi (User: {$user->id}): " . $e->getMessage());
                    }
                }
            }

            $kural->update(['son_calisma_tarihi' => $simdi]);
            $this->info("Kural tetiklendi: {$kural->ad}");
        }

        return Command::SUCCESS;
    }

    protected function zamanUygunMu($kural, $simdi)
    {
        // Saat kontrolü (Sadece o dakika aralığında bir kez çalışması yeterli)
        $kuralSaati = \Carbon\Carbon::createFromFormat('H:i:s', $kural->saat)->format('H:i');
        if ($simdi->format('H:i') !== $kuralSaati) {
            return false;
        }

        // Sıklık kontrolü
        if ($kural->siklik === 'haftalik') {
            return in_array($simdi->dayOfWeekIso, $kural->haftanin_gunleri ?? []);
        }

        if ($kural->siklik === 'aylik') {
            return $simdi->day === 1;
        }

        return true; // Günlük
    }

    protected function aliciKullanicilariTespitEt($kural, $sikayet)
    {
        $users = collect();

        // 1. Rol Bazlı Alıcılar
        foreach ($kural->bildirim_rolleri ?? [] as $rol) {
            // Burada şikayetin bölümüne göre o roldeki kullanıcıları bulmalıyız
            $bolumId = $sikayet->sikayetKategori->bolum_id ?? null;

            if ($rol === 'Yonetim') {
                $users = $users->merge(User::role('Yonetim')->get());
            } elseif ($rol === 'Direktör' && $bolumId) {
                $bolum = \App\Models\Bolum::find($bolumId);
                if ($bolum && $bolum->director_id) {
                    $users->push(User::find($bolum->director_id));
                }
            } elseif ($rol === 'Bölüm Lideri' && $bolumId) {
                $bolumLiderleri = User::role('Bölüm Lideri')->where('bolum_id', $bolumId)->get();
                $users = $users->merge($bolumLiderleri);
            } elseif ($rol === 'Bölüm Kalite Yöneticisi' && $bolumId) {
                // Kategori bazlı kalite yöneticilerini bul
                $kaliteYoneticileri = User::whereHas('yonettigiSikayetKategorileri', function($q) use ($sikayet) {
                    $q->where('sikayet_kategorileri.id', $sikayet->sikayet_kategorisi_id);
                })->get();
                $users = $users->merge($kaliteYoneticileri);
            } elseif ($rol === 'Müşteri Şikayeti Çözüm Lideri') {
                if ($sikayet->cozumTakimi && $sikayet->cozumTakimi->lider_user_id) {
                    $users->push(User::find($sikayet->cozumTakimi->lider_user_id));
                }
            }
        }

        // 2. Şikayeti Giren Personel
        if ($kural->sikayeti_girene_bildir && $sikayet->olusturan_user_id) {
            $users->push(User::find($sikayet->olusturan_user_id));
        }

        // 3. Müşteri
        if ($kural->musteriye_bildir && $sikayet->yetkili_user_id) {
            $users->push(User::find($sikayet->yetkili_user_id));
        }

        // 4. Ek Kullanıcılar
        if (!empty($kural->ek_kullanici_ids)) {
            $users = $users->merge(User::whereIn('id', $kural->ek_kullanici_ids)->get());
        }

        return $users->filter()->unique('id');
    }
}
