<?php

namespace App\Livewire\Admin;

use App\Models\MusteriSikayeti;
use App\Models\Takim;
use App\Models\User;
use App\Models\Iaa; // Bunu ekledik (Squad kontrolü için)
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\SikayetKategori;
use Illuminate\Support\Facades\Auth;

class SikayetlerTablosu extends Component
{
    use WithPagination;

    // ... (Filtre property'leri aynı) ...
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
    public $filtreProjeDurumu = ''; // Yeni
    public $filtreBeklemeMin = '';  // Yeni
    public $filtreBeklemeMax = '';  // Yeni

    protected $listeners = ['sikayetGuncellendi' => '$refresh'];

    public function updated($propertyName)
    {
        if (in_array($propertyName, [
            'filtreDurum', 'filtreOncelik', 'filtreTakim', 'filtreKategori', 'filtreKonumTipi',
            'filtreMusteriAdi', 'filtreEkleyen',
            'filtreSonTarihBaslangic', 'filtreSonTarihBitis',
            'filtreKayitTarihBaslangic', 'filtreKayitTarihBitis',
            'filtrePuanMin', 'filtrePuanMax',
            'filtreProjeDurumu', 'filtreBeklemeMin', 'filtreBeklemeMax'
            ])) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset([
            'filtreDurum', 'filtreOncelik', 'filtreTakim', 'filtreKategori', 'filtreKonumTipi',
            'filtreMusteriAdi', 'filtreEkleyen',
            'filtreSonTarihBaslangic', 'filtreSonTarihBitis',
            'filtreKayitTarihBaslangic', 'filtreKayitTarihBitis',
            'filtrePuanMin', 'filtrePuanMax',
            'filtreProjeDurumu', 'filtreBeklemeMin', 'filtreBeklemeMax'
        ]);
        $this->resetPage();
    }
      

    public function render()
    {
        $user = Auth::user();
        
        $query = MusteriSikayeti::with([
            'olusturanKurulUyesi',
            'cozumTakimi',
            'sikayetKategori',
            'iaaProjesi',
            'loglar' => function ($query) {
                $query->whereIn('eylem', ['Atama Yapıldı (Triyaj)', 'Şikayet Güncellendi (Triyaj)', 'Atama Kaldırıldı'])
                      ->with('user') 
                      ->latest(); 
            }
        ]);

        // === YETKİ VE FİLTRELEME MANTIĞI ===

        // 1. SÜPER YETKİLİLER
        if ($user->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Yonetim'])) {
            // Hepsini görür
        } 
        
        // 2. BÖLÜM KALİTE YÖNETİCİSİ
        elseif ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            $yonettigiKategoriIds = $user->yonettigiSikayetKategorileri->pluck('id');
            if ($yonettigiKategoriIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('sikayet_kategorisi_id', $yonettigiKategoriIds);
            }
        }

        // 3. [YENİ] BÖLÜM LİDERİ (EMRAH AL İÇİN ÖZEL MANTIK)
        elseif ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
            
            $bolumId = $user->bolum_id;
            
            // Emrah'ın bölümündeki personellerin ID'leri
            $personelIds = User::where('bolum_id', $bolumId)->pluck('id');

