<?php

namespace App\Services\ProjectWorkspace;

use App\Models\Iaa;
use App\Models\IaaLog;
use App\Models\IaaStepAssignment;
use App\Models\MusteriSikayetiLog;
use App\Notifications\ProjeDurumuDegisti;
use App\Notifications\ProjeStakeholderBilgilendirme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Http;

class ProjeTamamlamaService
{
    protected $calismaAlaniService;

    public function __construct(ProjeCalismaAlaniService $calismaAlaniService)
    {
        $this->calismaAlaniService = $calismaAlaniService;
    }
    /**
     * İadeli Tamamlama (SENARYO 1)
     */
    public function completeWithReturn(Request $request, $id)
    {
        $iaa = Iaa::with('musteriSikayeti')->findOrFail($id);

        $request->validate([
            'iade_tarihi' => 'required|date',
            'urun_turu' => 'required|string',
            'iade_sebebi' => 'required|string',
            'miktar' => 'required|numeric|min:0',
            'birim' => 'required|string',
            'toplam_parti_miktari' => 'nullable|numeric|min:0',
            'aciklama' => 'nullable|string',
            'dosya' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        DB::transaction(function () use ($request, $iaa) {
            // 1. İade Kaydını Oluştur veya Güncelle
            if ($iaa->musteriSikayeti) {

                $dosyaYolu = null;
                if ($request->hasFile('dosya')) {
                    $dosyaYolu = $request->file('dosya')->store('iade_dosyalari', 'public');
                }

                // Mevcut kaydı bul veya yeni oluştur
                $iade = \App\Models\SikayetIadesi::where('musteri_sikayeti_id', $iaa->musteriSikayeti->id)->first();

                if ($iade) {
                    $iade->update([
                        'user_id' => Auth::id(),
                        'iade_tarihi' => $request->iade_tarihi,
                        'urun_turu' => $request->urun_turu,
                        'iade_sebebi' => $request->iade_sebebi,
                        'miktar' => $request->miktar,
                        'birim' => $request->birim,
                        'toplam_parti_miktari' => $request->toplam_parti_miktari,
                        'aciklama' => $request->aciklama,
                        'dosya_yolu' => $dosyaYolu ?? $iade->dosya_yolu // Yeni dosya varsa güncelle, yoksa eskisi kalsın
                    ]);
                } else {
                    \App\Models\SikayetIadesi::create([
                        'musteri_sikayeti_id' => $iaa->musteriSikayeti->id,
                        'user_id' => Auth::id(),
                        'iade_tarihi' => $request->iade_tarihi,
                        'urun_turu' => $request->urun_turu,
                        'iade_sebebi' => $request->iade_sebebi,
                        'miktar' => $request->miktar,
                        'birim' => $request->birim,
                        'toplam_parti_miktari' => $request->toplam_parti_miktari,
                        'aciklama' => $request->aciklama,
                        'dosya_yolu' => $dosyaYolu
                    ]);
                }
            }

            // 2. Projeyi Güncelle
            $isQualityManager = $this->calismaAlaniService->isQualityManagerWithInterventionPower(Auth::user(), $iaa);
            $direktorOnayiAyari = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->first();
            $direktorOnayiAktif = $direktorOnayiAyari ? $direktorOnayiAyari->value == '1' : false;

            $yeniDurum = 'Bölüm Onayı Bekliyor';
            $updateData = [
                'basvuran_notu' => $request->aciklama,
                'onaya_gonderilme_tarihi' => now(),
                'tamamlanma_tarihi' => now(),
                'durum_degistirme_tarihi' => now(),
                'tamamlayan_lider_id' => optional($iaa->atananTakim)->lider_user_id,
            ];

            $notifyRole = 'Bölüm Kalite Yöneticisi';
            $logEylemi = 'Bölüm Onayına Gönderildi (İadeli)';
            $logAciklama = Auth::user()->name . " projeyi tamamladı ve iade bilgileriyle onaya sundu.";

            $direktorGecerli = false;
            if ($direktorOnayiAktif && $iaa->musteriSikayeti) {
                $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum ?? $iaa->musteriSikayeti->bolum ?? null;
                if ($bolum && $bolum->director_id) {
                    $direktorGecerli = true;
                }
            }

            if ($isQualityManager) {
                if ($iaa->musteriSikayeti && $direktorGecerli) {
                    $yeniDurum = 'Direktör Onayı Bekliyor';
                    $logEylemi = 'Bölüm Onaylandı (İadeli - Direktör Onayına Sevk)';
                    $logAciklama = Auth::user()->name . " projeyi tamamlayarak onayladı ve iade bilgileriyle direktör onayına sundu.";
                    $notifyRole = 'Superadmin';
                } else {
                    $yeniDurum = 'Yönetici Onayı Bekliyor';
                    $logEylemi = 'Bölüm Onaylandı (İadeli - Yönetici Onayına Sevk)';
                    $logAciklama = Auth::user()->name . " projeyi tamamlayarak onayladı ve iade bilgileriyle yönetici onayına sundu.";
                    $notifyRole = 'Superadmin';
                }
                $updateData['onaylayan_user_id'] = Auth::id();
                $updateData['onaylanma_tarihi'] = now();
            }
            $updateData['durum'] = $yeniDurum;

            $iaa->update($updateData);

            // Şikayet Durumu Senkronizasyonu
            if ($iaa->musteriSikayeti) {
                $iaa->musteriSikayeti->update(['musteri_durum' => $yeniDurum]);
            }

            // === YENİ: TARİHSEL PUAN BÜTÜNLÜĞÜ (Takım Üyelerini Dondur) ===
            if ($iaa->atananTakim) {
                // 1. Mevcut ekipte olup işten ayrılanları temizle (Pivot tablodan uçur)
                $iaa->projeEkibi()
                    ->where(function($q) {
                        $q->whereNotNull('users.deleted_at')
                          ->orWhere('users.is_personnel', false)
                          ->orWhere(function($sq) {
                              $sq->whereNotNull('users.termination_date')
                                 ->where('users.termination_date', '<=', now());
                          });
                    })
                    ->detach();

                // 2. Takımdaki güncel aktif personelleri çek
                $uyeler = $iaa->atananTakim->uyeler()
                    ->where('is_personnel', true)
                    ->whereNull('users.deleted_at')
                    ->where(function($q) {
                        $q->whereNull('users.termination_date')
                          ->orWhere('users.termination_date', '>', now());
                    })
                    ->get();

                // PUAN KAYNAĞI: Şikayet projesi ise musteri_puan, değilse iaas.puan
                $isComplaintProject = !empty($iaa->musteriSikayeti);
                $dogruPuan = ($isComplaintProject)
                    ? (float)($iaa->musteriSikayeti->musteri_puan ?? $iaa->puan ?? 0)
                    : ($iaa->puan ?? 0);

                if ($uyeler->isNotEmpty()) {
                    $syncData = [];
                    foreach ($uyeler as $uye) {
                        $syncData[$uye->id] = [
                            'rol' => ($uye->id == $iaa->atananTakim->lider_user_id) ? 'Lider' : 'Takım Üyesi',
                            'kazanilan_puan' => $dogruPuan,
                            'durum' => 'onaylandi'
                        ];
                    }
                    $iaa->projeEkibi()->syncWithoutDetaching($syncData);
                }
            }
            // =========================================================


            // 3. Log
            IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => $logEylemi,
                'aciklama' => $logAciklama
            ]);

            // Takvim Ziyaretini Kilitle
            $this->lockVisitInTakvim($iaa->id, true);

            // 4. Müşteri Şikayeti Log
            if ($iaa->musteriSikayeti) {
                MusteriSikayetiLog::create([
                    'musteri_sikayeti_id' => $iaa->musteriSikayeti->id,
                    'user_id' => Auth::id(),
                    'eylem' => 'Çözüm Önerisi Sunuldu',
                    'aciklama' => 'Proje tamamlandı, iadeli kapanış onayı başlatıldı.'
                ]);
            }
            // Yan işlemler (Hata alsa da ana süreci bozmasın)
            try {
                // Bildirim Gönder
                $this->notifyNextApprover($iaa, $notifyRole);
            } catch (\Exception $e) {
                \Log::error("İadeli tamamlama bildirimi gönderilemedi: " . $e->getMessage());
                \App\Helpers\MailLogHelper::logFailure($iaa, "İadeli Proje Tamamlama Bildirimi", $notifyRole, $e->getMessage());
            }
        });
    }

