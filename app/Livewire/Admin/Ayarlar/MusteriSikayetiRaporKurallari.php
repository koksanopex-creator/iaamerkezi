<?php

namespace App\Livewire\Admin\Ayarlar;

use Livewire\Component;
use App\Models\MusteriSikayetiYoneticiRaporKurali;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Notifications\MusteriSikayetiManagerReportNotification;
use App\Models\MusteriSikayeti;
use Illuminate\Support\Facades\Notification;

class MusteriSikayetiRaporKurallari extends Component
{
    // Liste Modu
    public $kurallar;
    
    // Form Modu (Modal)
    public $aktifKuralId = null;
    public $isModalOpen = false;
    public $isPreviewModalOpen = false;

    // Form Alanları
    public $ad;
    public $aktif = true;
    public $siklik = 'gunluk';
    public $periyot = 1;
    public $saat = '09:00';
    public $haftanin_gunleri = [];
    public $ayin_gunleri = [];
    public $mail_aktif_et = true;
    public $zili_aktif_et = true;
    public $mail_konusu = '';
    public $mail_taslagi = '';
    public $bildirim_metni = '';
    public $rapor_kapsami = 'tum_kurul';

    // ALICILAR
    public $secili_roller = [];
    public $secili_users = [];
    public $harici_mailler = '';

    // Önizleme İçin
    public $previewData = [];

    protected $rules = [
        'ad' => 'required|string|max:255',
        'siklik' => 'required|in:gunluk,haftalik,aylik',
        'periyot' => 'required|integer|min:1',
        'saat' => 'required',
        'haftanin_gunleri' => 'nullable|array',
        'ayin_gunleri' => 'nullable|array',
        'mail_konusu' => 'nullable|string|max:255',
        'mail_taslagi' => 'nullable|string',
        'bildirim_metni' => 'nullable|string|max:255',
        'rapor_kapsami' => 'required|in:tum_kurul,yurt_ici_kurul,yurt_disi_kurul',
    ];

    public function render()
    {
        $this->kurallar = MusteriSikayetiYoneticiRaporKurali::all();
        
        $roller = Role::all(); 
        
        $users = User::with('bolum')
            ->where('is_personnel', true)
            ->where('is_mavi_yaka', false)
            ->orderBy('name')
            ->get(); 

        return view('livewire.admin.ayarlar.musteri-sikayeti-rapor-kurallari', compact('roller', 'users'));
    }

    public function yeniKural()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function duzenle($id)
    {
        $kural = MusteriSikayetiYoneticiRaporKurali::findOrFail($id);
        $this->aktifKuralId = $kural->id;
        
        $this->ad = $kural->ad;
        $this->aktif = $kural->aktif;
        $this->siklik = $kural->siklik;
        $this->periyot = $kural->periyot ?? 1;
        $this->saat = \Carbon\Carbon::parse($kural->saat)->format('H:i');
        $this->haftanin_gunleri = $kural->haftanin_gunleri ?? [];
        $this->ayin_gunleri = $kural->ayin_gunleri ?? [];
        
        $this->mail_aktif_et = $kural->mail_aktif_et;
        $this->zili_aktif_et = $kural->zili_aktif_et;
        $this->mail_konusu = $kural->mail_konusu;
        $this->mail_taslagi = $kural->mail_taslagi;
        $this->bildirim_metni = $kural->bildirim_metni;
        $this->rapor_kapsami = $kural->rapor_kapsami ?? 'tum_kurul';
        
        // Alıcıları parse et
        $this->secili_roller = $kural->alicilar['roller'] ?? [];
        $this->secili_users = $kural->alicilar['users'] ?? [];
        $this->harici_mailler = $kural->alicilar['emails'] ?? '';

        $this->isModalOpen = true;
    }

