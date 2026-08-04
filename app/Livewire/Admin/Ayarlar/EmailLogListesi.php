<?php

namespace App\Livewire\Admin\Ayarlar;

use App\Models\EmailLog;
use Livewire\Component;
use Livewire\WithPagination;

class EmailLogListesi extends Component
{
    use WithPagination;

    public $search = '';
    public $kategori = '';
    public $baslangicTarihi = '';
    public $bitisTarihi = '';
    public $perPage = 20;

    // Toplu silme için
    public $seciliLoglar = [];
    public $silmePeriodu = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'kategori' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingKategori()
    {
        $this->resetPage();
    }

    public function updatingBaslangicTarihi()
    {
        $this->resetPage();
    }

    public function updatingBitisTarihi()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // Seçilenleri temizle (sayfa değiştiğinde vs.)
    public function updatedPage()
    {
        $this->seciliLoglar = [];
    }

    // Tekli Silme
    public function sil($id)
    {
        EmailLog::findOrFail($id)->delete();
        $this->dispatch('bildirim', ['mesaj' => 'Log başarıyla silindi.', 'tur' => 'success']);
    }

    // Toplu Silme
    public function secilileriSil()
    {
        if (count($this->seciliLoglar) > 0) {
            EmailLog::whereIn('id', $this->seciliLoglar)->delete();
            $this->seciliLoglar = [];
            $this->resetPage();
            $this->dispatch('bildirim', ['mesaj' => 'Seçili loglar başarıyla silindi.', 'tur' => 'success']);
        }
    }

    // Periyodik Silme
    public function periyodikSil()
    {
        if (!$this->silmePeriodu) return;

        $tarih = now();
        if ($this->silmePeriodu == '1_ay') {
            $tarih->subMonth();
        } elseif ($this->silmePeriodu == '3_ay') {
            $tarih->subMonths(3);
        } elseif ($this->silmePeriodu == '6_ay') {
            $tarih->subMonths(6);
        } elseif ($this->silmePeriodu == '1_yil') {
            $tarih->subYear();
        }

        $silinenCount = EmailLog::where('created_at', '<', $tarih)->delete();
        
        $this->silmePeriodu = '';
        $this->resetPage();
        
        $this->dispatch('bildirim', ['mesaj' => $silinenCount . ' adet eski log başarıyla silindi.', 'tur' => 'success']);
    }

    public function render()
    {
        $query = EmailLog::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('alici_email', 'like', '%' . $this->search . '%')
                  ->orWhere('konu', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->kategori) {
            $query->where('kategori', $this->kategori);
        }
        
        if ($this->baslangicTarihi) {
            $query->whereDate('created_at', '>=', $this->baslangicTarihi);
        }
        
        if ($this->bitisTarihi) {
            $query->whereDate('created_at', '<=', $this->bitisTarihi);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        // İstatistikler
        $stats = [
            'toplam' => EmailLog::count(),
            'bugun' => EmailLog::whereDate('created_at', today())->count(),
            'bu_hafta' => EmailLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'bu_ay' => EmailLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        $kategoriler = EmailLog::select('kategori')
            ->distinct()
            ->whereNotNull('kategori')
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('livewire.admin.ayarlar.email-log-listesi', [
            'logs' => $logs,
            'kategoriler' => $kategoriler,
            'stats' => $stats,
        ]);
    }
}