            $query->where(function($q) use ($bolumId, $personelIds) {
                
                // Kural A: Kendi Bölümünün Şikayetleri
                $q->whereHas('sikayetKategori', function($subQ) use ($bolumId) {
                    $subQ->where('bolum_id', $bolumId);
                })
                
                // Kural B: Kendi Personelinin Görev Aldığı Şikayetler (Squad)
                ->orWhereHas('iaaProjesi', function($subQ) use ($personelIds) {
                    $subQ->whereHas('projeEkibi', function($squadQ) use ($personelIds) {
                        $squadQ->whereIn('users.id', $personelIds)
                               ->where('iaa_user.durum', 'onaylandi');
                    });
                });
            });
        }

        // 4. ÇÖZÜM LİDERİ
        elseif ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            $lideriOlduguTakimIds = $user->lideriOlduguTakimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            if ($lideriOlduguTakimIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('atanan_cozum_takimi_id', $lideriOlduguTakimIds);
            }
        } 
        
        // 5. STANDART KULLANICI / TAKIM ÜYESİ
        else {
            $uyesiOlduguTakimIds = $user->takimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            if ($uyesiOlduguTakimIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('atanan_cozum_takimi_id', $uyesiOlduguTakimIds);
            }
        }
        // === YETKİ SONU ===


        // İstatistikler
        $statsQuery = clone $query;
        $stats = [
            'toplam' => $statsQuery->count(),
            'beklemede' => (clone $statsQuery)->where('musteri_durum', 'Yeni')->count(),
            'islemde' => (clone $statsQuery)->where('musteri_durum', 'İşlemde')->count(),
            'cozulmus' => (clone $statsQuery)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(),
        ];

        // Dropdown Verileri
        $cozumTakimlari = Takim::where('tur', 'sikayet')->orderBy('ad')->get();
        $ekleyenUserIds = MusteriSikayeti::whereNotNull('olusturan_kurul_uyesi_id')->distinct()->pluck('olusturan_kurul_uyesi_id');
        $ekleyenKullanicilar = User::whereIn('id', $ekleyenUserIds)->orderBy('name')->get();
        $kategoriler = SikayetKategori::orderBy('ad')->get();
        

        // Filtreleri Uygula
        $query->when($this->filtreDurum, fn ($q) => $q->where('musteri_durum', $this->filtreDurum));
        $query->when($this->filtreOncelik, fn ($q) => $q->where('musteri_oncelik', $this->filtreOncelik));
        $query->when($this->filtreTakim, fn ($q) => $q->where('atanan_cozum_takimi_id', $this->filtreTakim));
        $query->when($this->filtreMusteriAdi, fn ($q) => $q->where('musteri_adi', 'like', '%' . $this->filtreMusteriAdi . '%'));
        $query->when($this->filtreEkleyen, fn ($q) => $q->where('olusturan_kurul_uyesi_id', $this->filtreEkleyen));
        $query->when($this->filtreSonTarihBaslangic, fn ($q) => $q->whereDate('musteri_cozum_son_tarihi', '>=', $this->filtreSonTarihBaslangic));
        $query->when($this->filtreSonTarihBitis, fn ($q) => $q->whereDate('musteri_cozum_son_tarihi', '<=', $this->filtreSonTarihBitis));
        $query->when($this->filtreKayitTarihBaslangic, fn ($q) => $q->whereDate('created_at', '>=', $this->filtreKayitTarihBaslangic));
        $query->when($this->filtreKayitTarihBitis, fn ($q) => $q->whereDate('created_at', '<=', $this->filtreKayitTarihBitis));
        $query->when($this->filtreKategori, fn ($q) => $q->where('sikayet_kategorisi_id', $this->filtreKategori));
        $query->when(!is_null($this->filtrePuanMin) && $this->filtrePuanMin !== '', function ($q) {
            $minPuan = filter_var($this->filtrePuanMin, FILTER_VALIDATE_FLOAT);
            if ($minPuan !== false) { $q->where('musteri_puan', '>=', $minPuan); }
        });
        $query->when(!is_null($this->filtrePuanMax) && $this->filtrePuanMax !== '', function ($q) {
             $maxPuan = filter_var($this->filtrePuanMax, FILTER_VALIDATE_FLOAT);
             if ($maxPuan !== false) { $q->where('musteri_puan', '<=', $maxPuan); }
        });
        $query->when($this->filtreKonumTipi, fn ($q) => $q->where('konum_tipi', $this->filtreKonumTipi));

        // === YENİ FİLTRE MANTIKLARI BURAYA ===

        // 1. PROJE DURUMU FİLTRESİ
        // Şikayete bağlı "iaaProjesi" tablosuna gidip "durum" kolonunu kontrol eder.
        $query->when($this->filtreProjeDurumu, function ($q) {
            $q->whereHas('iaaProjesi', function ($subQ) {
                $subQ->where('durum', $this->filtreProjeDurumu);
            });
        });

        // 2. BEKLEME SÜRESİ (MİNİMUM GÜN)
        // Örnek: "5" yazarsan, 5 gün ve daha eskileri getirir.
        $query->when($this->filtreBeklemeMin, function ($q) {
            $q->where('created_at', '<=', now()->subDays($this->filtreBeklemeMin));
        });

        // 3. BEKLEME SÜRESİ (MAKSİMUM GÜN)
        // Örnek: "30" yazarsan, son 30 gün içindekileri getirir.
        $query->when($this->filtreBeklemeMax, function ($q) {
            $q->where('created_at', '>=', now()->subDays($this->filtreBeklemeMax));
        });

        // ======================================

        $sikayetler = $query->latest()->paginate(10);

        return view('livewire.admin.sikayetler-tablosu', [
            'sikayetler' => $sikayetler,
            'stats' => $stats,
            'cozumTakimlari' => $cozumTakimlari,
            'ekleyenKullanicilar' => $ekleyenKullanicilar,
            'kategoriler' => $kategoriler,
        ]);
    }
}