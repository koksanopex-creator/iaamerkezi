<?php

namespace App\Services;

use App\Models\SikayetHatirlatma;
use App\Models\SikayetHatirlatmaYorumu;
use App\Models\SikayetHatirlatmaBildirilen;
use App\Models\MusteriSikayeti;
use App\Models\User;
use App\Models\Setting;
use App\Notifications\SikayetHatirlatmaBildirimi;
use App\Notifications\SikayetHatirlatmaYaniti;
use App\Notifications\SikayetHatirlatmaIknaOldu;
use App\Models\MusteriSikayetiLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SikayetHatirlatmaService
{
    /**
     * Yeni bir manuel hatırlatma oluşturur ve bildirir.
     */
    public function hatirlatmaGonder(MusteriSikayeti $sikayet, ?string $aciklama = null)
    {
        // 1. Cooldown Kontrolü
        $mevcutHatirlatma = SikayetHatirlatma::where('musteri_sikayeti_id', $sikayet->id)
            ->where('durum', '!=', 'kapatildi')
            ->first();

        if ($mevcutHatirlatma && $mevcutHatirlatma->sonraki_hak_tarihi > now()) {
            $kalan = now()->diffForHumans($mevcutHatirlatma->sonraki_hak_tarihi, true);
            throw new \Exception("Henüz yeni bir hatırlatma gönderemezsiniz. Tekrar gönderim hakkı için kalan süre: {$kalan}");
        }

        return DB::transaction(function () use ($sikayet, $mevcutHatirlatma, $aciklama) {
            $user = Auth::user();
            
            // 2. Hatırlatma Kaydını Oluştur veya Güncelle
            if (!$mevcutHatirlatma) {
                $hatirlatma = SikayetHatirlatma::create([
                    'musteri_sikayeti_id' => $sikayet->id,
                    'gonderen_user_id' => $user->id,
                    'durum' => 'bilgi_girisi_bekleniyor',
                    'hatirlatma_sayisi' => 1,
                ]);
            } else {
                $hatirlatma = $mevcutHatirlatma;
                $hatirlatma->increment('hatirlatma_sayisi');
                $hatirlatma->update([
                    'durum' => 'bilgi_girisi_bekleniyor',
                ]);
            }

            // 3. Alıcıları Tespit Et
            $alicilar = $this->getHatirlatmaAlicilari($sikayet);
            
            // 4. Bildirilenler Tablosuna Kaydet ve Bildirim Gönder
            foreach ($alicilar as $alici) {
                // Kayıt
                SikayetHatirlatmaBildirilen::updateOrCreate(
                    [
                        'sikayet_hatirlatma_id' => $hatirlatma->id,
                        'user_id' => $alici['user']->id
                    ],
                    ['bildirim_rolu' => $alici['rol']]
                );

                // Bildirim (Zil + Mail)
                $alici['user']->notify(new SikayetHatirlatmaBildirimi($hatirlatma, $alici['rol']));
            }

            // 5. Tarihleri Güncelle
            $cooldownSaat = (float) Setting::get('hatirlatma_cooldown_saat', 24);
            $hatirlatma->update([
                'son_hatirlatma_tarihi' => now(),
                'sonraki_hak_tarihi' => now()->addMinutes($cooldownSaat * 60),
            ]);

            // 6. Eğer açıklama varsa yorum olarak ekle
            if (!empty($aciklama)) {
                $this->yorumEkle($hatirlatma, $aciklama);
            }

            // 6. Log Kaydı
            $isProject = !empty($sikayet->iaa_id);
            $logAciklama = $isProject 
                ? 'Müşteri temsilcisi tarafından ilgili birimlere hatırlatma bildirimi gönderildi.'
                : 'Şikayet henüz projeye dönüşmediği için Müşteri Şikayeti Kurulu üyelerine atama hatırlatması gönderildi.';

            MusteriSikayetiLog::create([
                'musteri_sikayeti_id' => $sikayet->id,
                'user_id' => $user->id,
                'eylem' => 'Müşteri Hatırlatıyor',
                'aciklama' => $logAciklama,
            ]);

            return $hatirlatma;
        });
    }

    /**
     * Hatırlatmaya yorum ekler.
     */
    public function yorumEkle(SikayetHatirlatma $hatirlatma, string $yorumMetni)
    {
        return DB::transaction(function () use ($hatirlatma, $yorumMetni) {
            $user = Auth::user();

            $yorum = SikayetHatirlatmaYorumu::create([
                'sikayet_hatirlatma_id' => $hatirlatma->id,
                'hatirlatma_numarasi' => $hatirlatma->hatirlatma_sayisi,
                'user_id' => $user->id,
                'yorum' => $yorumMetni,
            ]);

            // Durum Güncellemesi: Rol Tabanlı Pinpon Mantığı
            // Müşteri temsilcisi mesaj attıysa KÖKSAN'dan cevap bekleniyor (Bekleyen)
            if ($user->customer_id || !$user->is_personnel) {
                $hatirlatma->update(['durum' => 'bilgi_girisi_bekleniyor']);
            } else {
                // KÖKSAN personeli mesaj attıysa müşteriye yanıt verildi (Yanıtlanan)
                $hatirlatma->update(['durum' => 'bilgi_girildi']);
            }

            // Bildirimler
            // 1. Müşteri Temsilcisine (Gönderen)
            if ($hatirlatma->gonderen_user_id !== $user->id) {
                $hatirlatma->gonderen->notify(new SikayetHatirlatmaYaniti($yorum));
            }

            // 2. Diğer Bildirilenlere
            $bildirilenUserIds = $hatirlatma->bildirilenler()->pluck('user_id')->toArray();
            $digerAlicilar = User::whereIn('id', $bildirilenUserIds)
                ->where('id', '!=', $user->id)
                ->where('id', '!=', $hatirlatma->gonderen_user_id)
                ->get();

            foreach ($digerAlicilar as $alici) {
                $alici->notify(new SikayetHatirlatmaYaniti($yorum));
            }

            return $yorum;
        });
    }

    /**
     * Müşterinin ikna olduğunu işaretler.
     */
    public function iknaOldu(SikayetHatirlatma $hatirlatma)
    {
        return DB::transaction(function () use ($hatirlatma) {
            $hatirlatma->update(['durum' => 'musteri_ikna_oldu']);

            // Bildirilenlere haber ver
            $bildirilenUserIds = $hatirlatma->bildirilenler()->pluck('user_id')->toArray();
            $alicilar = User::whereIn('id', $bildirilenUserIds)->get();

            foreach ($alicilar as $alici) {
                $alici->notify(new SikayetHatirlatmaIknaOldu($hatirlatma));
            }

            // Log Kaydı
            MusteriSikayetiLog::create([
                'musteri_sikayeti_id' => $hatirlatma->musteri_sikayeti_id,
                'user_id' => Auth::id(),
                'eylem' => 'Müşteri İkna Oldu',
                'aciklama' => 'Müşteri hatırlatma süreci sonucunda ikna olduğu onaylandı.',
            ]);

            return $hatirlatma;
        });
    }

    /**
     * Şikayet bazlı alıcıları ayarlar tablosuna, takım ve rollere göre bulur.
     */
    private function getHatirlatmaAlicilari(MusteriSikayeti $sikayet)
    {
        $alicilar = [];
        $bolum = $sikayet->bolum;
        $aktifUser = Auth::user();

        // 1. Şikayeti Giren Personel
        if (Setting::get('hatirlatma_sikayeti_giren_bildir', 1)) {
            $olusturan = $sikayet->olusturanKurulUyesi; // Personel girişi ise
            if ($olusturan && $olusturan->is_personnel) {
                $alicilar[] = ['user' => $olusturan, 'rol' => 'Şikayeti Giren'];
            }
        }

        // 2. Çözüm Takımı Lideri (Doğrudan Atanan Takım Üzerinden)
        if (Setting::get('hatirlatma_cozum_lideri_bildir', 1)) {
            if ($sikayet->cozumTakimi && $sikayet->cozumTakimi->lider) {
                $alicilar[] = ['user' => $sikayet->cozumTakimi->lider, 'rol' => 'Çözüm Takımı Lideri'];
            } elseif ($bolum) {
                // Fallback
                $lider = User::role('Müşteri Şikayeti Çözüm Lideri')->where('bolum_id', $bolum->id)->first();
                if ($lider) $alicilar[] = ['user' => $lider, 'rol' => 'Çözüm Takımı Lideri'];
            }
        }

        // 3. Bölüm Kalite Yöneticisi (Şikayet Kategorisi Üzerinden)
        if (Setting::get('hatirlatma_kalite_yoneticisi_bildir', 1)) {
            if ($sikayet->sikayetKategori && $sikayet->sikayetKategori->yoneticiler->count() > 0) {
                foreach ($sikayet->sikayetKategori->yoneticiler as $kaliteci) {
                    $alicilar[] = ['user' => $kaliteci, 'rol' => 'Kalite Yöneticisi'];
                }
            } elseif ($bolum) {
                // Fallback: Eski role-based atamalar için
                $kaliteci = User::role('Bölüm Kalite Yöneticisi')->where('bolum_id', $bolum->id)->first();
                if ($kaliteci) $alicilar[] = ['user' => $kaliteci, 'rol' => 'Kalite Yöneticisi'];
            }
        }

        // 4. Bölüm Lideri (Müdür)
        if ($bolum && Setting::get('hatirlatma_bolum_lideri_bildir', 0)) {
            $mudur = User::role('Bölüm Lideri')->where('bolum_id', $bolum->id)->first();
            if ($mudur) $alicilar[] = ['user' => $mudur, 'rol' => 'Bölüm Lideri'];
        }

        // 5. Direktör
        if ($bolum && Setting::get('hatirlatma_direktor_bildir', 0)) {
            if ($bolum->director_id) {
                $direktor = User::find($bolum->director_id);
                if ($direktor) $alicilar[] = ['user' => $direktor, 'rol' => 'Direktör'];
            }
        }

        // 6. Yönetim
        if (Setting::get('hatirlatma_yonetim_bildir', 0)) {
            $yonetim = User::role('Yonetim')->get();
            foreach ($yonetim as $y) {
                $alicilar[] = ['user' => $y, 'rol' => 'Yönetim'];
            }
        }

        // 7. Firmanın Diğer Müşteri Temsilcileri
        if ($sikayet->customer_id) {
            $digerTemsilciler = User::whereHas('customers', function ($q) use ($sikayet) {
                $q->where('customers.id', $sikayet->customer_id);
            })->orWhere('customer_id', $sikayet->customer_id)->get();

            foreach ($digerTemsilciler as $temsilci) {
                // Gönderen kişinin kendisi hariç
                if ($aktifUser && $temsilci->id === $aktifUser->id) continue;
                $alicilar[] = ['user' => $temsilci, 'rol' => 'Müşteri Temsilcisi'];
            }
        }

        // 8. Müşteri Şikayeti Kurulu Üyeleri (ATAMA YETKİLİLERİ)
        $kurulUyeleri = User::role('Müşteri Şikayeti Kurulu')->get();
        foreach ($kurulUyeleri as $kurul) {
            $rolAdi = $sikayet->iaa_id ? 'Kurul Üyesi' : 'Kurul Üyesi (Atama Sorumlusu)';
            $alicilar[] = ['user' => $kurul, 'rol' => $rolAdi];
        }

        // Tekilleştir (Aynı kişi farklı rollerde olabilir)
        $uniqueAlicilar = [];
        $seenIds = [];
        foreach ($alicilar as $a) {
            if (!in_array($a['user']->id, $seenIds)) {
                // Eğer proje değilse, Çözüm Lideri vb. henüz atanmadığı için 
                // Kurul üyeleri dışındaki (varsa) kişileri süzmek isteyebiliriz ama 
                // güvenlik için tüm mantığı koruyup rol ismini güncellemek yeterli.
                $uniqueAlicilar[] = $a;
                $seenIds[] = $a['user']->id;
            }
        }

        return $uniqueAlicilar;
    }
}
