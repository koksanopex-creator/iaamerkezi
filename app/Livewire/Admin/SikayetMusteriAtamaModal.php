<?php

namespace App\Livewire\Admin;

use App\Models\MusteriSikayeti;
use App\Models\Customer;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Livewire\WithFileUploads;

class SikayetMusteriAtamaModal extends Component
{
    use WithFileUploads;

    public $sikayetId;
    public $sikayet;
    public $showModal = false;

    public $activeTab = 'mevcut'; // 'mevcut' veya 'yeni'

    // Mevcut Müşteri Seçimi
    public $customers;
    public $representatives = [];
    public $selectedCustomerId = null;
    public $selectedRepId = null;
    public $search = '';

    // Yeni Müşteri Form Verileri (Zengin Form)
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

    protected $listeners = ['openMusteriAtamaModal' => 'open'];

    public function open($sikayetId)
    {
        $this->sikayetId = $sikayetId;
        $this->sikayet = MusteriSikayeti::find($sikayetId);
        
        if (!$this->sikayet) return;

        // Yetki Kontrolü
        if (!$this->canAssign()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Bu şikayete müşteri atama yetkiniz bulunmuyor.']);
            return;
        }

        $this->resetForm();
        $this->customers = Customer::orderBy('name')->get();
        $this->showModal = true;
    }

    protected function resetForm()
    {
        $this->reset(['selectedCustomerId', 'selectedRepId', 'search', 'name', 'tax_number', 'phone', 'address', 'logo']);
        $this->activeTab = 'mevcut';
        $this->reps = [
            [
                'name' => '',
                'title' => '',
                'email' => '',
                'phone' => '',
            ]
        ];
    }

    protected function canAssign()
    {
        $user = Auth::user();
        if ($user->hasAnyRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Yonetim'])) {
            return true;
        }

        if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            $yonetilenKategoriIds = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
            if (empty($yonetilenKategoriIds) && $user->bolum_id) {
                $yonetilenKategoriIds = \App\Models\SikayetKategori::where('bolum_id', $user->bolum_id)->pluck('id')->toArray();
            }
            return in_array($this->sikayet->sikayet_kategorisi_id, $yonetilenKategoriIds);
        }

        return false;
    }

    public function updatedSelectedCustomerId($value)
    {
        $this->selectedRepId = null;
        if ($value) {
            $this->representatives = User::where('customer_id', $value)->get();
        } else {
            $this->representatives = [];
        }
    }

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
        if (count($this->reps) > 1) {
            unset($this->reps[$index]);
            $this->reps = array_values($this->reps);
        }
    }

    public function assign()
    {
        if (!$this->canAssign()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Bu işlem için yetkiniz bulunmuyor.']);
            return;
        }

        DB::beginTransaction();
        try {
            if ($this->activeTab === 'mevcut') {
                $this->validate([
                    'selectedCustomerId' => 'required',
                    'selectedRepId' => 'required',
                ]);

                $customer = Customer::find($this->selectedCustomerId);
            } else {
                // Yeni Müşteri Oluşturma
                $this->validate([
                    'name' => 'required|min:3|max:150',
                    'reps.*.name' => 'required|min:3',
                    'reps.*.email' => 'required|email|unique:users,email',
                    'logo' => 'nullable|image|max:2048',
                ], [
                    'reps.*.name.required' => 'Yetkili adı zorunludur.',
                    'reps.*.email.required' => 'E-posta zorunludur.',
                    'reps.*.email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
                ]);

                $logoPath = $this->logo ? $this->logo->store('musteri-logolari', 'public') : null;

                $customer = Customer::create([
                    'name' => $this->name,
                    'tax_number' => $this->tax_number,
                    'location_type' => $this->location_type,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'logo_path' => $logoPath,
                ]);

                $createdUserIds = [];
                foreach ($this->reps as $repData) {
                    $password = Str::random(8);
                    $newUser = User::create([
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
                        $newUser->assignRole('Müşteri Temsilcisi');
                    }

                    $createdUserIds[] = $newUser->id;
                }

                $this->selectedCustomerId = $customer->id;
                $this->selectedRepId = $createdUserIds[0];
            }

            // Şikayeti güncelle
            $this->sikayet->update([
                'customer_id' => $this->selectedCustomerId,
                'yetkili_user_id' => $this->selectedRepId,
                'musteri_adi' => $customer->name,
            ]);

            // Log Ekle
            \App\Models\MusteriSikayetiLog::create([
                'musteri_sikayeti_id' => $this->sikayet->id,
                'user_id' => Auth::id(),
                'eylem' => 'Müşteri Atandı',
                'islem_aciklamasi' => "Misafir şikayeti {$customer->name} müşterisine atandı.",
            ]);

            DB::commit();

            $this->showModal = false;
            $this->dispatch('sikayetGuncellendi');
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Müşteri başarıyla atandı ve Takvim ile senkronize edildi.']);
            
            // Eğer detay sayfasındaysak sayfayı yenilemek için redirect
            return redirect()->route('admin.sikayetler.show', $this->sikayet->id);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.admin.sikayet-musteri-atama-modal');
    }
}
