<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\Iaa;
use App\Models\Bolum;
use App\Models\Takim;
use App\Models\MusteriSikayeti;
use App\Models\ProjeYorumu;
use App\Models\ProfileComment;
use App\Models\DisciplinaryCase;
use App\Models\Customer;
use App\Models\ArabuluculukCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class SuperAdminDashboardService
{
    /**
     * Superadmin Dashboard genel istatistiklerini döndürür.
     *
     * @return array
     */
    public function getStats($bolumId = null)
    {
        // 1. GENEL İSTATİSTİKLER
        $stats = [
            'toplam_kullanici' => User::when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),
            'onay_bekleyen_kullanici' => User::where('onaylandi_mi', false)->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),
            'son_kullanicilar' => User::when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->latest()->take(10)->get(),

            // SAF IAA İSTATİSTİKLERİ
            'toplam_iaa' => Iaa::sadeceOneriler()->where('durum', 'Tamamlandı')->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),
            'yeni_iaa_onerileri' => Iaa::sadeceOneriler()->whereNull('atanan_takim_id')->where('durum', 'Onay Bekliyor')->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),
            'onay_bekleyen_tamamlanmis_iaa' => Iaa::sadeceOneriler()->whereNotNull('atanan_takim_id')->whereIn('durum', [
                'Bölüm Onayı Bekliyor',
                'Direktör Onayı Bekliyor',
                'Yönetici Onayı Bekliyor'
            ])->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),

            'atama_bekleyen_iaa' => Iaa::sadeceOneriler()->where('durum', 'Havuzda')->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),
            
            'bekleyen_iaa_atama_talepleri' => DB::table('iaa_talepleri')
                ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
                ->where('iaa_talepleri.durum', 'beklemede')
                ->where('iaas.durum', 'Havuzda')
                ->when($bolumId, fn($q) => $q->where('iaas.bolum_id', $bolumId))
                ->distinct('iaa_talepleri.iaa_id')
                ->count('iaa_talepleri.iaa_id'),
            
            'toplam_iaa_talep_sayisi' => DB::table('iaa_talepleri')
                ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
                ->where('iaa_talepleri.durum', 'beklemede')
                ->where('iaas.durum', 'Havuzda')
                ->when($bolumId, fn($q) => $q->where('iaas.bolum_id', $bolumId))
                ->count(),

            'son_iaalar' => Iaa::sadeceOneriler()->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->orderBy('created_at', 'desc')->take(3)->get(),

            'toplam_bolum' => Bolum::count(),
            'son_bolumler' => Bolum::latest()->take(10)->get(),

            'toplam_takim' => Takim::when($bolumId, fn($q) => $q->whereHas('lider', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'son_takimlar' => Takim::with('lider')->withCount('uyeler')->when($bolumId, fn($q) => $q->whereHas('lider', fn($q2) => $q2->where('bolum_id', $bolumId)))->latest()->take(10)->get(),

            'toplam_sikayet' => MusteriSikayeti::whereNull('deleted_at')->when($bolumId, fn($q) => $q->whereHas('sikayetKategori', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'yeni_sikayet' => MusteriSikayeti::whereNull('deleted_at')->where('musteri_durum', 'Yeni')->when($bolumId, fn($q) => $q->whereHas('sikayetKategori', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'islemde_sikayet' => MusteriSikayeti::whereNull('deleted_at')->where('musteri_durum', 'İşlemde')->when($bolumId, fn($q) => $q->whereHas('sikayetKategori', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'onay_bekleyen_sikayet' => MusteriSikayeti::whereNull('deleted_at')->whereHas('iaaProjesi', function ($q) {
                $q->whereIn('durum', ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor']);
            })->when($bolumId, fn($q) => $q->whereHas('sikayetKategori', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'son_sikayetler' => MusteriSikayeti::whereNull('deleted_at')->with('customer')->when($bolumId, fn($q) => $q->whereHas('sikayetKategori', fn($q2) => $q2->where('bolum_id', $bolumId)))->latest()->take(3)->get(),

            // YENİ EKLEMELER
            'toplam_musteri' => Customer::count(), // Müşteri her zaman global
            'son_musteriler_listesi' => Customer::latest()->take(3)->get(),
            'toplam_disiplin' => DisciplinaryCase::when($bolumId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'aktif_disiplin' => DisciplinaryCase::whereNotIn('durum', ['Karar Verildi', 'İptal Edildi'])->when($bolumId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'toplam_arabuluculuk' => ArabuluculukCase::when($bolumId, fn($q) => $q->whereHas('calisan', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'aktif_arabuluculuk' => ArabuluculukCase::where('status', '!=', 'kapatildi')->when($bolumId, fn($q) => $q->whereHas('calisan', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
        ];

        return $stats;
    }


    /**
     * Tüm personellerin bekleyen işlerini gösterir (Liste Tasarımı)
     */
    public function getAllPendingWorks(array $filters = [])
    {
        $tur = $filters['tur'] ?? null;
        $bolum = $filters['bolum'] ?? null;
        $durum = $filters['durum'] ?? null;

        $bekleyenIsler = collect();
        $user = auth()->user();
        $allowedBolumIds = $user ? $user->getAllowedBolumIds() : [];
        $isGlobal = ($allowedBolumIds === '*');

        // Global yetkileri modüllere göre detaylandıralım (Örn: Müşteri Şikayeti Kurulu Şikayetleri global görür, İAA'yı göremez)
        $isIaaGlobal = $isGlobal && $user->hasAnyRole(['Superadmin', 'Yonetim']);
        $isSikayetGlobal = $isGlobal;
        $isZiyaretGlobal = $isGlobal && $user->hasAnyRole(['Superadmin', 'Yonetim']);
        $isDisiplinGlobal = $isGlobal && $user->hasAnyRole(['Superadmin', 'Yonetim']);
        $isArabuluculukGlobal = $isGlobal && $user->hasAnyRole(['Superadmin', 'Yonetim']);
        $isKullaniciGlobal = $isGlobal && $user->hasAnyRole(['Superadmin', 'Yonetim']);

        // 1. İAA PROJELERİ
        if (!$tur || $tur == 'İAA') {
            $iaas = Iaa::sadeceOneriler()->with(['bolum', 'gonderen', 'atananTakim.lider'])
                ->whereNotIn('durum', ['Tamamlandı', 'Reddedildi', 'İptal Edildi', 'Taslak', 'talep_olarak_kapatildi']);

            if (!$isIaaGlobal) {
                $iaas->where(function($q) use ($allowedBolumIds, $user) {
                    if (is_array($allowedBolumIds) && !empty($allowedBolumIds)) {
                        $q->whereIn('bolum_id', $allowedBolumIds);
                    }
                    $q->orWhere('gonderen_user_id', $user->id)
                      ->orWhereHas('atananTakim', function($t) use ($user) {
                          $t->where('lider_user_id', $user->id)
                            ->orWhereHas('uyeler', fn($u) => $u->where('users.id', $user->id));
                      })
                      ->orWhereHas('projeEkibi', function($pe) use ($user) {
                          $pe->where('users.id', $user->id);
                      })
                      ->orWhereHas('stepAssignments', function($sa) use ($user) {
                          $sa->where('user_id', $user->id);
                      });
                });
            }

            if ($bolum) {
                $iaas->whereHas('bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
            }

            $iaas->get()->each(function ($item) use ($bekleyenIsler) {
                // Sorumlu Kişi Mantığı - Spesifik İsim
                $sorumlu = 'Atanmamış';
                if (in_array($item->durum, ['Onay Bekliyor', 'talep_onayi_bekliyor_superadmin', 'hatali_bildirim_onayi_bekliyor_superadmin'])) {
                    $sa = User::role('Superadmin')->first();
                    $sorumlu = $sa ? $sa->name : 'Sistem Yöneticisi';
                } elseif (in_array($item->durum, ['Bölüm Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_kalite'])) {
                    $kaliteci = User::role('Bölüm Kalite Yöneticisi')
                        ->whereHas('yonettigiSikayetKategorileri', function ($q) use ($item) {
                            $q->whereHas('bolum', fn($bq) => $bq->where('id', $item->bolum_id));
                        })->first();
                    $sorumlu = $kaliteci ? $kaliteci->name : 'Bölüm Kalite Yöneticisi';
                } elseif ($item->durum == 'Yönetici Onayı Bekliyor') {
                    $yonetici = User::role(['Superadmin', 'Yonetim'])->first();
                    $sorumlu = $yonetici ? $yonetici->name : 'Yönetim / Superadmin';
                } elseif (in_array($item->durum, ['Direktör Onayı Bekliyor', 'talep_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_direktor'])) {
                    $direktor = $item->bolum && $item->bolum->director_id ? User::find($item->bolum->director_id) : null;
                    $sorumlu = $direktor ? $direktor->name : 'Direktör';
                } else {
                    $sorumlu = $item->atananTakim && $item->atananTakim->lider 
                        ? $item->atananTakim->lider->name 
                        : ($item->gonderen ? $item->gonderen->name : 'Atanmamış');
                }

                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'İAA',
                    'konu' => $item->baslik,
                    'personel' => $sorumlu,
                    'bolum' => $item->bolum ? $item->bolum->ad : '-',
                    'durum' => $item->durum,
                    'status_html' => $item->durum_etiketi,
                    'gun' => $item->created_at->diffInDays(now()),
                    'oncelik' => $item->oncelik ?? 'Normal',
                    'link' => route('proje.workspace.show', $item->id)
                ]);
            });
        }

        // 2. MÜŞTERİ ŞİKAYETLERİ
        if (!$tur || $tur == 'Müşteri Şikayeti') {
            $sikayetler = MusteriSikayeti::with(['sikayetKategori.bolum', 'cozumTakimi.lider', 'iaaProjesi'])
                ->where('musteri_durum', '!=', 'Kapatıldı');

            if (!$isSikayetGlobal) {
                $sikayetler->where(function($q) use ($allowedBolumIds, $user) {
                    if (is_array($allowedBolumIds) && !empty($allowedBolumIds)) {
                        $q->whereHas('sikayetKategori', function($c) use ($allowedBolumIds) {
                            $c->whereIn('bolum_id', $allowedBolumIds);
                        });
                    }
                    $q->orWhere('olusturan_kurul_uyesi_id', $user->id)
                      ->orWhereHas('cozumTakimi', function($t) use ($user) {
                          $t->where('lider_user_id', $user->id)
                            ->orWhereHas('uyeler', fn($u) => $u->where('users.id', $user->id));
                      })
                      ->orWhereHas('iaaProjesi.stepAssignments', function($sa) use ($user) {
                          $sa->where('user_id', $user->id);
                      })
                      ->orWhereHas('iaaProjesi.projeEkibi', function($pe) use ($user) {
                          $pe->where('users.id', $user->id);
                      });
                });
            }

            if ($bolum) {
                $sikayetler->whereHas('sikayetKategori.bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
            }

            $sikayetler->get()->each(function ($item) use ($bekleyenIsler) {
                // Sorumlu Kişi Mantığı (Dinamik)
                $sorumlu = 'Atanmamış';
                $projeDurumu = $item->iaaProjesi ? $item->iaaProjesi->durum : $item->musteri_durum;

                if (in_array($projeDurumu, ['Bölüm Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_kalite'])) {
                    $kaliteci = $item->sikayetKategori ? $item->sikayetKategori->yoneticiler()->first() : null;
                    $sorumlu = $kaliteci ? $kaliteci->name : 'Kalite Yöneticisi';
                } elseif (in_array($projeDurumu, ['Yönetici Onayı Bekliyor', 'talep_onayi_bekliyor_superadmin', 'hatali_bildirim_onayi_bekliyor_superadmin'])) {
                    $yonetici = User::role(['Superadmin', 'Yonetim'])->first();
                    $sorumlu = $yonetici ? $yonetici->name : 'Yönetim / Superadmin';
                } elseif (in_array($projeDurumu, ['Direktör Onayı Bekliyor', 'talep_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_direktor'])) {
                    $direktor = $item->sikayetKategori && $item->sikayetKategori->bolum ? $item->sikayetKategori->bolum->director : null;
                    $sorumlu = $direktor ? $direktor->name : 'Direktör';
                } else {
                    $sorumlu = $item->cozumTakimi && $item->cozumTakimi->lider 
                        ? $item->cozumTakimi->lider->name 
                        : ($item->olusturanKurulUyesi ? $item->olusturanKurulUyesi->name : 'Kurul Üyesi');
                }

                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'Müşteri Şikayeti',
                    'konu' => $item->musteri_sikayet_konusu,
                    'personel' => $sorumlu,
                    'bolum' => $item->sikayetKategori && $item->sikayetKategori->bolum ? $item->sikayetKategori->bolum->ad : '-',
                    'durum' => $projeDurumu,
                    'status_html' => $item->musteri_durum_badge,
                    'gun' => $item->created_at->diffInDays(now()),
                    'oncelik' => $item->oncelik ?? 'Normal',
                    'link' => route('admin.sikayetler.show', $item->id)
                ]);
            });
        }

        // 3. ZİYARET PLANLARI
        if (!$tur || $tur == 'Ziyaret Planı') {
            $ziyaretler = \App\Models\IaaZiyaretPlani::where(function ($q) {
                    $q->where('status', 'Onaylandı')
                      ->orWhereIn('return_date_revision_status', ['Bekliyor', 'Direktör Onayı Bekliyor']);
                })->with(['iaa.bolum', 'iaa.musteriSikayeti.customer', 'planner']);

            if (!$isZiyaretGlobal) {
                $ziyaretler->where(function($q) use ($allowedBolumIds, $user) {
                    if (is_array($allowedBolumIds) && !empty($allowedBolumIds)) {
                        $q->whereHas('iaa', function($iaa) use ($allowedBolumIds) {
                            $iaa->whereIn('bolum_id', $allowedBolumIds);
                        });
                    }
                    $q->orWhere('visitor_id', $user->id)
                      ->orWhereJsonContains('visitors', (string)$user->id)
                      ->orWhereJsonContains('visitors', $user->id);
                });
            }

            if ($bolum) {
                $ziyaretler->whereHas('iaa.bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
            }

            $ziyaretler->get()->each(function ($item) use ($bekleyenIsler) {
                $isRevision = in_array($item->return_date_revision_status, ['Bekliyor', 'Direktör Onayı Bekliyor']);
                $durumText = $isRevision ? 'Tahmini Dönüş Revizyonu Bekliyor' : 'Ziyaret Sonuçlarının Girilmesi Bekleniyor';

                $sorumlu = '-';
                if ($isRevision) {
                    if ($item->return_date_revision_status == 'Direktör Onayı Bekliyor') {
                        $direktor = $item->iaa->bolum && $item->iaa->bolum->director_id ? \App\Models\User::find($item->iaa->bolum->director_id) : null;
                        $sorumlu = $direktor ? $direktor->name : 'Direktör';
                    } else {
                        // 'Bekliyor' durumunda: Bölüm Kalite Yöneticisi onayı bekleniyor
                        $catId = $item->iaa->musteriSikayeti->sikayet_kategorisi_id ?? null;
                        $kaliteci = null;
                        if ($catId) {
                            $kaliteci = \App\Models\User::role('Bölüm Kalite Yöneticisi')
                                ->whereHas('yonettigiSikayetKategorileri', function ($q) use ($catId) {
                                    $q->where('sikayet_kategorileri.id', $catId);
                                })->first();
                        }
                        $sorumlu = $kaliteci ? $kaliteci->name : 'Bölüm Kalite Yöneticisi';
                    }
                } else {
                    $visitorIdsArray = $item->visitors ? (is_string($item->visitors) ? json_decode($item->visitors, true) : (is_array($item->visitors) ? $item->visitors : [])) : [];
                    if (!empty($visitorIdsArray)) {
                        $users = \App\Models\User::whereIn('id', $visitorIdsArray)->pluck('name')->toArray();
                        if (!empty($users)) {
                            $sorumlu = implode(', ', $users);
                        }
                    } elseif (!empty($item->visitor_name)) {
                        $sorumlu = $item->visitor_name;
                    } else {
                        $sorumlu = 'Ziyaretçiler';
                    }
                }

                $statusHtml = '<span class="inline-flex flex-col items-center justify-center px-2 py-1.5 rounded-xl text-[10px] font-black border uppercase tracking-tight text-center leading-tight w-32 whitespace-normal break-words bg-yellow-50 text-yellow-600 border-yellow-300 shadow-sm">⚠ ' . $durumText . '</span>';
                
                $bekleyenIsler->push([
                    'id' => 'ziyaret-' . $item->id,
                    'tur' => 'Ziyaret Planı',
                    'konu' => $item->iaa->musteriSikayeti ? $item->iaa->musteriSikayeti->musteri_sikayet_konusu : $item->iaa->baslik,
                    'personel' => $sorumlu,
                    'bolum' => $item->iaa->bolum ? $item->iaa->bolum->ad : '-',
                    'durum' => $durumText,
                    'status_html' => $statusHtml,
                    'gun' => $item->updated_at->diffInDays(now()),
                    'oncelik' => 'Normal',
                    'link' => route('proje.workspace.show', $item->iaa_id) . '#ziyaret-bilgileri-alani'
                ]);
            });
        }

        if (!$tur || $tur == 'Arabuluculuk') {
            $arabuluculuk = ArabuluculukCase::with(['calisan.bolum'])
                ->where('status', '!=', 'kapatildi');

            if (!$isArabuluculukGlobal) {
                $arabuluculuk->where(function($q) use ($allowedBolumIds, $user) {
                    if (is_array($allowedBolumIds) && !empty($allowedBolumIds)) {
                        $q->whereHas('calisan', function($c) use ($allowedBolumIds) {
                            $c->whereIn('bolum_id', $allowedBolumIds);
                        });
                    }
                    $q->orWhere('calisan_user_id', $user->id)
                      ->orWhere('external_lawyer_id', $user->id)
                      ->orWhere('created_by', $user->id);
                });
            }

            if ($bolum) {
                $arabuluculuk->whereHas('calisan.bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
            }

            $arabuluculuk->get()->each(function ($item) use ($bekleyenIsler) {
                $statusHtml = '<span class="inline-flex flex-col items-center justify-center px-2 py-1.5 rounded-xl text-[10px] font-black border uppercase tracking-tight text-center leading-tight w-32 whitespace-normal break-words bg-blue-50 text-blue-600 border-blue-200 shadow-sm">' . strtoupper(str_replace('_', ' ', $item->status)) . '</span>';
                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'Arabuluculuk',
                    'konu' => ($item->calisan ? $item->calisan->name : 'Bilinmeyen') . ' - Arabuluculuk Dosyası',
                    'personel' => $item->calisan ? $item->calisan->name : '-',
                    'bolum' => $item->calisan && $item->calisan->bolum ? $item->calisan->bolum->ad : '-',
                    'durum' => $item->status,
                    'status_html' => $statusHtml,
                    'gun' => $item->created_at->diffInDays(now()),
                    'oncelik' => 'Yüksek',
                    'link' => route('admin.arabuluculuk.show', $item->id)
                ]);
            });
        }

        // 4. DİSİPLİN DOSYALARI
        if (!$tur || $tur == 'Disiplin') {
            $disiplinler = DisciplinaryCase::with(['user.bolum', 'reporter'])
                ->whereNotIn('durum', ['Karar Verildi', 'İptal Edildi', 'Taslak']);

            if (!$isDisiplinGlobal) {
                $disiplinler->where(function($q) use ($allowedBolumIds, $user) {
                    if (is_array($allowedBolumIds) && !empty($allowedBolumIds)) {
                        $q->whereHas('user', function($u) use ($allowedBolumIds) {
                            $u->whereIn('bolum_id', $allowedBolumIds);
                        });
                    }
                    $q->orWhere('user_id', $user->id)
                      ->orWhere('reporter_id', $user->id);
                });
            }

            if ($bolum) {
                $disiplinler->whereHas('user.bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
            }

            $disiplinler->get()->each(function ($item) use ($bekleyenIsler) {
                // Sorumlu Kişi Mantığı
                $sorumlu = 'Atanmamış';
                if ($item->durum == 'Savunma Bekleniyor') {
                    $sorumlu = $item->user ? $item->user->name : 'İlgili Personel';
                } elseif ($item->durum == 'Yönetici Değerlendirmesi') {
                    $hukuk = User::role('Hukuk Admini')->first();
                    $sorumlu = $hukuk ? $hukuk->name : 'Hukuk Birimi';
                } elseif (in_array($item->durum, ['Kurulda', 'Kurul İncelemesinde'])) {
                    $sorumlu = 'Disiplin Kurulu Üyeleri';
                }

                $statusHtml = '<span class="inline-flex flex-col items-center justify-center px-2 py-1.5 rounded-xl text-[10px] font-black border uppercase tracking-tight text-center leading-tight w-32 whitespace-normal break-words bg-rose-50 text-rose-600 border-rose-200 shadow-sm">' . strtoupper($item->durum) . '</span>';
                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'Disiplin',
                    'konu' => ($item->behavior ? $item->behavior->ad : 'Disiplin Dosyası') . ' (' . ($item->user ? $item->user->name : '-') . ')',
                    'personel' => $sorumlu,
                    'bolum' => $item->user && $item->user->bolum ? $item->user->bolum->ad : '-',
                    'durum' => $item->durum,
                    'status_html' => $statusHtml,
                    'gun' => $item->created_at->diffInDays(now()),
                    'oncelik' => $item->hesaplanan_puan > 50 ? 'Yüksek' : 'Normal',
                    'link' => route('admin.disiplin.show', $item->id)
                ]);
            });
        }

        // 5. KULLANICI ONAYLARI
        if ((!$tur || $tur == 'Kullanıcı Kaydı') && $isKullaniciGlobal) {
            $kullanicilar = User::where('onaylandi_mi', false)->with('bolum');

            if ($bolum) {
                $kullanicilar->whereHas('bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
            }

            $kullanicilar->get()->each(function ($item) use ($bekleyenIsler) {
                $statusHtml = '<span class="inline-flex flex-col items-center justify-center px-2 py-1.5 rounded-xl text-[10px] font-black border uppercase tracking-tight text-center leading-tight w-32 whitespace-normal break-words bg-slate-900 text-white border-slate-700 shadow-sm">ONAY BEKLİYOR</span>';
                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'Kullanıcı Kaydı',
                    'konu' => 'Yeni Kayıt Onayı: ' . $item->name,
                    'personel' => 'Süperadmin',
                    'bolum' => $item->bolum ? $item->bolum->ad : '-',
                    'durum' => 'Onay Bekliyor',
                    'status_html' => $statusHtml,
                    'gun' => $item->created_at->diffInDays(now()),
                    'oncelik' => 'Normal',
                    'link' => route('admin.users.index')
                ]);
            });
        }

        // Dropdown Verileri
        $dropdowns = [
            'bolumler' => Bolum::pluck('ad')->toArray(),
            'turler' => ['İAA', 'Müşteri Şikayeti', 'Ziyaret Planı', 'Arabuluculuk', 'Disiplin', 'Kullanıcı Kaydı'],
            'durumlar' => array_values(array_unique($bekleyenIsler->pluck('durum')->toArray()))
        ];

        // İstatistikler
        $stats = [
            'toplam' => $bekleyenIsler->count(),
            'onay_bekleyen' => $bekleyenIsler->whereIn('durum', ['Onay Bekliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Yeni', 'Savunma Bekleniyor', 'Yönetici Değerlendirmesi', 'Kurulda'])->count(),
            'aktif_islemde' => $bekleyenIsler->whereIn('durum', ['Atandı', 'Devam Ediyor', 'İşlemde', 'Arabulucuda'])->count(),
            'arabuluculuk' => $bekleyenIsler->where('tur', 'Arabuluculuk')->count()
        ];

        if ($durum) {
            $bekleyenIsler = $bekleyenIsler->filter(function ($item) use ($durum) {
                return $item['durum'] == $durum;
            });
        }

        // Sıralama (En çok bekleyenden en az bekleyene)
        $bekleyenIsler = $bekleyenIsler->sortByDesc('gun')->values();

        // Sayfalama (Pagination) - Her sayfada 20 sonuç
        $page = request()->get('page', 1);
        $perPage = 20;
        $paginatedItems = new LengthAwarePaginator(
            $bekleyenIsler->forPage($page, $perPage),
            $bekleyenIsler->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return [
            'bekleyenIsler' => $paginatedItems,
            'stats' => $stats,
            'dropdowns' => $dropdowns
        ];
    }

    /**
     * Superadmin Dashboard için ekstra detay tablolarını döndürür.
     *
     * @return array
     */
    public function getExtraTables()
    {
        $ekstraTablolar = [];

        // 1. TAKIMLAR (AYRIŞTIRILMIŞ)
        $ekstraTablolar['son_iaa_takimlari'] = Takim::where('tur', '!=', 'sikayet')->with('lider')->latest()->take(10)->get();
        $ekstraTablolar['son_sikayet_takimlari'] = Takim::where('tur', 'sikayet')->with('lider')->latest()->take(10)->get();

        // 2. SON ÇÖZÜLEN ŞİKAYETLER
        $ekstraTablolar['son_cozulen_sikayetler'] = MusteriSikayeti::where('musteri_durum', 'Kapatıldı')->with(['cozumTakimi', 'sikayetKategori', 'customer', 'iaaProjesi'])->latest('updated_at')->take(10)->get();

        // 3. SON TAMAMLANAN IAA (SADECE IAA)
        $ekstraTablolar['son_tamamlanan_iaa'] = Iaa::sadeceOneriler()->where('durum', 'Tamamlandı')->with('atananTakim')->latest('updated_at')->take(10)->get();

        // 4. DİSİPLİN VAKALARI (YENİ)
        $ekstraTablolar['son_disiplin_vakalari'] = DisciplinaryCase::with(['user'])->latest()->take(10)->get();

        // 5. DİĞERLERİ
        $ekstraTablolar['son_yorumlar'] = ProjeYorumu::with('iaa')->latest()->take(10)->get();
        $ekstraTablolar['son_profil_yorumlari'] = ProfileComment::with(['yazan', 'profilSahibi'])->latest()->take(10)->get();

        // 5. SON KAZANILAN PUANLAR (Global Karma Liste)
        // A) Projelerden (IAA + Şikayet Çözümü)
        $puanliProjeler = Iaa::where('puan', '>', 0)
            ->where('durum', 'Tamamlandı')
            ->with(['atananTakim', 'musteriSikayeti.sikayetKategori'])
            ->latest('updated_at')
            ->take(10)
            ->get()
            ->toBase()
            ->map(function($p) {
                // Puanı kazanan kişiyi bul (Sabitlenen lider veya fallback olarak mevcut lider)
                $winnerId = $p->tamamlayan_lider_id ?? ($p->atananTakim ? $p->atananTakim->lider_user_id : null);
                $winner = $winnerId ? User::withTrashed()->find($winnerId) : null;

                return [
                    'id' => $p->id,
                    'tip' => $p->musteriSikayeti ? 'Şikayet (Proje)' : 'İAA Projesi',
                    'baslik' => $p->baslik,
                    'puan' => $p->puan,
                    'tarih' => $p->updated_at,
                    'takim' => $p->atananTakim ? $p->atananTakim->ad : '-',
                    'kategori' => $p->musteriSikayeti && $p->musteriSikayeti->sikayetKategori ? $p->musteriSikayeti->sikayetKategori->ad : 'Genel',
                    'user' => $winner,
                    'badge_color' => $p->musteriSikayeti ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800',
                    'url' => route('proje.workspace.show', $p->id)
                ];
            });

        // B) Şikayet Girişleri
        $puanliSikayetGirisleri = MusteriSikayeti::where('kazanilan_puan', '>', 0)
            ->with(['olusturanKurulUyesi', 'sikayetKategori', 'cozumTakimi'])
            ->latest('created_at')
            ->take(10)
            ->get()
            ->toBase()
            ->map(fn($s) => [
                'id' => $s->id,
                'tip' => 'Şikayet (Giriş)',
                'baslik' => $s->musteri_sikayet_konusu,
                'puan' => $s->kazanilan_puan,
                'tarih' => $s->created_at,
                'takim' => $s->cozumTakimi ? $s->cozumTakimi->ad : '-',
                'kategori' => $s->sikayetKategori ? $s->sikayetKategori->ad : 'Genel',
                'user' => $s->olusturanKurulUyesi, // Bu zaten sabit (oluşturan)
                'badge_color' => 'bg-indigo-100 text-indigo-800',
                'url' => route('admin.sikayetler.show', $s->id)
            ]);

        // Birleştir ve Sırala
        $ekstraTablolar['son_kazanilan_puanlar'] = collect($puanliProjeler->all())
            ->merge($puanliSikayetGirisleri->all())
            ->sortByDesc('tarih')
            ->take(10);

        return $ekstraTablolar;
    }
}
