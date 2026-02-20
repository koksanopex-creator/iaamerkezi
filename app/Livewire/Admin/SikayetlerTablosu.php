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
    public $filtreDurum = '';
    public $filtreOncelik = '';
    public $filtreTakim = '';
    public $filtreKategori = '';
    public $filtreMusteriAdi = '';
    public $filtreEkleyen = '';
    public $filtreSonTarihBaslangic = '';
    public $filtreSonTarihBitis = '';
    public $filtreKayitTarihBaslangic = '';
    public $filtreKayitTarihBitis = '';
    public $filtrePuanMin = null;
    public $filtrePuanMax = null;
    public $filtreKonumTipi = '';
    public $filtreProjeDurumu = '';
    public $filtreBeklemeMin = '';
    public $filtreBeklemeMax = '';
    public $filtreKonu = '';
    public $viewMode = 'card'; // 'card' veya 'list'

    public $activeTab = 'tumu'; // Varsayılan sekme

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

    public function resetFilters()
    {
        $this->reset();
        $this->activeTab = 'tumu'; // Resetleyince sekmeyi de başa al
        $this->resetPage();
    }

    // === YENİ: SEKME DEĞİŞTİRME FONKSİYONU ===
    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->filtreDurum = ''; // Sekme değişince detaylı durum filtresini temizle
        $this->resetPage();
    }

    public function delete($id)
    {
        $sikayet = MusteriSikayeti::find($id);
        if ($sikayet) {
            $sikayet->delete();
            session()->flash('message', 'Şikayet başarıyla silindi.');
            $this->dispatch('close-modal');
        }
    }

    public function render()
    {
        $user = Auth::user();

        // 1. Temel Sorgu
        $query = MusteriSikayeti::with([
            'olusturanKurulUyesi',
            'cozumTakimi',
            'sikayetKategori',
            'iaaProjesi',
            'loglar' => function ($query) {
                $query->whereIn('eylem', ['Atama Yapıldı (Triyaj)', 'Şikayet Güncellendi (Triyaj)', 'Atama Kaldırıldı'])
                    ->with('user')->latest();
            }
        ]);

        // === 2. DURUM GRUPLARI (Basit eşleşmeler için) ===
        $durumGruplari = [
            'yeni' => ['Yeni'],
            'islemde' => [
                'İşlemde',
                'İnceleniyor',
                'Atandı',
                'Devam Ediyor',
                'Revize',
                'Beklemede',
                'Bölüm Onayı Bekliyor',
                'Direktör Onayı Bekliyor', // EKLENDİ
                'Yönetici Onayı Bekliyor',
                'talep_onayi_bekliyor_kalite',
                'talep_onayi_bekliyor_superadmin'
            ],
            // 'cozulmus' ve 'talep_kapali' için özel sorgu yazacağız, buraya koymuyoruz.
            'iptal' => ['İptal Edildi', 'Reddedildi', 'Tamamlanması Reddedildi']
        ];

        // === 3. YETKİ KONTROLÜ (Mevcut kodunuz - dokunmuyoruz) ===
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
        } else {
            $uyesiOlduguTakimIds = $user->takimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            if ($uyesiOlduguTakimIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('atanan_cozum_takimi_id', $uyesiOlduguTakimIds);
            }
        }

        // === 4. İSTATİSTİKLERİ HESAPLA (Düzeltilmiş Mantık) ===
        $baseStatsQuery = clone $query;

        $stats = [
            'tumu' => (clone $baseStatsQuery)->count(),

            'yeni' => (clone $baseStatsQuery)->whereIn('musteri_durum', $durumGruplari['yeni'])->count(),

            'islemde' => (clone $baseStatsQuery)->where(function ($q) use ($durumGruplari) {
                $q->whereIn('musteri_durum', $durumGruplari['islemde'])
                    ->orWhereHas('iaaProjesi', fn($p) => $p->whereIn('durum', $durumGruplari['islemde']));
            })->count(),

            // ÇÖZÜLMÜŞ: Durumu Kapatıldı/Tamamlandı olanlar AMA projesi 'talep_olarak_kapatildi' VEYA 'hatali_bildirim_olarak_kapatildi' OLMAYANLAR
            'cozulmus' => (clone $baseStatsQuery)
                ->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])
                ->whereDoesntHave('iaaProjesi', fn($q) => $q->whereIn('durum', ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']))
                ->count(),

            // TALEP KAPALI: Sadece projesi 'talep_olarak_kapatildi' olanlar
            'talep_kapali' => (clone $baseStatsQuery)
                ->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'talep_olarak_kapatildi'))
                ->count(),

            // === YENİ: HATALI BİLDİRİM (KIRMIZI/GRİ) ===
            'hatali_bildirim' => (clone $baseStatsQuery)
                ->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'hatali_bildirim_olarak_kapatildi'))
                ->count(),

            // === YENİ: ONAY BEKLEYENLER (Mor/Turuncu) ===
            'onay_bekleyenler' => (clone $baseStatsQuery)
                ->whereHas('iaaProjesi', fn($q) => $q->whereIn('durum', ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])) // EKLENDİ
                ->count(),

            'iptal' => (clone $baseStatsQuery)->whereIn('musteri_durum', $durumGruplari['iptal'])->count(),
        ];

        // === 5. AKTİF SEKME FİLTRESİ (Düzeltilmiş Mantık) ===
        if ($this->activeTab !== 'tumu') {

            // A) ÖZEL SEKMELER
            if ($this->activeTab == 'talep_kapali') {
                // Şikayet tablosuna değil, İLİŞKİLİ TABLOYA bakıyoruz
                $query->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'talep_olarak_kapatildi'));

            } elseif ($this->activeTab == 'cozulmus') {
                // ÇÖZÜLMÜŞLER: Durumu Kapatıldı/Tamamlandı olanlar AMA projesi 'talep_olarak_kapatildi' VEYA 'hatali_bildirim_olarak_kapatildi' OLMAYANLAR
                $query->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])
                    ->whereDoesntHave('iaaProjesi', fn($q) => $q->whereIn('durum', ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']));

            } elseif ($this->activeTab == 'hatali_bildirim') {
                // HATALI BİLDİRİM SEKME MANTIĞI
                $query->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'hatali_bildirim_olarak_kapatildi'));

                // === YENİ: ONAY BEKLEYENLER SEKME MANTIĞI ===
            } elseif ($this->activeTab == 'onay_bekleyenler') {
                $query->whereHas('iaaProjesi', fn($q) => $q->whereIn('durum', ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])); // EKLENDİ

                // B) STANDART GRUPLAR (Yeni, İptal)
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

        // === 6. DİĞER FİLTRELER ===
        // ... (Bu kısım aynı kalıyor) ...
        $query->when($this->filtreDurum, fn($q) => $q->where('musteri_durum', $this->filtreDurum));
        $query->when($this->filtreOncelik, fn($q) => $q->where('musteri_oncelik', $this->filtreOncelik));
        $query->when($this->filtreTakim, fn($q) => $q->where('atanan_cozum_takimi_id', $this->filtreTakim));
        $query->when($this->filtreMusteriAdi, fn($q) => $q->where('musteri_adi', 'like', '%' . $this->filtreMusteriAdi . '%'));
        $query->when(strlen($this->filtreKonu) >= 2, fn($q) => $q->where('musteri_sikayet_konusu', 'like', '%' . $this->filtreKonu . '%'));
        $query->when($this->filtreEkleyen, fn($q) => $q->where('olusturan_kurul_uyesi_id', $this->filtreEkleyen));
        $query->when($this->filtreSonTarihBaslangic, fn($q) => $q->whereDate('musteri_cozum_son_tarihi', '>=', $this->filtreSonTarihBaslangic));
        $query->when($this->filtreSonTarihBitis, fn($q) => $q->whereDate('musteri_cozum_son_tarihi', '<=', $this->filtreSonTarihBitis));
        $query->when($this->filtreKayitTarihBaslangic, fn($q) => $q->whereDate('created_at', '>=', $this->filtreKayitTarihBaslangic));
        $query->when($this->filtreKayitTarihBitis, fn($q) => $q->whereDate('created_at', '<=', $this->filtreKayitTarihBitis));
        $query->when($this->filtreKategori, fn($q) => $q->where('sikayet_kategorisi_id', $this->filtreKategori));
        $query->when($this->filtreKonumTipi, fn($q) => $q->where('konum_tipi', $this->filtreKonumTipi));

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

        $query->when($this->filtreProjeDurumu, function ($q) {
            $q->whereHas('iaaProjesi', function ($subQ) {
                $subQ->where('durum', $this->filtreProjeDurumu);
            });
        });

        $query->when($this->filtreBeklemeMin, function ($q) {
            $q->where('created_at', '<=', now()->subDays($this->filtreBeklemeMin));
        });
        $query->when($this->filtreBeklemeMax, function ($q) {
            $q->where('created_at', '>=', now()->subDays($this->filtreBeklemeMax));
        });

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