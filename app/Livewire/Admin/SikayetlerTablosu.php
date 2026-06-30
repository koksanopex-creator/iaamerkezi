<?php

namespace App\Livewire\Admin;

use App\Models\MusteriSikayeti;
use App\Models\Takim;
use App\Models\User;
use App\Models\Iaa;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\SikayetKategori;
use Illuminate\Support\Facades\Auth;

class SikayetlerTablosu extends Component
{
    use WithPagination;

    // --- MEVCUT FİLTRELER ---
    public $filtreDurum = [];
    public $filtreOncelik = [];
    public $filtreTakim = [];
    public $filtreKategori = [];
    public $filtreMusteriAdi = '';
    public $filtreEkleyen = [];
    public $filtreSonTarihBaslangic = '';
    public $filtreSonTarihBitis = '';
    public $filtreKayitTarihBaslangic = '';
    public $filtreKayitTarihBitis = '';
    public $filtrePuanMin = null;
    public $filtrePuanMax = null;
    public $filtreKonumTipi = [];
    public $filtreProjeDurumu = [];
    public $filtreBeklemeMin = '';
    public $filtreBeklemeMax = '';
    public $filtreKonu = '';
    public $filtreIadeVar = false; // YENİ
    public $filtreZiyaretVar = false; // YENİ
    public $viewMode = 'card'; // 'card' veya 'list'
    public $selectedSikayetler = []; // Seçili şikayetlerin ID'leri
    public $activeTab = 'tumu'; // Varsayılan sekme
    
    protected $queryString = [
        'activeTab' => ['as' => 'tab'],
        'filtreDurum' => ['except' => []],
        'filtreOncelik' => ['except' => []],
        'filtreKategori' => ['except' => []],
        'filtreTakim' => ['except' => []],
        'filtreMusteriAdi' => ['except' => ''],
        'filtreEkleyen' => ['except' => []],
        'filtreSonTarihBaslangic' => ['except' => ''],
        'filtreSonTarihBitis' => ['except' => ''],
        'filtreKayitTarihBaslangic' => ['except' => ''],
        'filtreKayitTarihBitis' => ['except' => ''],
        'filtrePuanMin' => ['except' => null],
        'filtrePuanMax' => ['except' => null],
        'filtreKonumTipi' => ['except' => []],
        'filtreProjeDurumu' => ['except' => []],
        'filtreBeklemeMin' => ['except' => ''],
        'filtreBeklemeMax' => ['except' => ''],
        'filtreKonu' => ['except' => ''],
        'filtreIadeVar' => ['except' => false],
        'filtreZiyaretVar' => ['except' => false],
    ];
    
    // --- SİLME MODALI DEĞİŞKENLERİ ---
    public $confirmingDeletionId = null;
    public $confirmingBulkDelete = false;

    // --- GERİ AL MODÜLÜ CİHAZLARI ---
    public $recentlyDeletedSikayet = null;

    protected $listeners = ['sikayetGuncellendi' => '$refresh'];

    public function mount()
    {
        $this->viewMode = session()->get('sikayet_view_mode', 'card');
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        session()->put('sikayet_view_mode', $mode);
    }

    public function updated($propertyName)
    {
        // Filtre değişince sayfayı başa al
        if (str_starts_with($propertyName, 'filtre')) {
            $this->resetPage();
        }
    }

    public $selectAll = false;

    public function resetFilters()
    {
        $this->reset([
            'filtreDurum', 'filtreOncelik', 'filtreTakim', 'filtreKategori', 
            'filtreMusteriAdi', 'filtreEkleyen', 'filtreSonTarihBaslangic', 
            'filtreSonTarihBitis', 'filtreKayitTarihBaslangic', 'filtreKayitTarihBitis',
            'filtrePuanMin', 'filtrePuanMax', 'filtreKonumTipi', 'filtreProjeDurumu',
            'filtreBeklemeMin', 'filtreBeklemeMax', 'filtreKonu', 'filtreIadeVar', 'filtreZiyaretVar'
        ]);
        
        // Dizi olanları açıkça sıfırla (reset bazen boş string yapabiliyor config'e göre)
        $this->filtreDurum = [];
        $this->filtreOncelik = [];
        $this->filtreTakim = [];
        $this->filtreKategori = [];
        $this->filtreEkleyen = [];
        $this->filtreKonumTipi = [];
        $this->filtreProjeDurumu = [];

        $this->activeTab = 'tumu';
        $this->resetPage();
    }

