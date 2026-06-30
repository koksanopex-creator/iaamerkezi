<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\Iaa;
use App\Models\MusteriSikayeti;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KullaniciPuanService
{
    /**
     * Tek bir kullanıcının toplam puanını hesaplar.
     * (Sinan'ın 50 Puanı ve Gülnur'un Çift Puan Sorunu Düzeltildi)
     */
    public function calculateTotalScore(User $user)
    {
        return $this->calculateScoreInRange($user);
    }

    /**
     * Kullanıcının toplam puan önbelleğini (cache) günceller.
     */
    public function syncUserCache(User $user)
    {
        $realScore = $this->calculateTotalScore($user);
        $user->update(['toplam_puan' => $realScore]);
        return $realScore;
    }

    /**
     * Belirli bir tarih aralığında kullanıcının puanını hesaplar.
     */
    public function calculateScoreInRange(User $user, $startDate = null, $endDate = null)
    {
        // === MÜŞTERİ VEYA AYRILAN PERSONEL MUAFİYETİ ===
        if (!$user->is_personnel) {
            return 0;
        }
        // İşten ayrılma tarihi (Effective Departure Date) - En erken olanı al (soft delete veya resmi fesih)
        $termDate = $user->termination_date ? Carbon::parse($user->termination_date)->endOfDay() : null;
        $deletedAt = $user->deleted_at ? Carbon::parse($user->deleted_at)->endOfDay() : null;

        if ($termDate && $deletedAt) {
            $departureDate = $termDate->lt($deletedAt) ? $termDate : $deletedAt;
        } else {
            $departureDate = $deletedAt ?? $termDate;
        }

        $toplamPuan = 0;

        // Tarih filtresi fonksiyonu (Reusable)
        $applyDateFilter = function ($query, $column = 'updated_at') use ($startDate, $endDate) {
            if ($startDate) {
                $query->whereDate($column, '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate($column, '<=', $endDate);
            }
        };

        // =================================================================
        // 1. PROJELERDEN GELEN PUANLAR (LİDER + SQUAD + PASİF ÜYE)
        // =================================================================

        $takimIdleri = $user->takimlar()->pluck('takim_id');
        $squadProjeIds = $user->gorevliOlduguProjeler()
            ->wherePivot('durum', 'onaylandi')
            ->where(function($q) use ($departureDate) {
                if ($departureDate) {
                    // İşten ayrılan kişi, sadece ayrılma tarihinden ÖNCE tamamlanmış projelerden puan alabilir.
                    $q->where(DB::raw('COALESCE(iaas.tamamlanma_tarihi, iaas.onaylanma_tarihi, iaas.created_at)'), '<=', Carbon::parse($departureDate)->endOfDay());
                }
            })
            ->pluck('iaas.id');

        $projeQuery = Iaa::where('durum', 'Tamamlandı')
            ->where('puan', '>', 0)
            ->where(function ($query) use ($takimIdleri, $user, $squadProjeIds, $departureDate) {
                // 1. LİDER (SABİTLENMİŞ)
        // Liderin puan alması için projenin tamamlayan lideri olması şart.
                $query->where('tamamlayan_lider_id', $user->id);
                
                if ($departureDate) {
                    $query->where(\Illuminate\Support\Facades\DB::raw('COALESCE(iaas.tamamlanma_tarihi, iaas.onaylanma_tarihi, iaas.created_at)'), '<=', Carbon::parse($departureDate)->endOfDay());
                }

                // 2. SQUAD / DONDURULMUŞ ÜYELER
                $query->orWhereIn('id', $squadProjeIds);

                // 3. PASİF TAKIM PUANI (Sadece Öneriler)
                $query->orWhere(function ($sub) use ($takimIdleri, $user, $departureDate) {
                    $sub->whereIn('atanan_takim_id', $takimIdleri)
                        ->sadeceOneriler();
                    
                    if ($departureDate) {
                        $sub->where(\Illuminate\Support\Facades\DB::raw('COALESCE(iaas.tamamlanma_tarihi, iaas.onaylanma_tarihi, iaas.created_at)'), '<=', Carbon::parse($departureDate)->endOfDay());
                    }
                    
                    // ERHAN CESUR KURALI: 
                    // Eğer kullanıcı artık takım lideri değilse (veya pasifse), 
                    // o takıma atanmış projelerden (EĞER SQUAD'DA DEĞİLSE) pasif puan almamalıdır.
                    // Mevcut yapı zaten takımları $user->takimlar() üzerinden aldığı için 
                    // eğer takımdan çıkarıldıysa puanı kesilecektir.
                });
            });

        $applyDateFilter($projeQuery, \Illuminate\Support\Facades\DB::raw('COALESCE(iaas.onaya_gonderilme_tarihi, iaas.onaylanma_tarihi, iaas.created_at)'));
        $toplamPuan += $projeQuery->sum('puan');

        // =================================================================
        // 2. ŞİKAYET OLUŞTURMA PUANI (SADECE GİRİŞ PUANI)
        // =================================================================

        $sikayetGirisPuani = Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0;

        if ($sikayetGirisPuani > 0 && !$user->hasRole(['Superadmin', 'Müşteri'])) {
            $sikayetQuery = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $user->id)
                ->where('musteri_durum', '!=', 'Talep')
                ->whereDoesntHave('iaaProjesi', function ($q) {
                    $q->whereIn('durum', [
                        'talep_olarak_kapatildi',
                        'hatali_bildirim_olarak_kapatildi'
                    ]);
                });

            $applyDateFilter($sikayetQuery, 'created_at');
            $toplamPuan += ($sikayetQuery->count() * $sikayetGirisPuani);
        }

        // =================================================================
        // 3. ONAYLANAN ÖNERİLER
        // =================================================================
        $iaaOneriPuani = Setting::where('key', 'iaa_oneri_puani')->value('value') ?? 0;

        if ($iaaOneriPuani > 0) {
            $oneriQuery = Iaa::where('gonderen_user_id', $user->id)
                ->whereNotIn('durum', ['Taslak', 'Onay Bekliyor', 'Reddedildi']);

            $applyDateFilter($oneriQuery, 'created_at');
            $toplamPuan += ($oneriQuery->count() * $iaaOneriPuani);
        }

        // =================================================================
        // 4. DİSİPLİN CEZALARI
        // =================================================================
        $cezalarQuery = \App\Models\DisciplinaryCase::where('user_id', $user->id)
            ->where('durum', 'Karar Verildi')
            ->where('final_karar', '!=', 'Savunma Kabul Edildi (Ceza Yok)');

        $applyDateFilter($cezalarQuery, 'updated_at');
        $toplamPuan -= $cezalarQuery->sum('hesaplanan_puan');

        return $toplamPuan;
    }

    /**
     * Tüm kullanıcılar için tarih bazlı sıralama döndürür.
     */
    public function getRankings($startDate = null, $endDate = null, $bolumId = null, $limit = null, $excludeRoles = [])
    {
        $query = User::withTrashed()
            ->where('is_personnel', true)
            ->where('onaylandi_mi', true);

        if ($bolumId) {
            $query->where('bolum_id', $bolumId);
        }

        if (!empty($excludeRoles)) {
            $query->whereDoesntHave('roles', function ($q) use ($excludeRoles) {
                $q->whereIn('name', $excludeRoles);
            });
        }

        // PERFORMANS OPTİMİZASYONU: Tarih filtresi yoksa cached toplam_puan'ı kullan
        if (empty($startDate) && empty($endDate)) {
            $query->orderBy('toplam_puan', 'desc');
            
            if ($limit) {
                $query->limit($limit);
            }
            
            $users = $query->get();
            
            foreach ($users as $user) {
                $user->period_puan = $user->toplam_puan;
            }
            
            return $users;
        }

        $users = $query->get();

        foreach ($users as $user) {
            $user->period_puan = $this->calculateScoreInRange($user, $startDate, $endDate);
        }

        $sorted = $users->sortByDesc('period_puan')->values();

        if ($limit) {
            return $sorted->take($limit);
        }

        return $sorted;
    }


    /**
     * Kullanıcının detaylı puan verilerini ve geçmişini döndürür.
     * Tarih filtresi eklendi.
     */
    public function getDetailedScoreData(User $user, $startDate = null, $endDate = null)
    {
        // === MÜŞTERİ MUAFİYETİ ===
        if (!$user->is_personnel) {
            return [
                'tum_projeler' => collect(),
                'sikayet_girisleri' => collect(),
                'sikayet_giris_puani' => 0,
                'oneriler' => collect(),
                'oneri_puani' => 0,
                'cezalar' => collect(),
                'toplam_puan' => 0
            ];
        }

        // İşten ayrılma tarihi (Effective Departure Date) - En erken olanı al (soft delete veya resmi fesih)
        $termDate = $user->termination_date ? \Carbon\Carbon::parse($user->termination_date)->endOfDay() : null;
        $deletedAt = $user->deleted_at;

        if ($termDate && $deletedAt) {
            $departureDate = $termDate->lt($deletedAt) ? $termDate : $deletedAt;
        } else {
            $departureDate = $deletedAt ?? $termDate;
        }

        // Tarih filtresi fonksiyonu (Reusable)
        $applyDateFilter = function ($query, $column = 'updated_at') use ($startDate, $endDate) {
            if ($startDate) {
                $query->whereDate($column, '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate($column, '<=', $endDate);
            }
        };

        // 1. PROJELER (Kazanılan Puanlar)
        // A. Lider Olduğu Projeler
        $liderProjelerQuery = Iaa::where('tamamlayan_lider_id', $user->id)
            ->where('durum', 'Tamamlandı')
            ->where('puan', '>', 0)
            ->where(function($q) use ($departureDate) {
                if ($departureDate) {
                    $q->where(DB::raw('COALESCE(iaas.tamamlanma_tarihi, iaas.onaylanma_tarihi, iaas.created_at)'), '<=', Carbon::parse($departureDate)->endOfDay());
                }
            });

        $applyDateFilter($liderProjelerQuery, \Illuminate\Support\Facades\DB::raw('COALESCE(iaas.onaya_gonderilme_tarihi, iaas.onaylanma_tarihi, iaas.created_at)')); 

        $liderProjeler = $liderProjelerQuery->get()->map(function ($p) {
            $p->kazanma_sebebi = 'Takım Lideri';
            return $p;
        });

        // B. Squad (Görevli) Olduğu Projeler
        $squadProjelerQuery = $user->gorevliOlduguProjeler()
            ->wherePivot('durum', 'onaylandi') // Sadece onaylayanlar
            ->where('iaas.durum', 'Tamamlandı')
            ->where('iaas.puan', '>', 0)
            ->where(function($q) use ($departureDate) {
                if ($departureDate) {
                    $q->where(DB::raw('COALESCE(iaas.tamamlanma_tarihi, iaas.onaylanma_tarihi, iaas.created_at)'), '<=', Carbon::parse($departureDate)->endOfDay());
                }
            });

        $applyDateFilter($squadProjelerQuery, \Illuminate\Support\Facades\DB::raw('COALESCE(iaas.onaya_gonderilme_tarihi, iaas.onaylanma_tarihi, iaas.created_at)'));

        $squadProjeler = $squadProjelerQuery->get()->map(function ($p) {
            $p->kazanma_sebebi = 'Proje Ekibi (Squad)';
            return $p;
        });

        // C. Pasif Üye Olduğu Projeler (Şikayet Olmayanlar)
        $pasifProjeIds = $user->takimlar()->pluck('takim_id');
        $pasifProjelerQuery = Iaa::whereIn('atanan_takim_id', $pasifProjeIds)
            ->sadeceOneriler() // Şikayetlerden pasif puan alınmaz
            ->where('durum', 'Tamamlandı')
            ->where('puan', '>', 0)
            ->where(function($q) use ($departureDate) {
                if ($departureDate) {
                    $q->where(DB::raw('COALESCE(iaas.tamamlanma_tarihi, iaas.onaylanma_tarihi, iaas.created_at)'), '<=', Carbon::parse($departureDate)->endOfDay());
                }
            });

        $applyDateFilter($pasifProjelerQuery, \Illuminate\Support\Facades\DB::raw('COALESCE(iaas.onaya_gonderilme_tarihi, iaas.onaylanma_tarihi, iaas.created_at)'));

        $pasifProjeler = $pasifProjelerQuery->get()->map(function ($p) {
            $p->kazanma_sebebi = 'Takım Üyesi'; // "Pasif" kelimesi kaldırıldı
            return $p;
        });

        // Hepsini birleştir ve ID'ye göre unique yap
        $tumProjeler = $liderProjeler->merge($squadProjeler)->merge($pasifProjeler)->unique('id')->sortByDesc(function ($p) {
            return $p->onaya_gonderilme_tarihi ?? $p->onaylanma_tarihi ?? $p->created_at;
        });

        // 2. ŞİKAYET GİRİŞLERİ
        $sikayetGirisleriQuery = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $user->id)
            ->whereHas('olusturanKurulUyesi', function($q) {
                $q->whereDoesntHave('roles', function($rq) {
                    $rq->whereIn('name', ['Superadmin', 'Müşteri']);
                });
            })
            ->where('musteri_durum', '!=', 'Talep')
            ->whereDoesntHave('iaaProjesi', function ($q) {
                $q->whereIn('durum', [
                    'talep_olarak_kapatildi',
                    'hatali_bildirim_olarak_kapatildi'
                ]);
            });

        $applyDateFilter($sikayetGirisleriQuery, 'created_at'); // Oluşturulma tarihine göre

        $sikayetGirisleri = $sikayetGirisleriQuery->get();

        $sikayetGirisPuani = Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0;

        // 3. ÖNERİLER
        $onerilerQuery = Iaa::where('gonderen_user_id', $user->id)
            ->whereNotIn('durum', ['Taslak', 'Onay Bekliyor', 'Reddedildi']);

        $applyDateFilter($onerilerQuery, 'created_at'); // Öneri tarihine göre

        $oneriler = $onerilerQuery->get();

        $oneriPuani = Setting::where('key', 'iaa_oneri_puani')->value('value') ?? 0;

        // 4. DİSİPLİN CEZALARI
        $cezalarQuery = \App\Models\DisciplinaryCase::where('user_id', $user->id)
            ->where('durum', 'Karar Verildi')
            ->where('final_karar', '!=', 'Savunma Kabul Edildi (Ceza Yok)');

        $applyDateFilter($cezalarQuery, 'updated_at'); // Karar tarihine göre

        $cezalar = $cezalarQuery->get();

        // 5. TOPLAM PUAN HESABI (Filtreli veya Genel)
        // Eğer tarih filtresi varsa, yukarıdaki filtrelenmiş verilerden topla.
        // Yoksa genel toplamı hesapla.

        $hesaplananToplam = 0;

        // Projelerden
        foreach ($tumProjeler as $p) {
            $hesaplananToplam += $p->puan;
        }

        // Şikayetlerden
        $hesaplananToplam += ($sikayetGirisleri->count() * $sikayetGirisPuani);

        // Önerilerden
        $hesaplananToplam += ($oneriler->count() * $oneriPuani);

        // Cezalar (Düş)
        $hesaplananToplam -= $cezalar->sum('hesaplanan_puan');

        // NOT: Filtreli görünümde (örn: bu hafta) sadece ceza varsa, toplamın EKSİ çıkması isteniyor.
        // Bu yüzden alttaki kontrolü kaldırıyoruz.
        // if ($hesaplananToplam < 0)
        //    $hesaplananToplam = 0;

        return [
            'tum_projeler' => $tumProjeler,
            'sikayet_girisleri' => $sikayetGirisleri,
            'sikayet_giris_puani' => $sikayetGirisPuani,
            'oneriler' => $oneriler,
            'oneri_puani' => $oneriPuani,
            'cezalar' => $cezalar,
            'toplam_puan' => $hesaplananToplam // Filtreli toplam
        ];
    }


    public function getTeamDetailedScoreData(\App\Models\Takim $takim)
    {
        // 1. Takım Üyeleri
        $uyeler = $takim->uyeler;

        // 2. Tamamlanan Projeler (Bu takıma atanmış ve bitmiş)
        $tamamlananProjeler = Iaa::where('atanan_takim_id', $takim->id)
            ->where('durum', 'Tamamlandı')
            ->where('puan', '>', 0)
            ->sadeceOneriler() // SADECE SAF İAA PROJELERİ GÖZÜKSÜN (Şikayetler şikayet listesinde)
            ->orderByRaw('COALESCE(iaas.onaya_gonderilme_tarihi, iaas.onaylanma_tarihi, iaas.created_at) DESC')
            ->get();

        // 3. Şikayet Çözümleri (Eğer Şikayet Takımıysa)
        $cozulenSikayetler = [];
        if ($takim->tur == 'sikayet') {
            $cozulenSikayetler = MusteriSikayeti::where('atanan_cozum_takimi_id', $takim->id)
                ->where('musteri_durum', 'Kapatıldı')
                ->orderByDesc('updated_at')
                ->with('iaaProjesi') // Puanı çekmek için gerekli
                ->get();
        }

        // 4. İşlemde Olanlar (Aktif Görevler)
        $islemdekiProjeler = Iaa::where('atanan_takim_id', $takim->id)
            ->whereNotIn('durum', ['Tamamlandı', 'İptal Edildi', 'Reddedildi'])
            ->sadeceOneriler()
            ->orderBy('updated_at', 'DESC')
            ->get();

        $islemdekiSikayetler = MusteriSikayeti::where('atanan_cozum_takimi_id', $takim->id)
            ->whereNotIn('musteri_durum', ['Kapatıldı', 'Çözümlendi', 'Tamamlandı', 'İptal Edildi', 'Reddedildi'])
            ->orderBy('updated_at', 'DESC')
            ->get();

        // Puan Hesaplama (DB Score Should Match Calculated)
        $hesaplananPuan = $tamamlananProjeler->sum('puan');

        // Şikayetlerden gelen puanları ekle (Proje puanı varsa onu, yoksa şikayet puanını)
        if (!empty($cozulenSikayetler)) {
            foreach ($cozulenSikayetler as $sikayet) {
                if ($sikayet->iaa_id && $sikayet->iaaProjesi) {
                    $hesaplananPuan += $sikayet->iaaProjesi->puan;
                } else {
                    $hesaplananPuan += $sikayet->kazanilan_puan;
                }
            }
        }

        return [
            'uyeler' => $uyeler,
            'tamamlananProjeler' => $tamamlananProjeler,
            'cozulenSikayetler' => $cozulenSikayetler,
            'islemdekiProjeler' => $islemdekiProjeler,
            'islemdekiSikayetler' => $islemdekiSikayetler,
            'hesaplananPuan' => $hesaplananPuan
        ];
    }
}
