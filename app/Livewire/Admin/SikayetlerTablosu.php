<?php

namespace App\Livewire\Admin;

use App\Models\MusteriSikayeti;
use App\Models\Takim;
use App\Models\User;
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

    protected $listeners = ['sikayetGuncellendi' => '$refresh'];

    // ... (updated ve resetFilters metodları aynı) ...
    public function updated($propertyName)
    {
        if (in_array($propertyName, [
            'filtreDurum', 'filtreOncelik', 'filtreTakim', 'filtreKategori', 'filtreKonumTipi',
            'filtreMusteriAdi', 'filtreEkleyen',
            'filtreSonTarihBaslangic', 'filtreSonTarihBitis',
            'filtreKayitTarihBaslangic', 'filtreKayitTarihBitis',
            'filtrePuanMin', 'filtrePuanMax'
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
            'filtrePuanMin', 'filtrePuanMax'
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
            // === YENİ EKLENDİ (Faz 2) ===
            // Her şikayetin "Yönet" modalı loglarını ve logu atan kullanıcıyı da çek
            'loglar' => function ($query) {
                $query->whereIn('eylem', ['Atama Yapıldı (Triyaj)', 'Şikayet Güncellendi (Triyaj)', 'Atama Kaldırıldı'])
                      ->with('user') // Logu yapan kullanıcıyı da al
                      ->latest(); // En yeniler üste
            }
            // === YENİ EKLENDİ SONU ===
        ]);

        // === YENİ YETKİ VE FİLTRELEME MANTIĞI ===
        if ($user->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu'])) {
            // Admin ve Kurul tüm şikayetleri görür.
        } 
        elseif ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            // Çözüm Lideri, SADECE lideri olduğu 'sikayet' takımlarının şikayetlerini görür.
            $lideriOlduguTakimIds = $user->lideriOlduguTakimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            if ($lideriOlduguTakimIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('atanan_cozum_takimi_id', $lideriOlduguTakimIds);
            }
        } 
        else {
            // Normal Kullanıcı, SADECE üyesi olduğu 'sikayet' takımlarının şikayetlerini görür.
            
            // === HATA DÜZELTMESİ (Ambiguous ID) ===
            // pluck('id') -> pluck('takimlar.id') olarak değiştirildi.
            $uyesiOlduguTakimIds = $user->takimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            // === HATA DÜZELTMESİ SONU ===

            if ($uyesiOlduguTakimIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('atanan_cozum_takimi_id', $uyesiOlduguTakimIds);
            }
        }
        // === YETKİ VE FİLTRELEME SONU ===


        // İstatistikler (Filtrelenmiş sorgu üzerinden hesaplanır)
        $statsQuery = clone $query;
        $stats = [
            'toplam' => $statsQuery->count(),
            'beklemede' => (clone $statsQuery)->where('musteri_durum', 'Yeni')->count(),
            'islemde' => (clone $statsQuery)->where('musteri_durum', 'İşlemde')->count(),
            'cozulmus' => (clone $statsQuery)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(),
        ];

        // Dropdown'lar için veriler
        $cozumTakimlari = Takim::where('tur', 'sikayet')->orderBy('ad')->get();
        $ekleyenUserIds = MusteriSikayeti::whereNotNull('olusturan_kurul_uyesi_id')
                                          ->distinct()
                                          ->pluck('olusturan_kurul_uyesi_id');
        $ekleyenKullanicilar = User::whereIn('id', $ekleyenUserIds)->orderBy('name')->get();
        $kategoriler = SikayetKategori::orderBy('ad')->get();
        

        // --- KULLANICI FİLTRELERİNİ UYGULA ---
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
        // --- FİLTRELEME SONU ---

        // Sorguyu tamamla
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