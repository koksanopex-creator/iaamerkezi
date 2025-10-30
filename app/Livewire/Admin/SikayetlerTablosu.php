<?php

namespace App\Livewire\Admin;

use App\Models\MusteriSikayeti;
use App\Models\Takim;
use App\Models\User; // <-- YENİ: Kullanıcıları çekmek için eklendi
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB; // <-- YENİ: Distinct kullanıcıları çekmek için eklendi
use App\Models\SikayetKategori; // <-- YENİ: Kategori modelini ekle


class SikayetlerTablosu extends Component
{
    use WithPagination;

    // Mevcut Filtreler
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
    // ===============================================

    // Modal'dan gelen 'sikayetGuncellendi' event'ini dinler
    protected $listeners = ['sikayetGuncellendi' => '$refresh'];

    /**
     * Filtrelerden herhangi biri güncellendiğinde, 1. sayfaya dön.
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, [
            'filtreDurum', 'filtreOncelik', 'filtreTakim', 'filtreKategori', 'filtreKonumTipi',
            'filtreMusteriAdi', 'filtreEkleyen',
            'filtreSonTarihBaslangic', 'filtreSonTarihBitis',
            'filtreKayitTarihBaslangic', 'filtreKayitTarihBitis',
            'filtrePuanMin', 'filtrePuanMax' // <-- Eklendi
            ])) {
            $this->resetPage();
        }
    }

    /**
     * === YENİ EKLENDİ: Filtreleri Sıfırlama Fonksiyonu ===
     */
    public function resetFilters()
    {
        // === $filtrePuanMin ve $filtrePuanMax eklendi ===
        $this->reset([
            'filtreDurum', 'filtreOncelik', 'filtreTakim', 'filtreKategori', 'filtreKonumTipi',
            'filtreMusteriAdi', 'filtreEkleyen',
            'filtreSonTarihBaslangic', 'filtreSonTarihBitis',
            'filtreKayitTarihBaslangic', 'filtreKayitTarihBitis',
            'filtrePuanMin', 'filtrePuanMax' // <-- Eklendi
        ]);
        $this->resetPage();
    }
     // =======================================================


    public function render()
    {
        // İstatistikler (Aynı kaldı)
        $stats = [
            'toplam' => MusteriSikayeti::count(),
            'beklemede' => MusteriSikayeti::where('musteri_durum', 'Yeni')->count(),
            'islemde' => MusteriSikayeti::where('musteri_durum', 'İşlemde')->count(),
            'cozulmus' => MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(),
        ];

        // Çözüm takımları (Aynı kaldı)
        $cozumTakimlari = Takim::where('tur', 'sikayet')->orderBy('ad')->get();

        // === YENİ EKLENDİ: 'Ekleyen' filtresi için kullanıcı listesini al ===
        // Sadece şikayet oluşturan kullanıcıların ID'lerini al
        $ekleyenUserIds = MusteriSikayeti::whereNotNull('olusturan_kurul_uyesi_id')
                                         ->distinct()
                                         ->pluck('olusturan_kurul_uyesi_id');
        // Bu ID'lere sahip kullanıcıları isim sırasına göre çek
        $ekleyenKullanicilar = User::whereIn('id', $ekleyenUserIds)->orderBy('name')->get();
        // ====================================================================
        $kategoriler = SikayetKategori::orderBy('ad')->get();
        /**
         * === SORGULAMA GÜNCELLENDİ ===
         * Yeni filtre koşulları eklendi.
         */
        $query = MusteriSikayeti::with([
            'olusturanKurulUyesi',
            'cozumTakimi',
            'sikayetKategori' // <-- YENİ EKLENDİ
        ]);

        // Mevcut Filtreler
        $query->when($this->filtreDurum, fn ($q) => $q->where('musteri_durum', $this->filtreDurum));
        $query->when($this->filtreOncelik, fn ($q) => $q->where('musteri_oncelik', $this->filtreOncelik));
        $query->when($this->filtreTakim, fn ($q) => $q->where('atanan_cozum_takimi_id', $this->filtreTakim)); // Bu satırı önceki adımda düzeltmiştik

        // === YENİ FİLTRELER ===
        // 4. Müşteri Adı Filtresi (Arama)
        $query->when($this->filtreMusteriAdi, fn ($q) => $q->where('musteri_adi', 'like', '%' . $this->filtreMusteriAdi . '%'));

        // 5. Ekleyen Kişi Filtresi
        $query->when($this->filtreEkleyen, fn ($q) => $q->where('olusturan_kurul_uyesi_id', $this->filtreEkleyen));

        // 6. Son Tarih Aralığı Filtresi
        $query->when($this->filtreSonTarihBaslangic, fn ($q) => $q->whereDate('musteri_cozum_son_tarihi', '>=', $this->filtreSonTarihBaslangic));
        $query->when($this->filtreSonTarihBitis, fn ($q) => $q->whereDate('musteri_cozum_son_tarihi', '<=', $this->filtreSonTarihBitis));

        // 7. Kayıt Tarihi Aralığı Filtresi
        $query->when($this->filtreKayitTarihBaslangic, fn ($q) => $q->whereDate('created_at', '>=', $this->filtreKayitTarihBaslangic));
        $query->when($this->filtreKayitTarihBitis, fn ($q) => $q->whereDate('created_at', '<=', $this->filtreKayitTarihBitis));

        // 8. Şikayet Kategorisi Filtresi
        $query->when($this->filtreKategori, fn ($q) => $q->where('sikayet_kategorisi_id', $this->filtreKategori));

        // 9. === YENİ EKLENDİ: Puan Aralığı Filtresi ===
        // Minimum Puan (Sadece null veya boş değilse ve sayıysa uygula)
        $query->when(!is_null($this->filtrePuanMin) && $this->filtrePuanMin !== '', function ($q) {
            // Güvenlik için sayıya çevirelim
            $minPuan = filter_var($this->filtrePuanMin, FILTER_VALIDATE_FLOAT);
            if ($minPuan !== false) {
                 $q->where('musteri_puan', '>=', $minPuan);
            }
        });
        // Maksimum Puan (Sadece null veya boş değilse ve sayıysa uygula)
        $query->when(!is_null($this->filtrePuanMax) && $this->filtrePuanMax !== '', function ($q) {
             // Güvenlik için sayıya çevirelim
            $maxPuan = filter_var($this->filtrePuanMax, FILTER_VALIDATE_FLOAT);
             if ($maxPuan !== false) {
                $q->where('musteri_puan', '<=', $maxPuan);
             }
        });

        // 10. Konum Tipi Filtresi
        $query->when($this->filtreKonumTipi, fn ($q) => $q->where('konum_tipi', $this->filtreKonumTipi));
        // ============================================

        // Sorguyu tamamla
        $sikayetler = $query->latest()->paginate(10);
        // ========================================================

        return view('livewire.admin.sikayetler-tablosu', [
            'sikayetler' => $sikayetler,
            'stats' => $stats,
            'cozumTakimlari' => $cozumTakimlari,
            'ekleyenKullanicilar' => $ekleyenKullanicilar,
            'kategoriler' => $kategoriler, // <-- YENİ: kategorileri  view'e gönder
        ]);
    }
}