    // === YENİ: SEKME DEĞİŞTİRME FONKSİYONU ===
    public function setTab($tab)
    {
        if ($tab === 'cop_kutusu' && !Auth::user()->hasRole(['Superadmin', 'Super Admin', 'SUPERADMIN', 'superadmin', 'Yonetim', 'Yönetim'])) {
            return;
        }

        $this->activeTab = $tab;
        $this->filtreDurum = []; // Sekme değişince detaylı durum filtresini temizle
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletionId = $id;
        $this->confirmingBulkDelete = false;
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedSikayetler)) return;
        $this->confirmingBulkDelete = true;
        $this->confirmingDeletionId = null;
    }

    public function executeDelete()
    {
        if ($this->confirmingDeletionId) {
            $this->performDelete([$this->confirmingDeletionId]);
            session()->flash('message', 'Şikayet başarıyla silindi.');
            $this->confirmingDeletionId = null;
        } elseif ($this->confirmingBulkDelete) {
            $this->bulkDelete();
        }
        $this->dispatch('close-modal');
    }

    public function cancelDelete()
    {
        $this->confirmingDeletionId = null;
        $this->confirmingBulkDelete = false;
    }

    public function delete($id)
    {
        // Geriye dönük uyumluluk veya farklı kullanımlar için koruyabiliriz 
        // ama artık executeDelete() kullanılacak.
        $this->performDelete([$id]);
        session()->flash('message', 'Şikayet başarıyla silindi.');
        $this->dispatch('close-modal');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Filtreleri uygulayarak tüm eşleşen şikayet ID'lerini al
            $query = MusteriSikayeti::query();
            
            // Yetki filtrelerini uygula (render'daki mantıkla aynı olmalı)
            $this->applyAuthorizationFilters($query);
            
            // Aktif filtreleri uygula
            $this->applyFilters($query);
            
            // Sekme filtresini uygula
            $this->applyTabFilter($query);

            $this->selectedSikayetler = $query->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedSikayetler = [];
        }
    }

    public function bulkDelete()
    {
        if (!Auth::user()->can('deleteAny', \App\Models\MusteriSikayeti::class)) {
            abort(403, 'Yetkisiz işlem.');
        }

        if (empty($this->selectedSikayetler)) {
            return;
        }

        $this->performDelete($this->selectedSikayetler);
        $this->selectedSikayetler = [];
        $this->selectAll = false;

        session()->flash('message', 'Seçilen şikayetler başarıyla silindi.');
    }

    protected function performDelete(array $ids)
    {
        $user = Auth::user();
        $sikayetler = MusteriSikayeti::withTrashed()->whereIn('id', $ids)->get();

        DB::beginTransaction();
        try {
            foreach ($sikayetler as $sikayet) {
                if ($this->activeTab === 'cop_kutusu') {
                    // KALICI SİLME (SuperAdmin)
                    \App\Models\MusteriLog::add(
                        $sikayet->customer_id,
                        'Kalıcı Şikayet Silme',
                        $user->name . ', #' . $sikayet->id . ' nolu şikayeti VERİTABANINDAN KALICI olarak sildi.'
                    );
                    $sikayet->forceDelete();
                } else {
                    // YUMUŞAK SİLME (Soft Delete)
                    \App\Models\MusteriLog::add(
                        $sikayet->customer_id,
                        'Şikayet Silme',
                        $user->name . ', #' . $sikayet->id . ' nolu şikayeti çöp kutusuna taşıdı.'
                    );
                    $sikayet->delete();
                    
                    // Geri al pop-upı için kayıt
                    $this->recentlyDeletedSikayet = [
                        'id' => $sikayet->id,
                        'ids' => $ids, // Toplu silme durumunda tüm id'leri tutalım
                        'message' => count($ids) > 1 
                                        ? count($ids) . ' adet şikayet id çöp kutusuna taşındı.'
                                        : $sikayet->musteri_adi . ' şikayeti çöp kutusuna taşındı.'
                    ];
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Delete Error: ' . $e->getMessage());
        }
    }

    public function undoDelete()
    {
        if ($this->recentlyDeletedSikayet) {
            $ids = $this->recentlyDeletedSikayet['ids'] ?? [];
            if (!empty($ids)) {
                $sikayetler = MusteriSikayeti::withTrashed()->whereIn('id', $ids)->get();
                foreach($sikayetler as $s) {
                    $s->restore();
                }
                session()->flash('success_message', 'Silinen öğeler başarıyla geri alındı.');
            }
            $this->recentlyDeletedSikayet = null;
        }
    }

    public function restoreFromCopKutusu($id)
    {
        if (!Auth::user()->hasRole(['Superadmin', 'Super Admin', 'SUPERADMIN', 'superadmin', 'Yonetim', 'Yönetim'])) {
            abort(403);
        }
        $sikayet = MusteriSikayeti::withTrashed()->find($id);
        if ($sikayet) {
            $sikayet->restore();
            session()->flash('success_message', 'Şikayet başarıyla listelere geri döndürüldü.');
        }
    }

    protected function applyAuthorizationFilters($query)
    {
        $user = Auth::user();

        // --- MÜŞTERİ TEMSİLCİSİ / ÇOKLU FİRMA FİLTRESİ ---
        // Eğer kullanıcı personel değilse (Müşteri Temsilcisi ise) veya bu rollere sahipse
        if ($user->hasRole(['Müşteri', 'Müşteri Temsilcisi']) || !$user->is_personnel) {
            $activeCustomerId = session('active_customer_id_' . $user->id) ?? $user->customer_id;
            
            if ($activeCustomerId) {
                $query->where('customer_id', $activeCustomerId);
                return; // Temsilci ise diğer bölge/takım filtrelerine girmesine gerek yok
            }
        }

        if ($user->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Yonetim'])) {
            // Hepsi
        } elseif ($user->hasRole('Direktör')) {
            $yonettigiBolumIds = $user->getAllowedBolumIds();
            if ($yonettigiBolumIds === '*') {
                // Hepsi
            } elseif (empty($yonettigiBolumIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereHas('sikayetKategori', function ($q) use ($yonettigiBolumIds) {
                    $q->whereIn('bolum_id', $yonettigiBolumIds);
                });
            }
        } elseif ($user->hasRole('Müşteri Saha Temsilcisi')) {
            $yonettigiBolumIds = $user->musteriSahaTemsilcisiOlduguBolumler()->pluck('bolumler.id')->toArray();
            if (empty($yonettigiBolumIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereHas('sikayetKategori', function ($q) use ($yonettigiBolumIds) {
                    $q->whereIn('bolum_id', $yonettigiBolumIds);
                });
            }
        } elseif ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            $yonettigiKategoriIds = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
            if (empty($yonettigiKategoriIds) && $user->bolum_id) {
                $yonettigiKategoriIds = SikayetKategori::where('bolum_id', $user->bolum_id)->pluck('id')->toArray();
            }
            if (empty($yonettigiKategoriIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('sikayet_kategorisi_id', $yonettigiKategoriIds);
            }
        } elseif ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
            $bolumId = $user->bolum_id;
            $personelIds = User::where('bolum_id', $bolumId)->pluck('id');
            $query->where(function ($q) use ($bolumId, $personelIds) {
                $q->whereHas('sikayetKategori', function ($subQ) use ($bolumId) {
                    $subQ->where('bolum_id', $bolumId);
                })
                    ->orWhereHas('iaaProjesi', function ($subQ) use ($personelIds) {
                        $subQ->whereHas('projeEkibi', function ($squadQ) use ($personelIds) {
                            $squadQ->whereIn('users.id', $personelIds)->where('iaa_user.durum', 'onaylandi');
                        });
                    });
            });
        } elseif ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            $lideriOlduguTakimIds = $user->lideriOlduguTakimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            if ($lideriOlduguTakimIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('atanan_cozum_takimi_id', $lideriOlduguTakimIds);
            }
        } elseif ($user->hasBolumAuthority('bolum.sikayet.gor') && $user->bolum_id) {
            $bolumId = $user->bolum_id;
            $query->whereHas('sikayetKategori', function ($subQ) use ($bolumId) {
                $subQ->where('bolum_id', $bolumId);
            });
        } else {
            $uyesiOlduguTakimIds = $user->takimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            if ($uyesiOlduguTakimIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('atanan_cozum_takimi_id', $uyesiOlduguTakimIds);
            }
        }
    }

    protected function applyFilters($query)
    {
        $query->when(!empty($this->filtreDurum), fn($q) => $q->whereIn('musteri_durum', (array) $this->filtreDurum));
        $query->when(!empty($this->filtreOncelik), fn($q) => $q->whereIn('musteri_oncelik', (array) $this->filtreOncelik));
        $query->when(!empty($this->filtreTakim), fn($q) => $q->whereIn('atanan_cozum_takimi_id', (array) $this->filtreTakim));
        $query->when($this->filtreMusteriAdi, fn($q) => $q->where('musteri_adi', 'like', '%' . $this->filtreMusteriAdi . '%'));
        $query->when(strlen($this->filtreKonu) >= 2, fn($q) => $q->where('musteri_sikayet_konusu', 'like', '%' . $this->filtreKonu . '%'));
        $query->when(!empty($this->filtreEkleyen), fn($q) => $q->whereIn('olusturan_kurul_uyesi_id', (array) $this->filtreEkleyen));
        $query->when($this->filtreSonTarihBaslangic, fn($q) => $q->whereDate('musteri_cozum_son_tarihi', '>=', $this->filtreSonTarihBaslangic));
        $query->when($this->filtreSonTarihBitis, fn($q) => $q->whereDate('musteri_cozum_son_tarihi', '<=', $this->filtreSonTarihBitis));
        $query->when($this->filtreKayitTarihBaslangic, fn($q) => $q->whereDate('created_at', '>=', $this->filtreKayitTarihBaslangic));
        $query->when($this->filtreKayitTarihBitis, fn($q) => $q->whereDate('created_at', '<=', $this->filtreKayitTarihBitis));
        $query->when(!empty($this->filtreKategori), function($q) {
            $categories = is_array($this->filtreKategori) ? $this->filtreKategori : [$this->filtreKategori];
            $q->whereIn('sikayet_kategorisi_id', $categories);
        });
        $query->when(!empty($this->filtreKonumTipi), fn($q) => $q->whereIn('konum_tipi', (array) $this->filtreKonumTipi));

        $query->when(!is_null($this->filtrePuanMin) && $this->filtrePuanMin !== '', function ($q) {
            $minPuan = filter_var($this->filtrePuanMin, FILTER_VALIDATE_FLOAT);
            if ($minPuan !== false) {
                $q->where('musteri_puan', '>=', $minPuan);
            }
        });
        $query->when(!is_null($this->filtrePuanMax) && $this->filtrePuanMax !== '', function ($q) {
            $maxPuan = filter_var($this->filtrePuanMax, FILTER_VALIDATE_FLOAT);
            if ($maxPuan !== false) {
                $q->where('musteri_puan', '<=', $maxPuan);
            }
        });

        $query->when(!empty($this->filtreProjeDurumu), function ($q) {
            $q->whereHas('iaaProjesi', function ($subQ) {
                $subQ->whereIn('durum', (array) $this->filtreProjeDurumu);
            });
        });

        $query->when($this->filtreBeklemeMin, function ($q) {
            $q->where('created_at', '<=', now()->subDays($this->filtreBeklemeMin));
        });
        $query->when($this->filtreBeklemeMax, function ($q) {
            $q->where('created_at', '>=', now()->subDays($this->filtreBeklemeMax));
        });

        // === YENİ FİLTRELER ===
        $query->when($this->filtreIadeVar, function ($q) {
            $q->whereHas('iadeler')
              ->whereIn('musteri_durum', ['Tamamlandı', 'Çözümlendi', 'Kapatıldı']);
        });

        $query->when($this->filtreZiyaretVar, function ($q) {
            $q->whereHas('iaaProjesi', function ($subQ) {
                $subQ->where('visit_planned', true);
            });
        });
    }

    protected function applyTabFilter($query)
    {
        if ($this->activeTab === 'cop_kutusu') {
            $query->onlyTrashed();
            return;
        }

        if ($this->activeTab === 'tumu') return;

        $durumGruplari = [
            'yeni' => ['Yeni'],
            'islemde' => [
                'İşlemde', 'İnceleniyor', 'Atandı', 'Devam Ediyor', 'Revize', 'Beklemede',
                'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor',
                'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin'
            ],
            'iptal' => ['İptal Edildi', 'Reddedildi', 'Tamamlanması Reddedildi']
        ];

        if ($this->activeTab == 'talep_kapali') {
            $query->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'talep_olarak_kapatildi'));
        } elseif ($this->activeTab == 'cozulmus') {
            $query->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])
                ->whereDoesntHave('iaaProjesi', fn($q) => $q->whereIn('durum', ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']));
        } elseif ($this->activeTab == 'hatali_bildirim') {
            $query->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'hatali_bildirim_olarak_kapatildi'));
        } elseif ($this->activeTab == 'onay_bekleyenler') {
            $query->whereHas('iaaProjesi', fn($q) => $q->whereIn('durum', [
                'Bölüm Onayı Bekliyor', 
                'Direktör Onayı Bekliyor', 
                'Yönetici Onayı Bekliyor',
                'talep_onayi_bekliyor_kalite',
                'talep_onayi_bekliyor_direktor',
                'talep_onayi_bekliyor_superadmin',
                'hatali_bildirim_onayi_bekliyor_kalite',
                'hatali_bildirim_onayi_bekliyor_direktor',
                'hatali_bildirim_onayi_bekliyor_superadmin'
            ]));
        } elseif (isset($durumGruplari[$this->activeTab])) {
            $secilenDurumlar = $durumGruplari[$this->activeTab];
            $query->where(function ($q) use ($secilenDurumlar) {
                $q->whereIn('musteri_durum', $secilenDurumlar);
                if ($this->activeTab == 'islemde') {
                    $q->orWhereHas('iaaProjesi', function ($subQ) use ($secilenDurumlar) {
                        $subQ->whereIn('durum', $secilenDurumlar);
                    });
                }
            });
        }
    }

    // === YENİ: FİLTRE ÖZETİ (BANNER İÇİN) ===
    public function getFilterSummaryProperty()
    {
        $summary = [];

        if ($this->filtreIadeVar) $summary[] = "♻️ İadeli";
        if ($this->filtreZiyaretVar) $summary[] = "📅 Ziyaretli";
        
        if (!empty($this->filtreDurum)) {
            $summary[] = "Durumu " . implode(', ', (array) $this->filtreDurum);
        }
        
        if (!empty($this->filtreOncelik)) {
            $summary[] = "Önceliği " . implode(', ', (array) $this->filtreOncelik);
        }
        
        if (!empty($this->filtreKategori)) {
            $kategoriAdlari = SikayetKategori::whereIn('id', (array) $this->filtreKategori)->pluck('ad')->toArray();
            $summary[] = "Kategorisi " . implode(', ', $kategoriAdlari);
        }
        
        if (!empty($this->filtreTakim)) {
            $takimAdlari = Takim::whereIn('id', (array) $this->filtreTakim)->pluck('ad')->toArray();
            $summary[] = "Takımı " . implode(', ', $takimAdlari);
        }

        if (!empty($this->filtreProjeDurumu)) {
            $statusMap = [
                'Atandı' => 'Atandı',
                'Bölüm Onayı Bekliyor' => 'Bölüm Onayı',
                'Yönetici Onayı Bekliyor' => 'Yönetici Onayı',
                'Revize Ediliyor' => 'Revize',
                'Tamamlandı' => 'Tamamlandı',
                'talep_olarak_kapatildi' => 'Talep Olarak Kapatıldı',
                'hatali_bildirim_olarak_kapatildi' => 'Hatalı Bildirim',
                'Reddedildi' => 'Reddedildi'
            ];
            $readableStatuses = array_map(fn($s) => $statusMap[$s] ?? $s, (array) $this->filtreProjeDurumu);
            $summary[] = "Proje Durumu " . implode(', ', $readableStatuses);
        }

        if ($this->filtreKayitTarihBaslangic || $this->filtreKayitTarihBitis) {
            $bas = $this->filtreKayitTarihBaslangic ? date('d.m.Y', strtotime($this->filtreKayitTarihBaslangic)) : '...';
            $bit = $this->filtreKayitTarihBitis ? date('d.m.Y', strtotime($this->filtreKayitTarihBitis)) : '...';
            $summary[] = "$bas - $bit tarihleri arasındaki";
        }

        if ($this->filtreKonu) {
            $summary[] = '"' . $this->filtreKonu . '" konusunu içeren';
        }

        if ($this->filtreMusteriAdi) {
            $summary[] = '"' . $this->filtreMusteriAdi . '" müşterisine ait';
        }

        if (($this->filtreBeklemeMin !== null && $this->filtreBeklemeMin !== '') || ($this->filtreBeklemeMax !== null && $this->filtreBeklemeMax !== '')) {
            $min = ($this->filtreBeklemeMin !== null && $this->filtreBeklemeMin !== '') ? $this->filtreBeklemeMin : '0';
            $max = ($this->filtreBeklemeMax !== null && $this->filtreBeklemeMax !== '') ? $this->filtreBeklemeMax : '∞';
            $summary[] = "bekleme süresi $min - $max gün arası olan";
        }

        if (($this->filtrePuanMin !== null && $this->filtrePuanMin !== '') || ($this->filtrePuanMax !== null && $this->filtrePuanMax !== '')) {
            $min = ($this->filtrePuanMin !== null && $this->filtrePuanMin !== '') ? $this->filtrePuanMin : '0';
            $max = ($this->filtrePuanMax !== null && $this->filtrePuanMax !== '') ? $this->filtrePuanMax : '5';
            $summary[] = "puanı $min - $max arası olan";
        }

        if (empty($summary)) return null;

        return implode(', ', $summary) . " şikayetleri görüntülüyorsun.";
    }

    /**
     * Aktif Livewire filtrelerini URL query parametresine çeviren computed property.
     * Export butonlarının (Excel/PDF) filtreli çıktı verebilmesi için kullanılır.
     */
    public function getExportParamsProperty()
    {
        $params = [];

        if (!empty($this->filtreDurum)) $params['filtreDurum'] = $this->filtreDurum;
        if (!empty($this->filtreOncelik)) $params['filtreOncelik'] = $this->filtreOncelik;
        if (!empty($this->filtreTakim)) $params['filtreTakim'] = $this->filtreTakim;
        if (!empty($this->filtreKategori)) $params['filtreKategori'] = $this->filtreKategori;
        if ($this->filtreMusteriAdi) $params['filtreMusteriAdi'] = $this->filtreMusteriAdi;
        if (!empty($this->filtreEkleyen)) $params['filtreEkleyen'] = $this->filtreEkleyen;
        if ($this->filtreSonTarihBaslangic) $params['filtreSonTarihBaslangic'] = $this->filtreSonTarihBaslangic;
        if ($this->filtreSonTarihBitis) $params['filtreSonTarihBitis'] = $this->filtreSonTarihBitis;
        if ($this->filtreKayitTarihBaslangic) $params['filtreKayitTarihBaslangic'] = $this->filtreKayitTarihBaslangic;
        if ($this->filtreKayitTarihBitis) $params['filtreKayitTarihBitis'] = $this->filtreKayitTarihBitis;
        if (!is_null($this->filtrePuanMin) && $this->filtrePuanMin !== '') $params['filtrePuanMin'] = $this->filtrePuanMin;
        if (!is_null($this->filtrePuanMax) && $this->filtrePuanMax !== '') $params['filtrePuanMax'] = $this->filtrePuanMax;
        if (!empty($this->filtreKonumTipi)) $params['filtreKonumTipi'] = $this->filtreKonumTipi;
        if (!empty($this->filtreProjeDurumu)) $params['filtreProjeDurumu'] = $this->filtreProjeDurumu;
        if ($this->filtreBeklemeMin) $params['filtreBeklemeMin'] = $this->filtreBeklemeMin;
        if ($this->filtreBeklemeMax) $params['filtreBeklemeMax'] = $this->filtreBeklemeMax;
        if ($this->filtreKonu) $params['filtreKonu'] = $this->filtreKonu;
        if ($this->filtreIadeVar) $params['filtreIadeVar'] = '1';
        if ($this->filtreZiyaretVar) $params['filtreZiyaretVar'] = '1';
        if ($this->activeTab && $this->activeTab !== 'tumu') $params['activeTab'] = $this->activeTab;

        return $params;
    }

    public function render()
    {
        // 1. Temel Sorgu ve Yetki Filtreleri
        $query = MusteriSikayeti::with([
            'olusturanKurulUyesi',
            'cozumTakimi',
            'sikayetKategori',
            'iaaProjesi.iaaTalebi',
            'iaaProjesi.progressUpdatesViaTalep',
            'dosyalar',
            'loglar' => function ($query) {
                $query->whereIn('eylem', ['Atama Yapıldı (Triyaj)', 'Şikayet Güncellendi (Triyaj)', 'Atama Kaldırıldı'])
                    ->with('user')->latest();
            }
        ]);

        $this->applyAuthorizationFilters($query);

        // === 2. İSTATİSTİKLERİ HESAPLA (AKTİF FİLTRELERE GÖRE) ===
        $baseStatsQuery = clone $query;
        
        // Üstteki sayaçların filtrelerden etkilenmesi için applyFilters'ı stats query'sine de uyguluyoruz
        $this->applyFilters($baseStatsQuery);

        $durumGruplari = [
            'yeni' => ['Yeni'],
            'islemde' => [
                'İşlemde', 'İnceleniyor', 'Atandı', 'Devam Ediyor', 'Revize', 'Beklemede',
                'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor',
                'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin'
            ],
            'iptal' => ['İptal Edildi', 'Reddedildi', 'Tamamlanması Reddedildi']
        ];

        $stats = [
            'tumu' => (clone $baseStatsQuery)->count(),
            'yeni' => (clone $baseStatsQuery)->whereIn('musteri_durum', $durumGruplari['yeni'])->count(),
            'islemde' => (clone $baseStatsQuery)->where(function ($q) use ($durumGruplari) {
                $q->whereIn('musteri_durum', $durumGruplari['islemde'])
                    ->orWhereHas('iaaProjesi', fn($p) => $p->whereIn('durum', $durumGruplari['islemde']));
            })->count(),
            'cozulmus' => (clone $baseStatsQuery)
                ->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])
                ->whereDoesntHave('iaaProjesi', fn($q) => $q->whereIn('durum', ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']))
                ->count(),
            'talep_kapali' => (clone $baseStatsQuery)
                ->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'talep_olarak_kapatildi'))
                ->count(),
            'hatali_bildirim' => (clone $baseStatsQuery)
                ->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'hatali_bildirim_olarak_kapatildi'))
                ->count(),
            'onay_bekleyenler' => (clone $baseStatsQuery)
                ->whereHas('iaaProjesi', fn($q) => $q->whereIn('durum', [
                    'Bölüm Onayı Bekliyor', 
                    'Direktör Onayı Bekliyor', 
                    'Yönetici Onayı Bekliyor',
                    'talep_onayi_bekliyor_kalite',
                    'talep_onayi_bekliyor_direktor',
                    'talep_onayi_bekliyor_superadmin',
                    'hatali_bildirim_onayi_bekliyor_kalite',
                    'hatali_bildirim_onayi_bekliyor_direktor',
                    'hatali_bildirim_onayi_bekliyor_superadmin'
                ]))
                ->count(),
            'iptal' => (clone $baseStatsQuery)->whereIn('musteri_durum', $durumGruplari['iptal'])->count(),
        ];

        // === 3. FİLTRELERİ VE SEKME MANTIĞINI UYGULA ===
        $this->applyFilters($query);
        $this->applyTabFilter($query);

        // Sonuçları Getir
        $sikayetler = $query->latest()->paginate(10);

        // View Bileşenleri
        $cozumTakimlari = Takim::where('tur', 'sikayet')->orderBy('ad')->get();
        $ekleyenUserIds = MusteriSikayeti::whereNotNull('olusturan_kurul_uyesi_id')->distinct()->pluck('olusturan_kurul_uyesi_id');
        $ekleyenKullanicilar = User::whereIn('id', $ekleyenUserIds)->orderBy('name')->get();
        $kategoriler = SikayetKategori::orderBy('ad')->get();

        return view('livewire.admin.sikayetler-tablosu', [
            'sikayetler' => $sikayetler,
            'stats' => $stats,
            'cozumTakimlari' => $cozumTakimlari,
            'ekleyenKullanicilar' => $ekleyenKullanicilar,
            'kategoriler' => $kategoriler,
        ]);
    }
}