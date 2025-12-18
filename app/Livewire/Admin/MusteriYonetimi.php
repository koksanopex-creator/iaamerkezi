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
use Livewire\WithFileUploads; // <--- EKLENDİ
use Illuminate\Support\Facades\Storage; // <--- EKLENDİ

class MusteriYonetimi extends Component
{
    use WithPagination, WithFileUploads;

    // Arama ve Modal Kontrolleri
    public $search = '';
    public $showModal = false; // Firma Ekleme/Düzenleme Modalı
    public $showRepModal = false; // YENİ: Yetkili Yönetimi Modalı
    public $isEditMode = false;

    // Firma Form Alanları
    public $customer_id;
    public $name, $tax_number, $tax_office, $address, $phone, $location_type = 'Yurt İçi';

    // İlk Yetkili Form Alanları (Firma eklerken)
    public $rep_name, $rep_email, $rep_phone;

    // YENİ: Ek Yetkili Ekleme Alanları (Yetkili Yönetimi Modalı için)
    public $new_rep_name, $new_rep_email, $new_rep_phone;
    public $selectedCustomer; // İşlem yapılan firma

    // Yetkili Düzenleme Değişkenleri
    public $editingRepId = null; // Şu an düzenlenen kişinin ID'si
    public $edit_rep_name, $edit_rep_email, $edit_rep_phone;

    // Yeni Değişkenler
    public $logo; // Logo dosyası için
    public $rep_title; // İlk yetkili ünvanı
    public $new_rep_title; // Yeni eklenecek yetkili ünvanı
    public $edit_rep_title; // Düzenlenen yetkili ünvanı

    // Validasyon Kuralları
    protected function rules()
    {
        return [
            // Firma Kuralları
            'name' => 'required|min:3|max:150|string',
            'logo' => 'nullable|image|max:2048', // <--- Logo: Resim olmalı, max 2MB
            'tax_number' => 'nullable|numeric|digits_between:10,11',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|numeric|digits_between:10,15',
            'location_type' => 'required|in:Yurt İçi,Yurt Dışı',

            // Yetkili Kişi Kuralları
            'rep_name' => $this->isEditMode ? 'nullable' : 'required|min:3|max:50|string',
            // DÜZELTİLEN KISIM: Silinmiş mailleri kontrol etme
            'rep_email' => [
                $this->isEditMode ? 'nullable' : 'required',
                'email',
                Rule::unique('users', 'email')->whereNull('deleted_at') 
            ],
            'rep_title' => 'nullable|string|max:100', // <--- İlk yetkili ünvanı
            'rep_phone' => 'nullable|numeric|digits_between:10,15',
        ];
    }

