<?php

namespace App\Livewire\Admin\Ayarlar;

use Livewire\Component;
use App\Models\RaporKurali;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;
use App\Mail\OtomatikYoneticiRaporu;
use App\Services\RaporVeriServisi;
use Illuminate\Support\Facades\Mail;

class RaporKurallari extends Component
{
    // Liste Modu
    public $kurallar;
    
    // Form Modu (Modal)
    public $aktifKuralId = null;
    public $isModalOpen = false;

    // Form Alanları
    public $baslik;
    public $periyot = 'gunluk';
    public $gonderim_saati = '09:00';

    public $gunler = []; // <-- YENİ DEĞİŞKEN (Günleri tutacak dizi)
    
    // ALICILAR
    public $secili_roller = [];
    public $secili_users = [];
    public $harici_mailler = '';

    // İÇERİK AYARLARI
    public $icerik = [
        'sikayet_ozet' => false,
        'sikayet_detay' => false,
        'iaa_ozet' => false,
        'iaa_havuz' => false,
        'disiplin_ozet' => false,
        'arabuluculuk_ozet' => false,
    ];

    protected $rules = [
        'baslik' => 'required|string|max:255',
        'periyot' => 'required',
        'gonderim_saati' => 'required',
        'gunler' => 'nullable', // Validation kuralı eklendi
    ];

    public function render()
    {
        $this->kurallar = RaporKurali::all();
        
        // Spatie Role kontrolü (Spatie yoksa hata vermemesi için try-catch veya class_exists kontrolü yapılabilir ama burada var varsayıyoruz)
        $roller = \Spatie\Permission\Models\Role::all(); 
        
        $users = User::orderBy('name')->get(); 

        return view('livewire.admin.ayarlar.rapor-kurallari', compact('roller', 'users'));
    }

    public function yeniKural()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function duzenle($id)
    {
        $kural = RaporKurali::findOrFail($id);
        $this->aktifKuralId = $kural->id;
        $this->baslik = $kural->baslik;
        $this->periyot = $kural->periyot;
        
        // VERİTABANINDAN GÜNLERİ ÇEKME
        $this->gunler = $kural->gunler ?? []; 
        
        $this->gonderim_saati = \Carbon\Carbon::parse($kural->gonderim_saati)->format('H:i');
        
        // Alıcıları parse et
        $this->secili_roller = $kural->alicilar['roller'] ?? [];
        $this->secili_users = $kural->alicilar['users'] ?? [];
        $this->harici_mailler = $kural->alicilar['emails'] ?? '';

        // İçerikleri parse et
        // Mevcut varsayılan ayarlar ile veritabanından gelenleri birleştir
        $this->icerik = array_merge($this->icerik, $kural->icerik_ayarlari ?? []);

        $this->isModalOpen = true;
    }

    public function kaydet()
    {
        $this->validate();

        $alicilarData = [
            'roller' => $this->secili_roller,
            'users' => $this->secili_users,
            'emails' => $this->harici_mailler
        ];

        // MANTIK: Eğer periyot 'gunluk' ise, seçilen günlerin bir anlamı yoktur, veritabanını kirletmemek için null kaydedelim.
        // Ama 'haftalik' veya 'aylik' ise $this->gunler dizisini kaydedelim.
        $kaydedilecekGunler = $this->periyot === 'gunluk' ? null : $this->gunler;

        if ($this->aktifKuralId) {
            // GÜNCELLEME İŞLEMİ
            $kural = RaporKurali::find($this->aktifKuralId);
            $kural->update([
                'baslik' => $this->baslik,
                'periyot' => $this->periyot,
                'gonderim_saati' => $this->gonderim_saati,
                'gunler' => $kaydedilecekGunler, // <-- BURAYA EKLENDİ
                'alicilar' => $alicilarData,
                'icerik_ayarlari' => $this->icerik,
            ]);
            session()->flash('success', 'Kural güncellendi.');
        } else {
            // YENİ KAYIT İŞLEMİ
            RaporKurali::create([
                'baslik' => $this->baslik,
                'periyot' => $this->periyot,
                'gonderim_saati' => $this->gonderim_saati,
                'gunler' => $kaydedilecekGunler, // <-- BURAYA EKLENDİ
                'alicilar' => $alicilarData,
                'icerik_ayarlari' => $this->icerik,
            ]);
            session()->flash('success', 'Yeni rapor kuralı oluşturuldu.');
        }

        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function sil($id)
    {
        RaporKurali::destroy($id);
        session()->flash('success', 'Kural silindi.');
    }
    
    public function manuelGonder($id)
    {
        $kural = RaporKurali::findOrFail($id);
        
        // 1. Verileri Topla
        $servis = new RaporVeriServisi();
        $raporData = $servis->verileriTopla($kural->icerik_ayarlari ?? []);

        // 2. Alıcı Listesini Oluştur
        $alicilar = collect();

        // a. Rollerdeki Kullanıcılar
        if (!empty($kural->alicilar['roller'])) {
            foreach ($kural->alicilar['roller'] as $roleId) {
                // Role ID'den ismi bul, sonra o isme sahip userları çek
                $role = Role::find($roleId);
                if ($role) {
                    $usersWithRole = User::role($role->name)->pluck('email');
                    $alicilar = $alicilar->merge($usersWithRole);
                }
            }
        }

        // b. Doğrudan Seçilen Kullanıcılar
        if (!empty($kural->alicilar['users'])) {
            $directUsers = User::whereIn('id', $kural->alicilar['users'])->pluck('email');
            $alicilar = $alicilar->merge($directUsers);
        }

        // c. Harici E-postalar
        if (!empty($kural->alicilar['emails'])) {
            // String mi Array mi kontrolü (eski kayıtlardan string gelebilir)
            $external = is_array($kural->alicilar['emails']) 
                        ? $kural->alicilar['emails'] 
                        : explode(',', $kural->alicilar['emails']);

            $externalEmails = array_map('trim', $external);
            $alicilar = $alicilar->merge($externalEmails);
        }

        // Tekilleştir ve Boşları Temizle
        $alicilar = $alicilar->filter()->unique();

        // 3. Mail Gönder
        if ($alicilar->isEmpty()) {
            session()->flash('error', 'Bu kural için geçerli bir alıcı bulunamadı.');
            return;
        }

        foreach ($alicilar as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($email)->send(new OtomatikYoneticiRaporu($raporData, $kural->baslik));
                } catch (\Exception $e) {
                    \Log::error("Rapor mail hatası ($email): " . $e->getMessage());
                }
            }
        }

        // 4. Son Gönderim Tarihini Güncelle
        $kural->update(['son_gonderim_tarihi' => now()]);

        session()->flash('success', 'Rapor başarıyla ' . $alicilar->count() . ' kişiye gönderildi.');
    }

    private function resetForm()
    {
        // 'gunler' değişkenini de sıfırlamayı unutmuyoruz
        $this->reset(['baslik', 'periyot', 'gonderim_saati', 'gunler', 'secili_roller', 'secili_users', 'harici_mailler', 'aktifKuralId']);
        
        foreach($this->icerik as $key => $val) {
            $this->icerik[$key] = false;
        }
    }
}