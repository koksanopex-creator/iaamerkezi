<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewCustomerUserCreated;
use App\Notifications\NewUserAddedNotification;

class MusteriYonetimi extends Component
{
    use WithPagination, WithFileUploads;

    // Arama ve Modal Değişkenleri
    public $search = '';
    public $showModal = false;
    public $showRepModal = false;
    public $showStatusModal = false;

    // Yetki Kontrolü
    public $isAdmin = false;

    // Form Alanları
    public $isEditMode = false;
    public $statusReason = '';

    // Durum Değişikliği İçin Hedef Müşteri (View'da DB sorgusu yapmamak için)
    public $targetCustomer = null;

    public $customer_id, $name, $tax_number, $tax_office, $address, $phone, $location_type = 'Yurt İçi';
    public $rep_name, $rep_email, $rep_phone;
    public $new_rep_name, $new_rep_email, $new_rep_phone, $selectedCustomer;
    public $editingRepId = null, $edit_rep_name, $edit_rep_email, $edit_rep_phone;
    public $logo, $rep_title, $new_rep_title, $edit_rep_title;

    // Sıralama Değişkenleri
    public $sortField = 'name';
    public $sortDirection = 'asc';

    // En Çok Şikayet Alanlar Filtreleri
    public $topFilterStartDate = '';
    public $topFilterEndDate = '';
    public $showTopComplaints = false;
    public $createdUserPassword = null;

    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:150|string',
            'logo' => 'nullable|image|max:2048',
            'tax_number' => 'nullable|numeric|digits_between:10,11',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|numeric|digits_between:10,15',
            'location_type' => 'required|in:Yurt İçi,Yurt Dışı',
            'rep_name' => $this->isEditMode ? 'nullable' : 'required|min:3|max:50|string',
            'rep_email' => [$this->isEditMode ? 'nullable' : 'required', 'email'], // Unique kontrolü manuel yapılacak
            'rep_title' => 'nullable|string|max:100',
            'rep_phone' => 'nullable|numeric|digits_between:10,15',
        ];
    }

    public function mount()
    {
        $user = Auth::user();

        // 1. MÜŞTERİ ENGELİ
        if ($user->customer_id) {
            return redirect()->route('musteri.profil.show', $user->customer_id);
        }

        // 2. YETKİ BELİRLEME ($isAdmin burada set ediliyor)
        // Organik bağ yoksa sayfayı hiç göremez (Hukuk, Direktör vb. tüm roller dahil)
        if (!$user->hasSikayetOrganikBagi()) {
            abort(403, 'Müşteri yönetimi sayfasına erişim yetkiniz (organik bağ) bulunmamaktadır.');
        }

        // BUTON YETKİSİ: Hukuk rolleri kesinlikle yeni müşteri/şikayet EKLEYEMEZ (sadece görebilirler - organik bağ varsa)
        if ($user->hasAnyRole(['Hukuk Admini', 'Hukuk Yöneticisi', 'Yonetim', 'Yönetim'])) {
            $this->isAdmin = false;
        } else if ($user->hasAnyRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Bölüm Lideri', 'Direktör', 'Bölüm Kalite Yöneticisi'])) {
            // Bu roller ekleme yapabilir (isAdmin = true)
            $this->isAdmin = true;
        } else if ($user->hasRole('Müşteri Saha Temsilcisi')) {
            $this->isAdmin = false;
        } else {
            $this->isAdmin = false;
            
            // GÖREV KONTROLÜ (Sinan gibi personeller için)
            $gorevVarMi = \App\Models\MusteriSikayeti::where(function ($q) use ($user) {
                $q->whereHas('cozumTakimi', function ($t) use ($user) {
                    $t->whereHas('uyeler', function ($u) use ($user) {
                        $u->where('users.id', $user->id);
                    });
                })
                    ->orWhereHas('iaa', function ($p) use ($user) {
                        $p->whereHas('users', function ($u) use ($user) {
                            $u->where('users.id', $user->id);
                        });
                    });
            })->exists();

            if (!$gorevVarMi) {
                abort(403, 'Herhangi bir müşteri şikayet takımında veya projede göreviniz bulunmamaktadır.');
            }
        }
    }

    public function render()
    {
        $user = Auth::user();
        $query = Customer::query();

        // 1. YETKİ TABANLI FİLTRELEME (Scoping)
        if ($user->hasAnyRole(['Superadmin', 'Yonetim', 'Yönetim', 'Müşteri Şikayeti Kurulu'])) {
            // Tam yetkili ve yönetim rolleri tüm müşterileri filtresiz görür
        } elseif (!$this->isAdmin && !$user->hasAnyRole(['Hukuk Admini', 'Hukuk Yöneticisi', 'Müşteri Saha Temsilcisi'])) {
            // Tam yetkili olmayan (Düz personel vb.) sadece dahil olduğu şikayetlerin müşterilerini görür
            $query->whereHas('sikayetler', function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->whereHas('cozumTakimi', function ($t) use ($user) {
                        $t->whereHas('uyeler', function ($u) use ($user) {
                            $u->where('users.id', $user->id);
                        });
                    })
                    ->orWhereHas('iaa', function ($p) use ($user) {
                        $p->whereHas('users', function ($u) use ($user) {
                            $u->where('users.id', $user->id);
                        });
                    });
                });
            });
        } else {
            // Bölüm Lideri, Direktör ve Hukuk rolleri (Kendi bölümü + Ekibinin dahil olduğu işler)
            $allowedBolumIds = $user->getAllowedBolumIds();
            $query->whereHas('sikayetler', function ($q) use ($user, $allowedBolumIds) {
                $q->where(function($sub) use ($user, $allowedBolumIds) {
                    // Kriter A: Şikayet Kategorisi Kendi Bölümüne Ait
                    if (is_array($allowedBolumIds) && count($allowedBolumIds) > 0) {
                        $sub->whereHas('sikayetKategori', function ($k) use ($allowedBolumIds) {
                            $k->whereIn('bolum_id', $allowedBolumIds);
                        });
                    }
                    
                    // Kriter B: Kendi Bölümünden Bir Personeli İşin İçinde (Takım veya Proje)
                    if ($user->bolum_id && !$user->hasRole('Müşteri Saha Temsilcisi')) {
                        $sub->orWhereHas('cozumTakimi.uyeler', function($u) use ($user) {
                            $u->where('users.bolum_id', $user->bolum_id);
                        })
                        ->orWhereHas('iaa.projeEkibi', function($u) use ($user) {
                            $u->where('users.bolum_id', $user->bolum_id);
                        });
                    }
                });
            });
        }

        // 2. İSTATİSTİKLER (Filtrelenmiş Query Üzerinden)
        $stats = [
            'total' => (clone $query)->count(),
            'domestic' => (clone $query)->where('location_type', 'Yurt İçi')->count(),
            'international' => (clone $query)->where('location_type', 'Yurt Dışı')->count(),
        ];

        // 3. MÜŞTERİ LİSTESİ ÇEKİMİ
        $customers = $query
            ->withCount([
                'representatives',
                'sikayetler as toplam_sikayet' => function($q) use ($user) {
                    // Toplam şikayet sayısını da yetkiye göre kısıtlıyoruz (Eğer Bölüm Lideri ise sadece kendi ilgilendiklerini saymalı)
                    if (!$user->hasAnyRole(['Superadmin', 'Yonetim', 'Yönetim', 'Müşteri Şikayeti Kurulu'])) {
                        $allowedBolumIds = $user->getAllowedBolumIds();
                        $q->where(function($sub) use ($user, $allowedBolumIds) {
                            if (is_array($allowedBolumIds) && count($allowedBolumIds) > 0) {
                                $sub->whereHas('sikayetKategori', function ($k) use ($allowedBolumIds) { $k->whereIn('bolum_id', $allowedBolumIds); });
                            }
                            if ($user->bolum_id && !$user->hasRole('Müşteri Saha Temsilcisi')) {
                                $sub->orWhereHas('cozumTakimi.uyeler', function($u) use ($user) { $u->where('users.bolum_id', $user->bolum_id); })
                                    ->orWhereHas('iaa.projeEkibi', function($u) use ($user) { $u->where('users.bolum_id', $user->bolum_id); });
                            }
                        });
                    }
                },
                'sikayetler as cozulmus_sikayet' => function ($q) use ($user) {
                    $q->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı']);
                    if (!$user->hasAnyRole(['Superadmin', 'Yonetim', 'Yönetim', 'Müşteri Şikayeti Kurulu'])) {
                        $allowedBolumIds = $user->getAllowedBolumIds();
                        $q->where(function($sub) use ($user, $allowedBolumIds) {
                            if (is_array($allowedBolumIds) && count($allowedBolumIds) > 0) {
                                $sub->whereHas('sikayetKategori', function ($k) use ($allowedBolumIds) { $k->whereIn('bolum_id', $allowedBolumIds); });
                            }
                            if ($user->bolum_id && !$user->hasRole('Müşteri Saha Temsilcisi')) {
                                $sub->orWhereHas('cozumTakimi.uyeler', function($u) use ($user) { $u->where('users.bolum_id', $user->bolum_id); })
                                    ->orWhereHas('iaa.projeEkibi', function($u) use ($user) { $u->where('users.bolum_id', $user->bolum_id); });
                            }
                        });
                    }
                },
                'sikayetler as total_returns' => function ($q) {
                    $q->has('iadeler');
                }
            ])
            ->addSelect([
                'total_visits' => \App\Models\IaaZiyaretPlani::whereIn('iaa_id', function($q) {
                    $q->select('iaa_id')->from('musteri_sikayetleri')
                      ->whereColumn('customer_id', 'customers.id')
                      ->whereNotNull('iaa_id');
                })->whereIn('status', ['Onaylandı', 'Tamamlandı'])
                  ->selectRaw('COALESCE(count(*), 0)'),
            ])
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('tax_number', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        // 4. EN ÇOK ŞİKAYET ALANLAR (Top 5 - Yetkiye Göre Filtrelenmiş)
        $topComplaints = (clone $query)->withCount(['sikayetler' => function($q) use ($user) {
            if ($this->topFilterStartDate) $q->whereDate('musteri_sikayet_tarihi', '>=', $this->topFilterStartDate);
            if ($this->topFilterEndDate) $q->whereDate('musteri_sikayet_tarihi', '<=', $this->topFilterEndDate);
            
            // Buradaki sayacı da yetkiye göre kısıtlıyoruz
            if (!$user->hasAnyRole(['Superadmin', 'Yonetim', 'Yönetim', 'Müşteri Şikayeti Kurulu'])) {
                $allowedBolumIds = $user->getAllowedBolumIds();
                $q->where(function($sub) use ($user, $allowedBolumIds) {
                    if (is_array($allowedBolumIds) && count($allowedBolumIds) > 0) {
                        $sub->whereHas('sikayetKategori', function ($k) use ($allowedBolumIds) { $k->whereIn('bolum_id', $allowedBolumIds); });
                    }
                    if ($user->bolum_id && !$user->hasRole('Müşteri Saha Temsilcisi')) {
                        $sub->orWhereHas('cozumTakimi.uyeler', function($u) use ($user) { $u->where('users.bolum_id', $user->bolum_id); })
                            ->orWhereHas('iaa.projeEkibi', function($u) use ($user) { $u->where('users.bolum_id', $user->bolum_id); });
                    }
                });
            }
        }])
        ->reorder() // Tablonun varsayılan sıralamasını temizle
        ->orderByDesc('sikayetler_count')
        ->take(5)
        ->get();

        /** @var \Illuminate\View\View $view */
        $view = view('livewire.admin.musteri-yonetimi', [
            'customers' => $customers,
            'stats' => $stats,
            'topComplaints' => $topComplaints
        ]);

        return $view->layout('layouts.app');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // --- ACTIONS ---

    public function create()
    {
        if (!$this->isAdmin)
            abort(403);
        abort_if(auth()->user()->hasRole('Direktör'), 403);
        $this->resetForm();
        $this->showModal = true;
        $this->isEditMode = false;
    }

    public function store()
    {
        if (!$this->isAdmin)
            abort(403);
        abort_if(auth()->user()->hasRole('Direktör'), 403);
        $this->validate();
        DB::transaction(function () {
            $logoPath = $this->logo ? $this->logo->store('musteri-logolari', 'public') : null;
            $customer = Customer::create([
                'name' => $this->name,
                'logo_path' => $logoPath,
                'tax_number' => $this->tax_number,
                'tax_office' => $this->tax_office,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->rep_email,
                'location_type' => $this->location_type,
                'is_active' => true
            ]);
            $this->createUserForCustomer($customer, $this->rep_name, $this->rep_email, $this->rep_phone, $this->rep_title);
        });
        $this->showModal = false;
        $tempPassword = $this->createdUserPassword;
        $this->resetForm();
        $this->createdUserPassword = $tempPassword;
        session()->flash('message', 'Müşteri başarıyla oluşturuldu.');
    }

    public function edit($id)
    {
        if (!$this->isAdmin)
            abort(403);
        abort_if(auth()->user()->hasRole('Direktör'), 403);
        $customer = Customer::findOrFail($id);
        $this->customer_id = $customer->id;
        $this->name = $customer->name;
        $this->tax_number = $customer->tax_number;
        $this->tax_office = $customer->tax_office;
        $this->address = $customer->address;
        $this->phone = $customer->phone;
        $this->location_type = $customer->location_type;
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        if (!$this->isAdmin)
            abort(403);
        abort_if(auth()->user()->hasRole('Direktör'), 403);
        $this->validate(['name' => 'required|min:3', 'logo' => 'nullable|image|max:2048', 'location_type' => 'required']);
        $customer = Customer::findOrFail($this->customer_id);
        $data = ['name' => $this->name, 'tax_number' => $this->tax_number, 'tax_office' => $this->tax_office, 'address' => $this->address, 'phone' => $this->phone, 'location_type' => $this->location_type];
        if ($this->logo) {
            if ($customer->logo_path)
                Storage::disk('public')->delete($customer->logo_path);
            $data['logo_path'] = $this->logo->store('musteri-logolari', 'public');
        }
        $customer->update($data);
        session()->flash('message', 'Güncellendi.');
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        if (!$this->isAdmin)
            abort(403);
        // Silme sadece Superadmin
        if (!auth()->user()->hasRole('Superadmin'))
            abort(403, 'Bu işlem için yetkiniz yok.');
        $customer = Customer::find($id);
        if ($customer) {
            foreach ($customer->representatives as $r)
                $r->delete();
            $customer->delete();
            session()->flash('message', 'Silindi.');
        }
    }

    // --- DURUM DEĞİŞTİRME (DÜZELTİLDİ) ---
    public function confirmStatusChange($id)
    {
        if (!$this->isAdmin)
            abort(403);
        abort_if(auth()->user()->hasRole('Direktör'), 403);

        // Müşteriyi veritabanından çekip değişkene atıyoruz
        // Böylece View'da tekrar DB sorgusu yapmaya gerek kalmaz.
        $this->targetCustomer = Customer::findOrFail($id);

        $this->statusReason = $this->targetCustomer->passive_reason;
        $this->showStatusModal = true;
    }

    public function updateStatus()
    {
        if (!$this->isAdmin)
            abort(403);
        abort_if(auth()->user()->hasRole('Direktör'), 403);

        if (!$this->targetCustomer) {
            $this->showStatusModal = false;
            return;
        }

        // Eğer şu an Aktifse -> Pasife alıyoruz (Sebep zorunlu)
        if ($this->targetCustomer->is_active) {
            $this->validate(['statusReason' => 'required|min:5|max:500'], ['statusReason.required' => 'Pasife alma sebebini yazmalısınız.']);
            $this->targetCustomer->update(['is_active' => false, 'passive_reason' => $this->statusReason]);
        }
        // Eğer Pasifse -> Aktife alıyoruz
        else {
            $this->targetCustomer->update(['is_active' => true, 'passive_reason' => null]);
        }

        $this->showStatusModal = false;
        $this->reset('statusReason', 'targetCustomer');
    }

    // --- YETKİLİ YÖNETİMİ ---
    public function manageRepresentatives($id)
    {
        if (!$this->isAdmin)
            abort(403);
        abort_if(auth()->user()->hasRole('Direktör'), 403);
        $this->selectedCustomer = Customer::with([
            'users' => function ($q) {
                $q->withTrashed();
            }
        ])->findOrFail($id);
        $this->showRepModal = true;
        $this->createdUserPassword = null;
    }

    public function addRepresentative()
    {
        if (!$this->isAdmin)
            abort(403);
        abort_if(auth()->user()->hasRole('Direktör'), 403);
        
        $this->validate([
            'new_rep_name' => 'required|min:3|max:50|string',
            'new_rep_email' => 'required|email',
            'new_rep_phone' => 'nullable|numeric|digits_between:10,15',
        ]);

        // Bu firma için zaten ekli mi?
        $isAlreadyLinked = $this->selectedCustomer->users()
            ->where('email', $this->new_rep_email)
            ->exists();

        if ($isAlreadyLinked) {
            $this->addError('new_rep_email', 'Bu yetkili bu firmaya zaten ekli.');
            return;
        }

        $this->createUserForCustomer($this->selectedCustomer, $this->new_rep_name, $this->new_rep_email, $this->new_rep_phone, $this->new_rep_title);
        
        $this->selectedCustomer->load(['users' => function($q) { $q->withTrashed(); }]);
        $this->reset('new_rep_name', 'new_rep_email', 'new_rep_phone', 'new_rep_title');
    }

    public function deleteRepresentative($id)
    {
        if (!$this->isAdmin)
            abort(403);

        abort_if(auth()->user()->hasRole('Direktör'), 403);
        abort_if(auth()->user()->hasRole('Müşteri Şikayeti Kurulu'), 403, 'Silme yetkiniz yok, yetkiliyi pasife alabilirsiniz.');

        // Kullanıcıyı tamamen silmek yerine firmadan ilişiğini kesiyoruz (detach)
        $this->selectedCustomer->users()->detach($id);
        
        session()->flash('rep_message', 'Yetkili firmadan başarıyla kaldırıldı.');
        $this->selectedCustomer->load(['users' => function($q) { $q->withTrashed(); }]);
    }

    private function createUserForCustomer($c, $n, $e, $p, $t = null): void
    {
        // 1. Rastgele Şifre Üret (Açık hali elimizde)
        $pass = Str::random(8);

        try {
            // 2. Kullanıcıyı Bul veya Oluştur
            $isNewUser = false;
            $u = User::withTrashed()->where('email', $e)->first();

            if (!$u) {
                $isNewUser = true;
                // Yeni kayıt
                $u = new User();
                $u->email = $e;
                $u->password = Hash::make($pass);
                $u->name = $n;
                $u->unvan = $t;
                $u->telefon = $p;
                $u->is_personnel = false;
                $u->customer_id = $c->id; // Geriye dönük uyum için ilk firmayı yazıyoruz
                $u->onaylandi_mi = true;
                $u->save();

                // SSO Senkronizasyonu
                try {
                    app(\App\Services\CentralSsoSyncService::class)->syncUser($u, $pass, 'customer');
                } catch (\Exception $ssoEx) {
                    \Illuminate\Support\Facades\Log::error('Central SSO Sync failed for new customer user: ' . $ssoEx->getMessage());
                }

                $u->notify(new NewUserAddedNotification(
                    $u,
                    "{$c->name} firması için müşteri temsilcisi olarak eklendiniz. Giriş yapabilirsiniz.",
                    "Yeni Firma Yetkilendirmesi"
                ));
            } else {
                // Mevcut kullanıcıyı geri getir (eğer silinmişse)
                if ($u->trashed()) {
                    $u->restore();
                    $isNewUser = true; // Trashed olan biri yeniden eklenirse "yeni" gibi davranıyoruz
                    
                    $u->password = Hash::make($pass);
                    $u->name = $n;
                    $u->unvan = $t;
                    $u->telefon = $p;
                    $u->is_personnel = false;
                    $u->customer_id = $c->id;
                    $u->onaylandi_mi = true;
                    $u->save();

                    // Trashed durumdan döndüğü için şifreyle birlikte SSO senkronizasyonu
                    try {
                        app(\App\Services\CentralSsoSyncService::class)->syncUser($u, $pass, 'customer');
                    } catch (\Exception $ssoEx) {
                        \Illuminate\Support\Facades\Log::error('Central SSO Sync failed for restored customer user: ' . $ssoEx->getMessage());
                    }

                    $u->notify(new NewUserAddedNotification(
                        $u,
                        "{$c->name} firması için müşteri temsilcisi olarak eklendiniz. Giriş yapabilirsiniz.",
                        "Yeni Firma Yetkilendirmesi"
                    ));
                } else {
                    // Sadece var olan aktif bir kullanıcı
                    // Bilgileri güncelle
                    $u->update([
                        'name' => $n,
                        'telefon' => $p
                    ]);

                    // Aktif kullanıcının bilgilerini (şifresiz) SSO senkronizasyonu
                    try {
                        app(\App\Services\CentralSsoSyncService::class)->syncUser($u, null, 'customer');
                    } catch (\Exception $ssoEx) {
                        \Illuminate\Support\Facades\Log::error('Central SSO Sync failed for existing customer user: ' . $ssoEx->getMessage());
                    }

                    $u->notify(new NewUserAddedNotification(
                        $u,
                        "{$c->name} firması için de müşteri temsilcisi olarak yetkilendirildiniz.",
                        "Yeni Firma Yetkilendirmesi"
                    ));
                }
            }

            // Firmaya bağla (Pivot tablo) - Ünvanı firmaya özel kaydediyoruz
            $c->users()->syncWithoutDetaching([$u->id => [
                'is_active' => true,
                'unvan' => $t
            ]]);

            // Rol Ataması
            if (Role::where('name', 'Müşteri Temsilcisi')->exists()) {
                $u->syncRoles(['Müşteri Temsilcisi']);
            }

            // 3. Mail Gönder (Sadece yeni kullanıcılara şifre gönderilir)
            if ($isNewUser) {
                try {
                    Mail::to($u->email)->queue(new NewCustomerUserCreated($u, $pass));
                } catch (\Exception $e) {
                    // Log error but don't stop execution
                }
            }

            // 4. Şifreyi Ekranda Göster
            if ($isNewUser) {
                session()->flash('generated_password', $pass);
                session()->flash('generated_user_email', $e);
                session()->flash('rep_message', 'Yetkili başarıyla oluşturuldu ve şifre e-posta ile gönderildi.');
            } else {
                session()->flash('rep_message', 'Mevcut yetkili bu firmaya başarıyla bağlandı. Mevcut şifresi ile giriş yapabilir.');
            }
            session()->flash('generated_customer_name', $c->name);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Yetkili oluşturma hatası: ' . $e->getMessage());
            session()->flash('error', 'Kullanıcı oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset(['name', 'tax_number', 'tax_office', 'address', 'phone', 'rep_name', 'rep_email', 'rep_phone', 'customer_id', 'new_rep_name', 'new_rep_email', 'new_rep_phone', 'logo', 'statusReason', 'targetCustomer', 'createdUserPassword']);
        $this->location_type = 'Yurt İçi';
    }
    public function editRepresentative($id)
    {
        if (!$this->isAdmin)
            return;

        // Ünvanı bu firmaya özel olan pivot tablodan çekiyoruz
        $u = $this->selectedCustomer->users()->where('users.id', $id)->firstOrFail();
        $this->editingRepId = $u->id;
        $this->edit_rep_name = $u->name;
        $this->edit_rep_email = $u->email;
        $this->edit_rep_phone = $u->telefon;
        $this->edit_rep_title = $u->pivot->unvan;
    }
    public function updateRepresentative()
    {
        if (!$this->isAdmin)
            return;

        $this->validate([
            'edit_rep_name' => 'required|min:3|max:50',
            'edit_rep_email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->editingRepId)->whereNull('deleted_at')
            ],
            'edit_rep_phone' => 'nullable|numeric|digits_between:10,15',
            'edit_rep_title' => 'nullable|string|max:100',
        ]);

        $user = User::findOrFail($this->editingRepId);
        $user->update([
            'name' => $this->edit_rep_name,
            'email' => $this->edit_rep_email,
            'telefon' => $this->edit_rep_phone,
            // 'unvan' => $this->edit_rep_title // ARTIK GLOBAL ÜNVANI GÜNCELLEMİYORUZ
        ]);

        // Firmaya özel ünvanı pivot tabloda güncelle
        $this->selectedCustomer->users()->updateExistingPivot($this->editingRepId, [
            'unvan' => $this->edit_rep_title
        ]);

        // Merkezi API'ye senkronize et (şifre boş gönderilir)
        try {
            app(\App\Services\CentralSsoSyncService::class)->syncUser($user, null, 'customer');
        } catch (\Exception $ssoEx) {
            \Illuminate\Support\Facades\Log::error('Central SSO Sync failed on representative update: ' . $ssoEx->getMessage());
        }

        $this->cancelEditRepresentative();
        
        // Refresh selected customer and its relations
        $this->selectedCustomer->refresh();
        $this->selectedCustomer->load(['users' => function($q) {
            $q->withTrashed();
        }]);
        
        session()->flash('rep_message', 'Yetkili bilgileri başarıyla güncellendi.');
    }
    public function cancelEditRepresentative()
    {
        $this->editingRepId = null;
        $this->reset('edit_rep_name', 'edit_rep_email', 'edit_rep_phone', 'edit_rep_title');
    }
    public function updatedShowRepModal($v)
    {
        if (!$v)
            $this->cancelEditRepresentative();
    }
    public function toggleRepresentativeStatus($repId)
    {
        if (!$this->isAdmin)
            abort(403);

        $pivot = DB::table('customer_user')
            ->where('customer_id', $this->selectedCustomer->id)
            ->where('user_id', $repId)
            ->first();

        if ($pivot) {
            DB::table('customer_user')
                ->where('id', $pivot->id)
                ->update(['is_active' => !$pivot->is_active]);
            
            $statusStr = !$pivot->is_active ? 'aktif edildi' : 'pasife alındı';
            session()->flash('rep_message', "Yetkili bu firma için {$statusStr}.");
        }

        $this->selectedCustomer->load(['users' => function ($q) {
            $q->withTrashed(); 
        }]);
    }
}