    /**
     * İadesiz Tamamlama (SENARYO 2)
     */
    public function completeWithoutReturn(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        DB::transaction(function () use ($request, $iaa) {
            $isComplaint = $iaa->musteriSikayeti !== null;
            $isQualityManager = $this->calismaAlaniService->isQualityManagerWithInterventionPower(Auth::user(), $iaa);
            $direktorOnayiAyari = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->first();
            $direktorOnayiAktif = $direktorOnayiAyari ? $direktorOnayiAyari->value == '1' : false;
            
            $updateData = [
                'basvuran_notu' => 'İadesiz kapanış yapıldı.',
                'onaya_gonderilme_tarihi' => now(),
                'tamamlanma_tarihi' => now(),
                'durum_degistirme_tarihi' => now(),
                'tamamlayan_lider_id' => optional($iaa->atananTakim)->lider_user_id,
            ];

            $direktorGecerli = false;
            if ($direktorOnayiAktif && $iaa->musteriSikayeti) {
                $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum ?? $iaa->musteriSikayeti->bolum ?? null;
                if ($bolum && $bolum->director_id) {
                    $direktorGecerli = true;
                }
            }

            if ($isQualityManager && $isComplaint) {
                if ($direktorGecerli) {
                    $yeniDurum = 'Direktör Onayı Bekliyor';
                    $logEylemi = 'Bölüm Onaylandı (Direktör Onayına Sevk)';
                    $notifyRole = 'Superadmin';
                    $bildirimMesaji = $iaa->baslik . " başlıklı şikayet " . Auth::user()->name . " tarafından onaylanmış ve direktör onayına sunulmuştur.";
                } else {
                    $yeniDurum = 'Yönetici Onayı Bekliyor';
                    $logEylemi = 'Bölüm Onaylandı (Yönetici Onayına Sevk)';
                    $notifyRole = 'Superadmin';
                    $bildirimMesaji = $iaa->baslik . " başlıklı şikayet " . Auth::user()->name . " tarafından onaylanmış ve yönetici onayına sunulmuştur.";
                }
                $updateData['onaylayan_user_id'] = Auth::id();
                $updateData['onaylanma_tarihi'] = now();
            } else {
                $yeniDurum = $isComplaint ? 'Bölüm Onayı Bekliyor' : 'Yönetici Onayı Bekliyor';
                $logEylemi = $isComplaint ? 'Bölüm Onayına Gönderildi' : 'Yönetici Onayına Gönderildi';
                $notifyRole = $isComplaint ? 'Bölüm Kalite Yöneticisi' : 'Superadmin';
                $bildirimMesaji = $isComplaint
                    ? Auth::user()->name . " projeyi tamamladı."
                    : $iaa->baslik . " başlıklı İAA projesi " . Auth::user()->name . " tarafından onayınıza sunulmuştur.";
            }
            
            $updateData['durum'] = $yeniDurum;
            $iaa->update($updateData);

            // Şikayet Durumu Senkronizasyonu
            if ($iaa->musteriSikayeti) {
                $iaa->musteriSikayeti->update(['musteri_durum' => $yeniDurum]);
            }

            // === YENİ: TARİHSEL PUAN BÜTÜNLÜĞÜ (Takım Üyelerini Dondur) ===
            if ($iaa->atananTakim) {
                // 1. Mevcut ekipte olup işten ayrılanları temizle (Pivot tablodan uçur)
                $iaa->projeEkibi()
                    ->where(function($q) {
                        $q->whereNotNull('users.deleted_at')
                          ->orWhere('users.is_personnel', false)
                          ->orWhere(function($sq) {
                              $sq->whereNotNull('users.termination_date')
                                 ->where('users.termination_date', '<=', now());
                          });
                    })
                    ->detach();

                // 2. Takımdaki güncel aktif personelleri çek
                $uyeler = $iaa->atananTakim->uyeler()
                    ->where('is_personnel', true)
                    ->whereNull('users.deleted_at')
                    ->where(function($q) {
                        $q->whereNull('users.termination_date')
                          ->orWhere('users.termination_date', '>', now());
                    })
                    ->get();

                // PUAN KAYNAĞI: Şikayet projesi ise musteri_puan, değilse iaas.puan
                $isComplaintProject = !empty($iaa->musteriSikayeti);
                $dogruPuan = ($isComplaintProject)
                    ? (float)($iaa->musteriSikayeti->musteri_puan ?? $iaa->puan ?? 0)
                    : ($iaa->puan ?? 0);

                if ($uyeler->isNotEmpty()) {
                    $syncData = [];
                    foreach ($uyeler as $uye) {
                        $syncData[$uye->id] = [
                            'rol' => ($uye->id == $iaa->atananTakim->lider_user_id) ? 'Lider' : 'Takım Üyesi',
                            'kazanilan_puan' => $dogruPuan,
                            'durum' => 'onaylandi'
                        ];
                    }
                    $iaa->projeEkibi()->syncWithoutDetaching($syncData);
                }
            }

            IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => $logEylemi,
                'aciklama' => Auth::user()->name . " projeyi tamamladı ve $notifyRole onayına sundu."
            ]);

            // Müşteri Şikayeti Log
            if ($isComplaint) {
                MusteriSikayetiLog::create([
                    'musteri_sikayeti_id' => $iaa->musteriSikayeti->id,
                    'user_id' => Auth::id(),
                    'eylem' => 'Çözüm Önerisi Sunuldu',
                    'aciklama' => 'Proje tamamlandı, onay süreci başlatıldı.'
                ]);
            }

            // Yan işlemler (Hata alsa da ana süreci bozmasın)
            try {
                // Takvim Ziyaretini Kilitle
                $this->lockVisitInTakvim($iaa->id, true);
            } catch (\Exception $e) {
                \Log::warning("Takvim kilitleme hatası (Proje tamamlandı ama takvim kilitlenemedi): " . $e->getMessage());
            }

            try {
                // Bildirim Gönder
                if ($isQualityManager && $isComplaint) {
                    $this->notifyNextApprover($iaa, 'Superadmin', $bildirimMesaji);
                } else {
                    $this->notifyNextApprover($iaa, $notifyRole, $bildirimMesaji);
                }
            } catch (\Exception $e) {
                \Log::error("Proje tamamlama bildirimi gönderilemedi: " . $e->getMessage());
                \App\Helpers\MailLogHelper::logFailure($iaa, "İadesiz Proje Tamamlama Bildirimi", $notifyRole, $e->getMessage());
            }
        });
    }

    /**
     * Başvuruyu Geri Çekme
     */
    public function recallSubmission(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        $isSuperAdmin = Auth::user()->hasRole('Superadmin');
        $isTeamLeader = $iaa->projeEkibi()->where('user_id', Auth::id())->where('iaa_user.rol', 'Lider')->exists();
        $isLeader = ($iaa->atananTakim && $iaa->atananTakim->lider_user_id == Auth::id()) || $isTeamLeader;
        $isQualityManager = $iaa->musteriSikayeti && $this->calismaAlaniService->isQualityManagerWithInterventionPower(Auth::user(), $iaa);

        $allowedStatuses = ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor'];
        if (!in_array($iaa->durum, $allowedStatuses)) {
            abort(403, 'Sadece onay sürecindeki projeler geri çekilebilir.');
        }

        // Yetki Kontrolü:
        // 1. Bölüm Onayı Bekliyor -> Lider, Müdahale Yetkili QM veya Admin geri çekebilir.
        // 2. Direktör Onayı Bekliyor -> Lider, Müdahale Yetkili QM veya Admin geri çekebilir.
        $canRecall = $isSuperAdmin || $isLeader || $isQualityManager;

        if (!$canRecall) {
            abort(403, 'Bu aşamada onayı geri çekme yetkiniz bulunmamaktadır.');
        }

        $iaa->update([
            'durum' => 'Atandı', // Veya 'Devam Ediyor'
            'tamamlanma_tarihi' => null,
            'tamamlayan_lider_id' => null, // Onay geri çekildiğinde tamamlayan bilgisini temizle
            'durum_degistirme_tarihi' => now(),
        ]);

        // Şikayet Durumu Senkronizasyonu
        if ($iaa->musteriSikayeti) {
            $iaa->musteriSikayeti->update(['musteri_durum' => 'İşlemde']);
        }


        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Başvuru Geri Çekildi',
            'aciklama' => Auth::user()->name . " onaya gönderilen projeyi geri çekti."
        ]);

        // Takvim Ziyaret kilidini aç
        $this->lockVisitInTakvim($iaa->id, false);

        // Bildirimleri temizle (Database Notification)
        // İlgili IAA'ya ait okunmamış bildirimleri bul ve sil
        // Bu logic biraz karmaşık olabilir, basitçe geçiyorum şimdilik
    }

    private function lockVisitInTakvim($iaaId, $lock = true)
    {
        try {
            // Takvim API URL'si .env'den veya config'den alınmalı
            $baseUrl = rtrim(config('services.takvim.url', 'http://localhost:8001'), '/');
            
            Http::withHeaders([
                'Accept' => 'application/json',
                'X-App-Internal' => 'iaa'
            ])->post($baseUrl . '/api/visit/toggle-lock', [
                'remote_id' => $iaaId,
                'lock' => $lock
            ]);
        } catch (\Exception $e) {
            \Log::error("Takvim visit lock toggle failed for IAA $iaaId: " . $e->getMessage());
        }
    }

    private function notifyNextApprover(Iaa $iaa, $roleName, $customMessage = null)
    {
        $tamamlayanKisi = Auth::user()->name;

        if ($roleName === 'Bölüm Kalite Yöneticisi') {
            // Bölüm bilgisini belirle (Proje veya Şikayet üzerinden)
            $bolum = $iaa->bolum ?? ($iaa->musteriSikayeti ? $iaa->musteriSikayeti->bolum : null);
            $bolumId = $bolum ? $bolum->id : null;
            $bolumAdi = $bolum ? $bolum->ad : 'Belirsiz';

            // Kalite Yöneticilerini Bul (Onay verecek kişiler)
            $kaliteYoneticileri = collect();
            if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayet_kategorisi_id) {
                $kategoriId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
                $kaliteYoneticileri = \App\Models\User::role('Bölüm Kalite Yöneticisi')
                    ->where(function ($q) use ($bolumId, $kategoriId) {
                        $q->where('bolum_id', $bolumId)
                            ->orWhereHas('yonettigiSikayetKategorileri', function ($sq) use ($kategoriId) {
                                $sq->where('sikayet_kategori_id', $kategoriId);
                            });
                    })->get();
            } else if ($bolumId) {
                $kaliteYoneticileri = \App\Models\User::role('Bölüm Kalite Yöneticisi')->where('bolum_id', $bolumId)->get();
            }

            $kyIsimleri = $kaliteYoneticileri->pluck('name')->implode(', ');
            if (empty($kyIsimleri)) {
                $kyIsimleri = 'Bölüm Kalite Yöneticisi';
            }

            // 1. Kalite Yöneticisine (Onay Bekliyor Bildirimi)
            $message = $customMessage ?? ($tamamlayanKisi . " projeyi tamamladı.");
            if ($kaliteYoneticileri->isNotEmpty()) {
                Notification::send($kaliteYoneticileri, new ProjeDurumuDegisti(
                    $iaa,
                    'ONAYINIZI BEKLİYOR.',
                    $message
                ));
            }

            // 2. Direktöre (Şenol Kanat vb.)
            if ($bolum && $bolum->director_id) {
                $direktor = \App\Models\User::find($bolum->director_id);
                if ($direktor) {
                    $dirMessage = "Direktörlüğünüze bağlı {$bolumAdi} bölümüne ait {$iaa->baslik} başlıklı şikayet {$tamamlayanKisi} tarafından {$kyIsimleri}'ın onayına gönderilmiştir";
                    $direktor->notify(new ProjeStakeholderBilgilendirme($iaa, $dirMessage, 'info'));
                }
            }

            // 3. Bölüm Liderine (Emrah Al vb.)
            if ($bolumId) {
                $bolumLiderleri = \App\Models\User::role('Bölüm Lideri')->where('bolum_id', $bolumId)->get();
                if ($bolumLiderleri->isNotEmpty()) {
                    $blMessage = "Bölümünüze bağlı {$iaa->baslik} başlıklı şikayet {$tamamlayanKisi} tarafından {$kyIsimleri}'ın onayına gönderilmiştir";
                    Notification::send($bolumLiderleri, new ProjeStakeholderBilgilendirme($iaa, $blMessage, 'info'));
                }
            }

            // 4. Müşteri Temsilcilerine
            if ($iaa->musteriSikayeti) {
                $sikayet = $iaa->musteriSikayeti;
                $temsilciler = collect();

                if ($sikayet->yetkili_user_id) {
                    $temsilciler->push($sikayet->yetkili_user);
                }
                $temsilciler = $temsilciler->merge($sikayet->ekYetkililer);

                if ($sikayet->customer) {
                    $firmaTemsilcileri = $sikayet->customer->users()->role('Müşteri Temsilcisi')->get();
                    $temsilciler = $temsilciler->merge($firmaTemsilcileri);
                }

                $finalTemsilciler = $temsilciler->unique('id')->reject(fn($u) => $u->id == Auth::id());

                if ($finalTemsilciler->isNotEmpty()) {
                    $msMsg = "{$iaa->baslik} başlıklı Şikayetiniz {$bolumAdi} bölümü kalite yöneticisi onayına sunulmuştur.";
                    Notification::send($finalTemsilciler, new ProjeStakeholderBilgilendirme($iaa, $msMsg, 'info'));
                }
            }
        } else {
            // Diğer Roller İçin (Mevcut Mantık - Genelde Superadmin için)
            $query = \App\Models\User::role($roleName);
            $yoneticiler = $query->get();
            $message = $customMessage ?? ($tamamlayanKisi . " projeyi tamamladı.");

            if ($yoneticiler->isNotEmpty()) {
                Notification::send($yoneticiler, new ProjeDurumuDegisti(
                    $iaa,
                    'ONAYINIZI BEKLİYOR.',
                    $message
                ));
            }
        }
    }
}
