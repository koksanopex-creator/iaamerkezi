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
    
    // Disiplin Kapsam ve Filtreleri
    public $disiplin_kapsam = 'tum_veriler';
    public $disiplin_suc_kategorileri = [];
    
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
        
        $users = User::with('bolum')
            ->where('is_personnel', true)
            ->where('is_mavi_yaka', false)
            ->orderBy('name')
            ->get(); 
            
        $disiplinKategorileri = \App\Models\DisciplinaryCategory::orderBy('ad')->get();

        return view('livewire.admin.ayarlar.rapor-kurallari', compact('roller', 'users', 'disiplinKategorileri'));
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

        // Disiplin Ayarları
        $this->disiplin_kapsam = $kural->disiplin_kapsam ?? 'tum_veriler';
        $this->disiplin_suc_kategorileri = $kural->disiplin_suc_kategorileri ?? [];

        // İçerikleri parse et
        // Mevcut varsayılan ayarlar ile veritabanından gelenleri birleştir
        $this->icerik = array_merge($this->icerik, $kural->icerik_ayarlari ?? []);

        $this->isModalOpen = true;
    }

    /**
     * Rol seçildiğinde o role ait kullanıcıları otomatik seçili kullanıcılar listesine ekler.
     */
    public function updatedSeciliRoller($value)
    {
        if (empty($value)) return;

        // Seçilen rollerdeki tüm kullanıcı ID'lerini al
        $roleNames = Role::whereIn('id', (array)$value)->pluck('name')->toArray();
        $userIds = User::role($roleNames)
            ->where('is_personnel', true)
            ->where('is_mavi_yaka', false)
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();

        // Mevcut seçili olanlarla birleştir (tekilleştir)
        $this->secili_users = array_unique(array_merge($this->secili_users, $userIds));
        
        // Select2'yi güncellemek için event fırlat (Opsiyonel, x-init hookumuz hallediyor olmalı)
        $this->dispatch('users-updated', ids: $this->secili_users);
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
                'disiplin_kapsam' => $this->disiplin_kapsam,
                'disiplin_suc_kategorileri' => empty($this->disiplin_suc_kategorileri) ? null : $this->disiplin_suc_kategorileri,
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
                'disiplin_kapsam' => $this->disiplin_kapsam,
                'disiplin_suc_kategorileri' => empty($this->disiplin_suc_kategorileri) ? null : $this->disiplin_suc_kategorileri,
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
        
        // Veri toplama işlemi döngü içine taşındığı için buradan kaldırıldı.

        // 2. Alıcı Listesini Oluştur
        $alicilar = collect();

        // a. Rollerdeki Kullanıcılar
        if (!empty($kural->alicilar['roller'])) {
            foreach ($kural->alicilar['roller'] as $roleId) {
                $role = Role::find($roleId);
                if ($role) {
                    $usersWithRole = User::role($role->name)->get();
                    foreach ($usersWithRole as $u) {
                        // AKILLI FİLTRE KALDIRILDI - Kullanıcı tam kontrol istedi
                        $alicilar->push($u->email);
                    }
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

        $alicilar = $alicilar->filter()->unique();

        // 3. Mail Gönder
        if ($alicilar->isEmpty()) {
            session()->flash('error', 'Bu kural için geçerli bir alıcı bulunamadı.');
            return;
        }

        // Eğer rapor disiplin detaylarını içeriyor ve "kendi_bolumu" seçilmişse:
        $isDisiplinScoped = ($kural->icerik_ayarlari['disiplin_ozet'] ?? false) && $kural->disiplin_kapsam === 'kendi_bolumu';

        foreach ($alicilar as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $user = User::where('email', $email)->first();
                    $raporIcerikData = null;

                    // Eğer disiplin için kişiye özel kapsam gerekiyorsa, o kişiye özel veri oluştur
                    if ($isDisiplinScoped && $user) {
                        $kisiselServis = new RaporVeriServisi();
                        $kisiselServis->setUser($user); // user set edildiği için bolum_id filtrelemesi yapılacak
                        
                        // Kuralda seçilen spesifik kategorileri servise iletelim
                        if (!empty($kural->disiplin_suc_kategorileri)) {
                            $kisiselServis->setDisiplinKategoriFiltresi($kural->disiplin_suc_kategorileri);
                        }

                        $raporIcerikData = $kisiselServis->verileriTopla($kural->icerik_ayarlari ?? []);
                    } elseif ($user && !empty($kural->disiplin_suc_kategorileri) && ($kural->icerik_ayarlari['disiplin_ozet'] ?? false)) {
                        // Kendi bölümü değil ama spesifik kategori filtresi varsa
                        $kisiselServis = new RaporVeriServisi();
                        $kisiselServis->setUser(null); // Tüm bölümler (Global)
                        $kisiselServis->setDisiplinKategoriFiltresi($kural->disiplin_suc_kategorileri);
                        $raporIcerikData = $kisiselServis->verileriTopla($kural->icerik_ayarlari ?? []);
                    } else {
                        // Global (Herkes için aynı üretilmiş olan) veri
                        if (!isset($globalRaporData)) {
                            $globalServis = new RaporVeriServisi();
                            if (!empty($kural->disiplin_suc_kategorileri)) {
                                $globalServis->setDisiplinKategoriFiltresi($kural->disiplin_suc_kategorileri);
                            }
                            $globalRaporData = $globalServis->verileriTopla($kural->icerik_ayarlari ?? []);
                        }
                        $raporIcerikData = $globalRaporData;
                    }

                    Mail::to($email)->queue(new OtomatikYoneticiRaporu($raporIcerikData, $kural->baslik));
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
        $this->reset(['baslik', 'periyot', 'gonderim_saati', 'gunler', 'secili_roller', 'secili_users', 'harici_mailler', 'aktifKuralId', 'disiplin_kapsam', 'disiplin_suc_kategorileri']);
        
        foreach($this->icerik as $key => $val) {
            $this->icerik[$key] = false;
        }
    }
}