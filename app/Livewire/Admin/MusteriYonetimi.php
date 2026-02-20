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

    // Şifre Gösterimi
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
            'rep_email' => [$this->isEditMode ? 'nullable' : 'required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
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
        // Bu roller tam yetkilidir.
        if ($user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Bölüm Lideri', 'Direktör'])) {
            $this->isAdmin = true;
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

        // FİLTRELEME MANTIĞI
        if (!$this->isAdmin) {
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
        } elseif (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu'])) {
            $allowedBolumIds = $user->getAllowedBolumIds();
            if (is_array($allowedBolumIds) && count($allowedBolumIds) > 0) {
                $query->whereHas('sikayetler', function ($q) use ($allowedBolumIds) {
                    $q->whereHas('sikayetKategori', function ($k) use ($allowedBolumIds) {
                        $k->whereIn('bolum_id', $allowedBolumIds);
                    });
                });
            }
        }

        $customers = $query
            ->withCount([
                'representatives',
                'sikayetler as toplam_sikayet',
                'sikayetler as cozulmus_sikayet' => function ($q) {
                    $q->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı']);
                }
            ])
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('tax_number', 'like', '%' . $this->search . '%');
            })
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.musteri-yonetimi', [
            'customers' => $customers
        ])->layout('layouts.app');
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
            'new_rep_email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'new_rep_phone' => 'nullable|numeric|digits_between:10,15',
        ]);
        $this->createUserForCustomer($this->selectedCustomer, $this->new_rep_name, $this->new_rep_email, $this->new_rep_phone, $this->new_rep_title);
        $this->selectedCustomer->load('representatives');
        $this->reset('new_rep_name', 'new_rep_email', 'new_rep_phone', 'new_rep_title');
    }

    public function deleteRepresentative($id)
    {
        if (!$this->isAdmin)
            abort(403);

        abort_if(auth()->user()->hasRole('Direktör'), 403);
        abort_if(auth()->user()->hasRole('Müşteri Şikayeti Kurulu'), 403, 'Silme yetkiniz yok, yetkiliyi pasife alabilirsiniz.');

        $u = User::find($id);
        if ($u && $u->customer_id == $this->selectedCustomer->id) {
            $u->delete();
            $this->selectedCustomer->refresh();
        }
    }

    private function createUserForCustomer($c, $n, $e, $p, $t = null): void
    {
        // 1. Rastgele Şifre Üret (Açık hali elimizde)
        $pass = Str::random(8);

        try {
            // 2. Kullanıcıyı Bul veya Oluştur (Daha Güvenli Yöntem)
            $u = User::withTrashed()->where('email', $e)->first();

            if (!$u) {
                // Yeni kayıt
                $u = new User();
                $u->email = $e;
                $u->password = Hash::make($pass);
            } else {
                // Eğer silinmişse geri getir
                if ($u->trashed()) {
                    $u->restore();
                }
                // Mevcut kullanıcı şifresini yenile
                $u->password = Hash::make($pass);
            }

            // Ortak alanları set et
            $u->name = $n;
            $u->unvan = $t;
            $u->telefon = $p;
            $u->is_personnel = false;
            $u->customer_id = $c->id;
            $u->onaylandi_mi = true;
            $u->save(); // Explicit Save

            // Rol Ataması
            if (Role::where('name', 'Müşteri Temsilcisi')->exists()) {
                $u->syncRoles(['Müşteri Temsilcisi']);
            }

            // 3. Mail Gönder
            try {
                Mail::to($u->email)->send(new NewCustomerUserCreated($u, $pass));
            } catch (\Exception $ex) {
                \Illuminate\Support\Facades\Log::error('Mail gönderilemedi: ' . $ex->getMessage());
            }

            // 4. Şifreyi Ekranda Göster
            session()->flash('generated_password', $pass);
            session()->flash('generated_user_email', $e);
            session()->flash('generated_customer_name', $c->name);
            session()->flash('message', 'Müşteri oluşturuldu ve şifre e-posta ile gönderildi.');

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
        if (auth()->user()->hasRole('Direktör'))
            return;
        $u = User::findOrFail($id);
        $this->editingRepId = $u->id;
        $this->edit_rep_name = $u->name;
        $this->edit_rep_email = $u->email;
        $this->edit_rep_phone = $u->telefon;
        $this->edit_rep_title = $u->unvan;
    }
    public function updateRepresentative()
    {
        if (!$this->isAdmin)
            return;
        if (auth()->user()->hasRole('Direktör'))
            return;
        $this->validate(['edit_rep_name' => 'required', 'edit_rep_email' => 'required|email']);
        User::where('id', $this->editingRepId)->update(['name' => $this->edit_rep_name, 'email' => $this->edit_rep_email, 'telefon' => $this->edit_rep_phone, 'unvan' => $this->edit_rep_title]);
        $this->cancelEditRepresentative();
        $this->selectedCustomer->refresh();
    }
    public function cancelEditRepresentative()
    {
        $this->editingRepId = null;
        $this->reset('edit_rep_name', 'edit_rep_email', 'edit_rep_phone');
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

        $rep = User::withTrashed()->findOrFail($repId);

        if ($rep->trashed()) {
            $rep->restore();
            session()->flash('rep_message', 'Yetkili tekrar aktif edildi (giriş yapabilir).');
        } else {
            $rep->delete(); // Soft Delete
            session()->flash('rep_message', 'Yetkili pasife alındı (giriş yapamaz).');
        }

        $this->selectedCustomer->unsetRelation('users');
        $this->selectedCustomer->load(['users' => function ($q) {
            $q->withTrashed(); }]);
    }
}