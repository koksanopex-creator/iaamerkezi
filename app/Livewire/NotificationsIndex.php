<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationsIndex extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $status = 'all'; // all, read, unread
    public $search = ''; // Anahtar kelime arama

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'status' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
    }

    public function updatingStartDate() { $this->resetPage(); }
    public function updatingEndDate() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public function toggleRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            if ($notification->read_at) {
                $notification->update(['read_at' => null]);
            } else {
                $notification->markAsRead();
            }
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->dispatch('notificationsMarkedAsRead'); 
    }

    public function clearFilters()
    {
        $this->reset(['startDate', 'endDate', 'status', 'search']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Auth::user()->notifications()
            ->latest() // Equivalent to orderBy('created_at', 'desc')
            ->when($this->search, function ($q) {
                // Laravel JSON path araması ile daha isabetli (karakter kodlamasından bağımsız) arama yapalım
                $search = $this->search;
                $q->where(function ($sq) use ($search) {
                    $sq->where('data->message', 'like', '%' . $search . '%')
                       ->orWhere('data', 'like', '%' . $search . '%');
                });
            })
            ->when($this->status, function ($q) {
                if ($this->status === 'read') {
                    $q->whereNotNull('read_at');
                } elseif ($this->status === 'unread') {
                    $q->whereNull('read_at');
                }
            })
            ->when($this->startDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate);
            });

        $notifications = $query->paginate(15);

        // Bölüm bazlı renk paleti (Premium Renkler)
        $deptColorMap = [
            'Preform' => 'blue',
            'Kapak' => 'emerald',
            'Kalite' => 'rose',
            'Hukuk' => 'violet',
            'Sistem' => 'slate',
            'Yönetim' => 'amber',
            'Genel' => 'slate',
            'Müşteri' => 'orange',
            'Satın Alma' => 'cyan',
            'Planlama' => 'indigo',
        ];

        // Bildirimler için Departman ve Tip Bilgisini Eklentleyelim
        $notifications->getCollection()->transform(function ($notification) use ($deptColorMap) {
            $data = $notification->data;
            $notifClass = $notification->type;
            $typeLabel = 'İAA Projesi';
            $dept = 'Genel';
            $color = 'indigo';

            $sikayetId = $data['sikayet_id'] ?? null;
            $iaaId = $data['iaa_id'] ?? null;

            // 1. Şikayet Üzerinden Kontrol (Öncelikli)
            if ($sikayetId) {
                $sikayet = \App\Models\MusteriSikayeti::with('sikayetKategori.bolum')->find($sikayetId);
                if ($sikayet) {
                    $typeLabel = 'Müşteri Şikayeti';
                    $color = 'red';
                    if ($sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum) {
                        $dept = $sikayet->sikayetKategori->bolum->ad;
                    }
                }
            } 
            // 2. İAA Üzerinden Kontrol (Şikayet ID yoksa veya ek bilgi lazımsa)
            elseif ($iaaId) {
                $iaa = \App\Models\Iaa::with(['bolum', 'musteriSikayeti.sikayetKategori.bolum'])->find($iaaId);
                if ($iaa) {
                    $dept = optional($iaa->bolum)->ad ?? 'Genel';
                    
                    // ÖNEMLİ: Iaa modelinde musteri_sikayeti_id kolonu yoktur, ilişki hasOne (şikayet tablosunda iaa_id) şeklindedir.
                    if ($iaa->musteriSikayeti) {
                        $typeLabel = 'Müşteri Şikayeti';
                        $color = 'red';
                        // Eğer İAA bölümü genel kalmışsa, şikayetin bağlı olduğu gerçek bölümü baz al
                        if ($dept == 'Genel' && $iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori) {
                            $dept = optional($iaa->musteriSikayeti->sikayetKategori->bolum)->ad ?? $dept;
                        }
                    }
                }
            }

            // 3. Disiplin / Kurul Bildirimleri Kontrolü (İlgili anahtar kelimelerden biri varsa mor etiket yap)
            if (str_contains($notifClass, 'Disiplin') || 
                str_contains($notifClass, 'Toplantı') || 
                str_contains($notifClass, 'Toplanti') ||
                str_contains($notifClass, 'Board') ||
                ($data['category'] ?? '') === 'disiplin') {
                $typeLabel = $data['label'] ?? 'Kurul Toplantısı';
                $dept = 'Hukuk';
                $color = 'violet';
            } 
            // 4. Davet Kontrolü (Disiplin değilse)
            elseif (str_contains($notifClass, 'Squad') || str_contains($notifClass, 'Davet')) {
                // Eğer şikayet kaynaklı değilse sarı etiket yap (Ekip Daveti)
                if ($color !== 'red') {
                    $typeLabel = 'Ekip Daveti';
                    $color = 'amber';
                }
            }

            // Departman rengini belirle
            $deptColor = $deptColorMap[$dept] ?? 'slate';

            $notification->computed_type = $typeLabel;
            $notification->computed_dept = $dept;
            $notification->computed_dept_color = $deptColor; 
            $notification->computed_color = $color;

            return $notification;
        });

        return view('livewire.notifications-index', [
            'notifications' => $notifications
        ]);
    }
}
