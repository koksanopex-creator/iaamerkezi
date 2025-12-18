<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Livewire\WithFileUploads;

class SikayetMusteriSecimi extends Component
{
    use WithFileUploads;

    // Seçim Verileri
    public $customers;
    public $representatives = [];
    
    // Formdan Gelen/Giden Veriler
    public $selectedCustomerId = null;
    public $selectedRepId = null;

    // Modal Kontrolü
    public $showCreateModal = false;

    // Hızlı Ekleme Form Verileri
    public $name, $tax_number, $location_type = 'Yurt İçi', $phone, $address;
    public $reps = [
        [
            'name' => '',
            'title' => '',
            'email' => '',
            'phone' => '',
        ]
    ];
    public $logo;

    // Blade'den gelen parametre isimleriyle eşleşmeli
    public function mount($selectedCustomerId = null, $selectedRepId = null)
    {
        $this->customers = Customer::orderBy('name')->get();

        // Eğer bir müşteri ID'si geldiyse işlemleri başlat
        if ($selectedCustomerId) {
            $this->selectedCustomerId = $selectedCustomerId;
            
            // O firmaya ait yetkilileri hemen yükle
            $this->representatives = User::where('customer_id', $selectedCustomerId)->get();
            
            // Eğer yetkili ID'si de geldiyse onu da seçili yap
            if ($selectedRepId) {
                $this->selectedRepId = $selectedRepId;
            }
        }
    }

    // Firma seçildiğinde yetkilileri getir
    public function updatedSelectedCustomerId($value)
    {
        $this->selectedRepId = null; // Firmayı değiştirince yetkiliyi sıfırla
        if ($value) {
            $this->representatives = User::where('customer_id', $value)->get();
        } else {
            $this->representatives = [];
        }
    }

    // === YENİ: SATIR EKLEME/SİLME FONKSİYONLARI ===
    
    public function addRepRow()
    {
        $this->reps[] = [
            'name' => '',
            'title' => '',
            'email' => '',
            'phone' => '',
        ];
    }

    public function removeRepRow($index)
    {
        // En az 1 satır kalsın istersen bu kontrolü açabilirsin
        if (count($this->reps) > 1) {
            unset($this->reps[$index]);
            $this->reps = array_values($this->reps); // Diziyi yeniden indeksle
        }
    }

    // === KAYDETME İŞLEMİ ===

    // HIZLI EKLEME FONKSİYONU
    public function storeNewCustomer()
    {
        // Validasyon Kuralları (Dizi için özel yazım)
        $this->validate([
            'name' => 'required|min:3|max:150',
            
            // Dizi Validasyonları (* işareti her satırı kontrol eder)
            'reps.*.name' => 'required|min:3',
            'reps.*.email' => 'required|email|unique:users,email',
        ], [
            'reps.*.name.required' => 'Yetkili adı zorunludur.',
            'reps.*.email.required' => 'E-posta zorunludur.',
            'reps.*.email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
        ]);

        // 1. Müşteriyi Oluştur
        $logoPath = $this->logo ? $this->logo->store('musteri-logolari', 'public') : null;

        $customer = Customer::create([
            'name' => $this->name,
            'tax_number' => $this->tax_number,
            'location_type' => $this->location_type,
            'phone' => $this->phone,
            'address' => $this->address,
            'logo_path' => $logoPath,
        ]);

        // 2. Yetkilileri Döngüyle Oluştur
        $createdUserIds = [];
        $passwords = [];

        foreach ($this->reps as $repData) {
            $password = Str::random(8);
            
            $user = User::create([
                'name' => $repData['name'],
                'email' => $repData['email'],
                'unvan' => $repData['title'],
                'telefon' => $repData['phone'],
                'password' => Hash::make($password),
                'is_personnel' => false,
                'customer_id' => $customer->id,
                'onaylandi_mi' => true,
            ]);

            if (Role::where('name', 'Müşteri Temsilcisi')->exists()) {
                $user->assignRole('Müşteri Temsilcisi');
            }

            $createdUserIds[] = $user->id;
            $passwords[] = "{$repData['name']}: {$password}";
        }

        // 3. Listeleri Güncelle
        $this->customers = Customer::orderBy('name')->get();
        $this->selectedCustomerId = $customer->id;
        $this->updatedSelectedCustomerId($customer->id);
        
        // İlk eklenen yetkiliyi seçili yap
        if (!empty($createdUserIds)) {
            $this->selectedRepId = $createdUserIds[0];
        }

        // 4. Temizle ve Kapat
        $this->showCreateModal = false;
        $this->reset(['name', 'tax_number', 'phone', 'address', 'logo']);
        
        // Reps dizisini sıfırla
        $this->reps = [[ 'name' => '', 'title' => '', 'email' => '', 'phone' => '' ]];
        
        session()->flash('message', "Müşteri ve " . count($createdUserIds) . " yetkili eklendi. Şifreler: " . implode(', ', $passwords));
    }

    public function render()
    {
        return view('livewire.admin.sikayet-musteri-secimi');
    }
}