    public function updatedSeciliRoller($value)
    {
        if (empty($value)) return;

        $roleNames = Role::whereIn('id', (array)$value)->pluck('name')->toArray();
        $userIds = User::role($roleNames)
            ->where('is_personnel', true)
            ->where('is_mavi_yaka', false)
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();

        $this->secili_users = array_unique(array_merge($this->secili_users, $userIds));
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

        $kaydedilecekGunler = $this->siklik === 'haftalik' ? $this->haftanin_gunleri : null;
        $kaydedilecekAyGunleri = $this->siklik === 'aylik' ? $this->ayin_gunleri : null;

        $data = [
            'ad' => $this->ad,
            'aktif' => $this->aktif,
            'siklik' => $this->siklik,
            'periyot' => $this->periyot,
            'saat' => $this->saat,
            'haftanin_gunleri' => $kaydedilecekGunler,
            'ayin_gunleri' => $kaydedilecekAyGunleri,
            'mail_aktif_et' => $this->mail_aktif_et,
            'zili_aktif_et' => $this->zili_aktif_et,
            'mail_konusu' => $this->mail_konusu,
            'mail_taslagi' => $this->mail_taslagi,
            'bildirim_metni' => $this->bildirim_metni,
            'alicilar' => $alicilarData,
            'rapor_kapsami' => $this->rapor_kapsami,
        ];

        if ($this->aktifKuralId) {
            $kural = MusteriSikayetiYoneticiRaporKurali::find($this->aktifKuralId);
            $kural->update($data);
            session()->flash('success', 'Kural güncellendi.');
        } else {
            MusteriSikayetiYoneticiRaporKurali::create($data);
            session()->flash('success', 'Yeni rapor kuralı oluşturuldu.');
        }

        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function sil($id)
    {
        MusteriSikayetiYoneticiRaporKurali::destroy($id);
        session()->flash('success', 'Kural silindi.');
    }
    
    private function calculateEkipPerformansi($kapsam)
    {
        $roller = [];
        if ($kapsam === 'tum_kurul') {
            $roller = [
                'Müşteri Şikayeti Kurulu', 
                'Müşteri Şikayeti Kurulu - Yurt İçi', 
                'Müşteri Şikayeti Kurulu - Yurt Dışı'
            ];
        } elseif ($kapsam === 'yurt_ici_kurul') {
            $roller = ['Müşteri Şikayeti Kurulu - Yurt İçi'];
        } elseif ($kapsam === 'yurt_disi_kurul') {
            $roller = ['Müşteri Şikayeti Kurulu - Yurt Dışı'];
        }

        $kurulUyeleri = User::role($roller)->get();
        $yediGunOnce = now()->subDays(7);
        $ekipPerformansi = [];

        foreach ($kurulUyeleri as $uye) {
            $baseQuery = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $uye->id);
            
            $toplam = (clone $baseQuery)->count();
            $cozumlenen = (clone $baseQuery)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])->count();
            $iptalRed = (clone $baseQuery)->whereIn('musteri_durum', ['İptal Edildi', 'Reddedildi', 'Tamamlanması Reddedildi'])->count();
            $son7Gun = (clone $baseQuery)->where('created_at', '>=', $yediGunOnce)->count();

            $ekipPerformansi[] = (object)[
                'name' => $uye->name,
                'toplam' => $toplam,
                'cozumlenen' => $cozumlenen,
                'iptal_red' => $iptalRed,
                'son_7_gun' => $son7Gun
            ];
        }

        return $ekipPerformansi;
    }

    public function openPreview()
    {
        // Gerçekte ne göstereceğini anlamak için form verilerini al
        $this->previewData = [
            'ad' => $this->ad ?: 'Test Kuralı',
            'mail_konusu' => $this->mail_konusu ?: 'Örnek Mail Konusu',
            'mail_taslagi' => $this->mail_taslagi ?: 'Merhaba, bu bir önizlemedir.',
            'bildirim_metni' => $this->bildirim_metni ?: 'Örnek zil bildirimi',
            'mail_aktif' => $this->mail_aktif_et,
            'zil_aktif' => $this->zili_aktif_et,
            'ekip_performansi' => $this->calculateEkipPerformansi($this->rapor_kapsami),
        ];
        
        $this->isPreviewModalOpen = true;
    }

    public function closePreview()
    {
        $this->isPreviewModalOpen = false;
    }

    public function manuelGonder($id)
    {
        $kural = MusteriSikayetiYoneticiRaporKurali::findOrFail($id);
        
        \Artisan::call('sikayet:yonetici-raporlari-gonder', ['--kural_id' => $kural->id]);
        
        session()->flash('success', 'Rapor manuel olarak başarıyla gönderildi.');
    }

    private function resetForm()
    {
        $this->reset([
            'aktifKuralId', 'ad', 'aktif', 'siklik', 'periyot', 'saat', 'haftanin_gunleri', 'ayin_gunleri',
            'mail_aktif_et', 'zili_aktif_et', 'mail_konusu', 'mail_taslagi',
            'bildirim_metni', 'rapor_kapsami', 'secili_roller', 'secili_users', 'harici_mailler'
        ]);
        
        $this->siklik = 'gunluk';
        $this->periyot = 1;
        $this->saat = '09:00';
        $this->aktif = true;
        $this->mail_aktif_et = true;
        $this->zili_aktif_et = true;
        $this->rapor_kapsami = 'tum_kurul';
    }
}
