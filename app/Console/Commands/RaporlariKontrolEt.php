<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RaporKurali;
use App\Models\User;
use App\Mail\OtomatikYoneticiRaporu;
use App\Services\RaporVeriServisi;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class RaporlariKontrolEt extends Command
{
    /**
     * Komutun sistemdeki adı.
     */
    protected $signature = 'rapor:kontrol-et';

    /**
     * Komutun açıklaması.
     */
    protected $description = 'Zamanı gelen otomatik raporları kontrol eder ve gönderir.';

    /**
     * Komutu çalıştıran ana fonksiyon.
     */
    public function handle()
    {
        // 1. Sunucu saatini ekrana yazdıralım
        $suan = now()->format('H:i');
        $this->info("------------------------------------------------");
        $this->info("KOMUT TETİKLENDİ");
        $this->info("Sunucu Saati: " . $suan);
        $this->info("Bugün: " . now()->translatedFormat('l'));
        
        // 2. Veritabanı sorgusu
        $kurallar = RaporKurali::where('aktif', true)
                               ->where('gonderim_saati', 'like', $suan . '%')
                               ->get();

        $this->info("Veritabanında Bu Saate ($suan) Ayarlı Kural Sayısı: " . $kurallar->count());

        if ($kurallar->isEmpty()) {
            $this->error("❌ Şu an gönderilecek bir rapor bulunamadı. (Saat eşleşmedi)");
            $this->info("------------------------------------------------");
            return;
        }

        foreach ($kurallar as $kural) {
            $this->info("✅ Kural Bulundu: " . $kural->baslik);

            // A. Periyot Kontrolü
            if (!$this->bugunGonderilmeliMi($kural)) {
                $this->warn("   -> Ancak bugün gönderim günü değil. (Periyot: {$kural->periyot})");
                continue;
            }

            // B. Çifte gönderim kontrolü
            if ($kural->son_gonderim_tarihi && Carbon::parse($kural->son_gonderim_tarihi)->isToday()) {
                $this->warn("   -> Bu rapor bugün zaten gönderilmiş.");
                continue;
            }

            $this->info("   -> Rapor hazırlanıyor ve gönderiliyor...");

            // C. Alıcıları Topla
            $alicilar = collect();

            if (!empty($kural->alicilar['roller'])) {
                $roleIds = $kural->alicilar['roller'];
                $roleNames = \Spatie\Permission\Models\Role::whereIn('id', (array)$roleIds)->pluck('name')->toArray();
                if (!empty($roleNames)) {
                    $users = User::role($roleNames)->get(); 
                    foreach ($users as $user) {
                        $alicilar->push($user->email);
                    }
                }
            }
            if (!empty($kural->alicilar['users'])) {
                $userIds = $kural->alicilar['users'];
                $users = User::whereIn('id', $userIds)->get();
                foreach ($users as $user) $alicilar->push($user->email);
            }
            if (!empty($kural->alicilar['emails'])) {
                $rawEmails = $kural->alicilar['emails'];
                
                if (is_array($rawEmails)) {
                    $external = $rawEmails;
                } else {
                    $normalized = str_replace(["\r\n", "\r", "\n", ";"], ',', $rawEmails);
                    $external = explode(',', $normalized);
                }
                
                $external = array_filter(array_map('trim', $external));
                $alicilar = $alicilar->merge($external);
            }

            $alicilar = $alicilar->filter()->unique();

            if ($alicilar->isEmpty()) {
                $this->warn("      -> Alıcı bulunamadı, gönderim iptal.");
                continue;
            }

            // D. Disiplin Özel Kuralları
            $isDisiplinScoped = ($kural->icerik_ayarlari['disiplin_ozet'] ?? false) && $kural->disiplin_kapsam === 'kendi_bolumu';

            foreach ($alicilar as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        $user = User::where('email', $email)->first();
                        $raporIcerikData = null;

                        // Eğer disiplin için kişiye özel kapsam gerekiyorsa, o kişiye özel veri oluştur
                        if ($isDisiplinScoped && $user) {
                            $kisiselServis = new RaporVeriServisi();
                            $kisiselServis->setUser($user);
                            
                            if (!empty($kural->disiplin_suc_kategorileri)) {
                                $kisiselServis->setDisiplinKategoriFiltresi($kural->disiplin_suc_kategorileri);
                            }

                            $raporIcerikData = $kisiselServis->verileriTopla($kural->icerik_ayarlari ?? []);
                        } elseif ($user && !empty($kural->disiplin_suc_kategorileri) && ($kural->icerik_ayarlari['disiplin_ozet'] ?? false)) {
                            $kisiselServis = new RaporVeriServisi();
                            $kisiselServis->setUser(null); // Tüm bölümler (Global)
                            $kisiselServis->setDisiplinKategoriFiltresi($kural->disiplin_suc_kategorileri);
                            $raporIcerikData = $kisiselServis->verileriTopla($kural->icerik_ayarlari ?? []);
                        } else {
                            // Global veri
                            if (!isset($globalRaporData)) {
                                $globalServis = new RaporVeriServisi();
                                if (!empty($kural->disiplin_suc_kategorileri)) {
                                    $globalServis->setDisiplinKategoriFiltresi($kural->disiplin_suc_kategorileri);
                                }
                                $globalRaporData = $globalServis->verileriTopla($kural->icerik_ayarlari ?? []);
                            }
                            $raporIcerikData = $globalRaporData;
                        }

                        Mail::to($email)->queue(new OtomatikYoneticiRaporu($raporIcerikData, $kural->baslik));
                        $this->info("      -> Mail Gönderildi: $email");
                    } catch (\Exception $e) {
                        $this->error("      -> HATA: $email - " . $e->getMessage());
                        Log::error("Rapor Hatası: " . $e->getMessage());
                    }
                }
            }

            $kural->update(['son_gonderim_tarihi' => now()]);
        }
        
        $this->info("------------------------------------------------");
    }

    private function bugunGonderilmeliMi($kural)
    {
        $periyot = $kural->periyot;
        
        // 1. GÜNLÜK ise her gün gönder
        if ($periyot == 'gunluk') {
            return true;
        }

        // Seçilen günler boşsa güvenlik için false dön (Hata olmasın)
        if (empty($kural->gunler)) {
            return false;
        }

        // 2. HAFTALIK KONTROLÜ
        if ($periyot == 'haftalik') {
            // Bugünün İngilizce adı: "Monday", "Friday" vs.
            $bugun = now()->format('l'); 
            
            // Veritabanındaki listede bugün var mı?
            // Örn: DB'de ["Monday", "Friday"] var. Bugün "Friday" ise TRUE döner.
            return in_array($bugun, $kural->gunler);
        }

        // 3. AYLIK KONTROLÜ
        if ($periyot == 'aylik') {
            // Bugün ayın kaçı? (1, 15, 23...)
            $bugun = now()->day;
            
            // Veritabanında kayıtlı gün mü? (Aylıkta tek gün seçtirdik ama array içinde olabilir)
            // Kullanıcı "15" seçtiyse, $kural->gunler içinde "15" var mı bakar.
            // Not: Selectbox string döndürebilir, bu yüzden in_array gevşek kontrol yapar, sorun olmaz.
            return in_array($bugun, (array)$kural->gunler);
        }

        return false;
    }
}