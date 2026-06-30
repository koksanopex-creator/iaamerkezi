<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Bolum;
use Illuminate\Support\Facades\DB;

class NotificationsAudit extends Component
{
    use WithPagination;

    public $search = '';
    public $status = 'all';
    public $selectedBolum = 'all';
    public $startDate;
    public $endDate;
    public $selectedUser = 'all';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }
    public function updatingSelectedBolum() { $this->resetPage(); }
    public function updatingSelectedUser() { $this->resetPage(); }

    public function mount()
    {
        $user = Auth::user();
        // Eğer Bölüm Lideri veya Kalite Yöneticisi ise varsayılan filtreleme yap
        if (!$user->hasAnyRole(['Superadmin', 'Yonetim'])) {
            if ($user->hasRole('Bölüm Lideri')) {
                $this->selectedBolum = $user->bolum_id;
            }
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        // Kimler görebilir?
        if (!$user->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Bölüm Kalite Yöneticisi'])) {
            abort(403);
        }

        $query = DB::table('notifications')
            ->join('users', 'notifications.notifiable_id', '=', 'users.id')
            ->leftJoin('bolumler', 'users.bolum_id', '=', 'bolumler.id')
            ->select('notifications.*', 'users.name as user_name', 'bolumler.ad as bolum_ad', 'users.bolum_id as user_bolum_id')
            ->where('notifications.notifiable_type', 'App\Models\User')
            ->where('users.id', '!=', $user->id) // KENDİNİ GÖRMESİN
            ->latest('notifications.created_at');

        // YETKİ FİLTRESİ
        if (!$user->hasAnyRole(['Superadmin', 'Yonetim'])) {
            
            // 1. KAPSAM BELİRLEME
            $myBolumId = $user->bolum_id;
            $yonetilenBolumIds = [];
            
            if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
                $yonetilenBolumIds = $user->yonettigiSikayetKategorileri()->pluck('bolum_id')->unique()->toArray();
            }

            // 2. KİMLERİ GÖREBİLİR? (HASSAS HİYERARŞİ)
            $query->where(function($q) use ($myBolumId, $yonetilenBolumIds) {
                
                // A. Kendi Asıl Bölümündeki Normal Personeller
                if ($myBolumId) {
                    $q->where(function($sub) use ($myBolumId) {
                        $sub->where('users.bolum_id', $myBolumId)
                            ->whereNotExists(function($r) {
                                $r->select(DB::raw(1))
                                    ->from('model_has_roles')
                                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                    ->whereColumn('model_has_roles.model_id', 'users.id')
                                    ->where('model_has_roles.model_type', 'App\Models\User')
                                    ->whereIn('roles.name', ['Superadmin', 'Yonetim', 'Yönetim', 'Bölüm Lideri', 'Direktör', 'Bölüm Kalite Yöneticisi', 'Müşteri Şikayeti Çözüm Lideri']);
                            });
                    });
                }

                // B. Kalite Sorumlusu Olduğu Bölümlerdeki Çözüm Liderleri (Bölümü boş olan liderler dahil)
                $q->orWhere(function($sub) use ($yonetilenBolumIds) {
                    $sub->where(function($inner) use ($yonetilenBolumIds) {
                            $inner->whereIn('users.bolum_id', $yonetilenBolumIds ?: [-1])
                                  ->orWhereNull('users.bolum_id');
                        })
                        ->whereExists(function($r) {
                            $r->select(DB::raw(1))
                                ->from('model_has_roles')
                                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                ->whereColumn('model_has_roles.model_id', 'users.id')
                                ->where('model_has_roles.model_type', 'App\Models\User')
                                ->where('roles.name', 'Müşteri Şikayeti Çözüm Lideri');
                        });
                });
            });

            // 3. İÇERİK KISITLAMASI (Çözüm Liderleri sadece şikayet bildirimlerini görsün)
            $query->where(function($q) {
                $q->whereExists(function($sub) {
                    $sub->select(DB::raw(1))
                        ->from('model_has_roles')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->whereColumn('model_has_roles.model_id', 'notifications.notifiable_id')
                        ->where('model_has_roles.model_type', 'App\Models\User')
                        ->where('roles.name', 'Müşteri Şikayeti Çözüm Lideri')
                        ->where(function($msg) {
                            $msg->where('notifications.data', 'like', '%şikayet%')
                                ->orWhere('notifications.data', 'like', '%müşteri%')
                                ->orWhere('notifications.data', 'like', '%Şikayet%')
                                ->orWhere('notifications.data', 'like', '%Müşteri%');
                        });
                })
                ->orWhereNotExists(function($sub) {
                    $sub->select(DB::raw(1))
                        ->from('model_has_roles')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->whereColumn('model_has_roles.model_id', 'notifications.notifiable_id')
                        ->where('model_has_roles.model_type', 'App\Models\User')
                        ->where('roles.name', 'Müşteri Şikayeti Çözüm Lideri');
                });
            });

        } else {
            // Superadmin veya Yönetim ise seçilen bölüm filtresini uygula
            if ($this->selectedBolum !== 'all') {
                $query->where('users.bolum_id', $this->selectedBolum);
            }
        }

        // DİĞER FİLTRELER
        if ($this->selectedUser !== 'all') {
            $query->where('notifications.notifiable_id', $this->selectedUser);
        }

        if ($this->status !== 'all') {
            if ($this->status === 'read') {
                $query->whereNotNull('notifications.read_at');
            } else {
                $query->whereNull('notifications.read_at');
            }
        }

        if ($this->startDate) {
            $query->whereDate('notifications.created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('notifications.created_at', '<=', $this->endDate);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('notifications.data', 'like', '%'.$this->search.'%')
                  ->orWhere('users.name', 'like', '%'.$this->search.'%');
            });
        }

        $notifications = $query->paginate(20);

        // Bölümler (Filtre için - Superadmin/Yonetim için hepsi, Lider için sadece yetkili oldukları)
        if ($user->hasAnyRole(['Superadmin', 'Yonetim'])) {
            $bolumler = Bolum::orderBy('ad')->get();
        } else {
             // Liderlerin görebileceği bölümler
            $leaderBolumIds = [];
            if ($user->hasRole('Bölüm Lideri') && $user->bolum_id) $leaderBolumIds[] = $user->bolum_id;
            if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
                $leaderBolumIds = array_merge($leaderBolumIds, $user->yonettigiSikayetKategorileri()->pluck('bolum_id')->unique()->toArray());
            }
            $bolumler = Bolum::whereIn('id', array_unique($leaderBolumIds))->orderBy('ad')->get();
        }
        
        // Kullanıcılar (Filtre için)
        $availableUsersQuery = User::where('id', '!=', $user->id)->orderBy('name');
        if (!$user->hasAnyRole(['Superadmin', 'Yonetim'])) {
            
            $myBolumId = $user->bolum_id;
            $yonetilenBolumIds = [];
            if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
                $yonetilenBolumIds = $user->yonettigiSikayetKategorileri()->pluck('bolum_id')->unique()->toArray();
            }

            $availableUsersQuery->where(function($q) use ($myBolumId, $yonetilenBolumIds) {
                // A. Kendi Bölümündeki Normal Personeller
                if ($myBolumId) {
                    $q->where(function($sub) use ($myBolumId) {
                        $sub->where('users.bolum_id', $myBolumId)
                            ->whereNotExists(function($r) {
                                $r->select(DB::raw(1))
                                    ->from('model_has_roles')
                                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                    ->whereColumn('model_has_roles.model_id', 'users.id')
                                    ->where('model_has_roles.model_type', 'App\Models\User')
                                    ->whereIn('roles.name', ['Superadmin', 'Yonetim', 'Yönetim', 'Bölüm Lideri', 'Direktör', 'Bölüm Kalite Yöneticisi', 'Müşteri Şikayeti Çözüm Lideri']);
                            });
                    });
                }

                // B. Tüm Çözüm Liderleri (Bölümü boş olanlar dahil)
                $q->orWhereExists(function($r) {
                    $r->select(DB::raw(1))
                        ->from('model_has_roles')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->whereColumn('model_has_roles.model_id', 'users.id')
                        ->where('model_has_roles.model_type', 'App\Models\User')
                        ->where('roles.name', 'Müşteri Şikayeti Çözüm Lideri');
                });
            });
        } elseif ($this->selectedBolum !== 'all') {
            $availableUsersQuery->where('bolum_id', $this->selectedBolum);
        }
        $usersList = $availableUsersQuery->get();

        return view('livewire.admin.notifications-audit', [
            'notifications' => $notifications,
            'bolumler' => $bolumler,
            'usersList' => $usersList
        ]);
    }
}