    public function render()
    {
        $customers = Customer::query()
            ->withCount('representatives') // Yetkili sayısını getir
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('tax_number', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.musteri-yonetimi', [
            'customers' => $customers
        ])->layout('layouts.app');
    }

    // === FİRMA İŞLEMLERİ ===

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditMode = false;
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            // Logo Yükleme İşlemi
            $logoPath = null;
            if ($this->logo) {
                $logoPath = $this->logo->store('musteri-logolari', 'public');
            }
            // 1. Firmayı Oluştur
            $customer = Customer::create([
                'name' => $this->name,
                'logo_path' => $logoPath, // <--- Eklendi
                'tax_number' => $this->tax_number,
                'tax_office' => $this->tax_office,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->rep_email,
                'location_type' => $this->location_type,
            ]);

            // 2. İlk Yetkiliyi Oluştur
            $this->createUserForCustomer($customer, $this->rep_name, $this->rep_email, $this->rep_phone, $this->rep_title);
        });

        $this->showModal = false;
        $this->resetForm();
    }

    public function edit($id)
    {
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
        $this->validate([
            'name' => 'required|min:3',
            'logo' => 'nullable|image|max:2048',
            'location_type' => 'required',
        ]);

        $customer = Customer::findOrFail($this->customer_id);
        
        // $data DEĞİŞKENİNİ BURADA TANIMLIYORUZ
        $data = [
            'name' => $this->name,
            'tax_number' => $this->tax_number,
            'tax_office' => $this->tax_office,
            'address' => $this->address,
            'phone' => $this->phone,
            'location_type' => $this->location_type,
        ];

        // Yeni logo varsa eskini sil, yenisini yükle
        if ($this->logo) {
            if ($customer->logo_path) {
                Storage::disk('public')->delete($customer->logo_path);
            }
            $data['logo_path'] = $this->logo->store('musteri-logolari', 'public');
        }

        $customer->update($data); // Artık $data tanımlı olduğu için hata vermeyecek

        session()->flash('message', 'Müşteri bilgileri güncellendi.');
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        $customer = Customer::find($id);
        
        if ($customer) {
            // Önce bu firmaya bağlı tüm yetkilileri Soft Delete yapalım
            foreach ($customer->representatives as $rep) {
                $rep->delete(); 
            }

            // Sonra firmayı silelim
            $customer->delete();
            
            session()->flash('message', 'Müşteri ve bağlı yetkilileri silindi.');
        }
    }

    // === YENİ: YETKİLİ YÖNETİMİ İŞLEMLERİ ===

    // Yetkililer penceresini aç
    public function manageRepresentatives($customerId)
    {
        $this->selectedCustomer = Customer::with('representatives')->findOrFail($customerId);
        $this->reset('new_rep_name', 'new_rep_email', 'new_rep_phone');
        $this->showRepModal = true;
    }

    // Yeni bir yetkili ekle (Mevcut firmaya)
    public function addRepresentative()
    {
        $this->validate([
            'new_rep_name' => 'required|min:3|max:50|string',
            'new_rep_title' => 'nullable|string|max:100', // Validasyon
            // DÜZELTİLEN KISIM: Silinmiş mailleri kontrol etme
            'new_rep_email' => [
                'required', 
                'email', 
                Rule::unique('users', 'email')->whereNull('deleted_at')
            ],
            'new_rep_phone' => 'nullable|numeric|digits_between:10,15',
        ]);

        $this->createUserForCustomer($this->selectedCustomer, $this->new_rep_name, $this->new_rep_email, $this->new_rep_phone, $this->new_rep_title);
        
        $this->selectedCustomer->refresh(); 
        $this->reset('new_rep_name', 'new_rep_email', 'new_rep_phone', 'new_rep_title');
    }

    // Yetkiliyi sil
    public function deleteRepresentative($userId)
    {
        $user = User::find($userId);
        if($user && $user->customer_id == $this->selectedCustomer->id) {
            $user->delete(); // Soft delete
            $this->selectedCustomer->refresh();
            session()->flash('rep_message', 'Yetkili silindi.');
        }
    }

    // Ortak Kullanıcı Oluşturma Fonksiyonu
    private function createUserForCustomer($customer, $name, $email, $phone, $title = null): void
    {
        $password = Str::random(8);
        
        // 1. Bu mail adresiyle SİLİNMİŞ bir kullanıcı var mı?
        $existingUser = User::withTrashed()->where('email', $email)->first();

        if ($existingUser) {
            // KULLANICI VARSA (Silinmişse geri getir)
            if ($existingUser->trashed()) {
                $existingUser->restore(); // Çöpten çıkar
            }

            // Bilgilerini Güncelle
            $existingUser->update([
                'name' => $name,
                'unvan' => $title, // <--- GÜNCELLENDİ
                'telefon' => $phone,
                'password' => Hash::make($password),
                'is_personnel' => false,
                'customer_id' => $customer->id,
                'onaylandi_mi' => true,
            ]);
            
            $user = $existingUser;
        } else {
            // KULLANICI HİÇ YOKSA (Sıfırdan oluştur)
            $user = User::create([
                'name' => $name,
                'unvan' => $title, // <--- EKLENDİ
                'email' => $email,
                'telefon' => $phone,
                'password' => Hash::make($password),
                'is_personnel' => false,
                'customer_id' => $customer->id,
                'onaylandi_mi' => true,
            ]);
        }

        if (Role::where('name', 'Müşteri Temsilcisi')->exists()) {
            $user->syncRoles(['Müşteri Temsilcisi']);
        }

        session()->flash('message', "Yetkili eklendi: {$name}. Geçici Şifre: {$password}");
        session()->flash('show_password_warning', true); 
        session()->flash('rep_message', "Kullanıcı oluşturuldu. Şifre: {$password}");
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'tax_number', 'tax_office', 'address', 'phone', 
            'rep_name', 'rep_email', 'rep_phone', 'customer_id',
            'new_rep_name', 'new_rep_email', 'new_rep_phone', 'selectedCustomer'
        ]);
        $this->location_type = 'Yurt İçi';
    }

    // 1. Düzenleme Modunu Aç
    public function editRepresentative($id)
    {
        $user = User::findOrFail($id);
        
        // Sadece seçili firmanın yetkilisiyse düzenlemeye izin ver
        if($user->customer_id != $this->selectedCustomer->id) {
            return;
        }

        $this->editingRepId = $user->id;
        $this->edit_rep_name = $user->name;
        $this->edit_rep_email = $user->email;
        $this->edit_rep_phone = $user->telefon;
        $this->edit_rep_title = $user->unvan; // <--- Mevcut ünvanı çek
    }

    // 2. Düzenlemeyi Kaydet
    public function updateRepresentative()
    {
        $this->validate([
            'edit_rep_name' => 'required|min:3|max:50|string',
            // DÜZELTİLEN KISIM: Kendi maili ve silinmişler hariç kontrol
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
            'unvan' => $this->edit_rep_title, // <--- GÜNCELLENDİ
            'email' => $this->edit_rep_email,
            'telefon' => $this->edit_rep_phone,
        ]);

        $this->cancelEditRepresentative();
        $this->selectedCustomer->refresh();
        session()->flash('rep_message', 'Yetkili bilgileri güncellendi.');
    }

    // 3. Düzenlemeyi İptal Et
    public function cancelEditRepresentative()
    {
        $this->editingRepId = null;
        $this->reset('edit_rep_name', 'edit_rep_email', 'edit_rep_phone');
    }

    // Modal kapandığında düzenleme modunu da sıfırla
    public function updatedShowRepModal($value)
    {
        if (!$value) {
            $this->cancelEditRepresentative();
        }
    }
}