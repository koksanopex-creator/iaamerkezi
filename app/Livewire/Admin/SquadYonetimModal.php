<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Iaa;
use App\Models\User;
use App\Models\Takim;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification; // <-- Bu satır çok önemli!

class SquadYonetimModal extends Component
{
    public $showModal = false;
    public $iaaId;
    public $projeBasligi;
    
    public $aramaMetni = ''; 
    public $bulunanKullanicilar = [];
    
    public $mevcutUyeListesi = [];
    public $liderId; 

    protected $listeners = [
        'openSquadModal' => 'open',
        'davetIptalFromOuter' => 'davetIptalFromOuter'
    ];

    public function davetIptalFromOuter($userId, $iaaId)
    {
        $this->iaaId = $iaaId;
        $this->loadProjectData(); // Yetki kontrolü için liderId vb. yüklenmeli
        $this->davetIptal($userId);
    }

    public function open($iaaId)
    {
        $this->iaaId = $iaaId;
        $this->loadProjectData();
        $this->showModal = true;
        $this->aramaMetni = '';
        $this->bulunanKullanicilar = [];
    }

    public function loadProjectData()
    {
        $iaa = Iaa::with(['projeEkibi', 'atananTakim'])->findOrFail($this->iaaId);
        
        $this->projeBasligi = $iaa->baslik;
        $this->mevcutUyeListesi = $iaa->projeEkibi;
        $this->liderId = $iaa->atananTakim->lider_user_id;
    }

    public function updatedAramaMetni()
    {
        // 1. Arama metni çok kısaysa işlem yapma
        if (strlen($this->aramaMetni) < 2) {
            $this->bulunanKullanicilar = [];
            return;
        }

        // 2. Zaten ekipte olanları bul (Listeden çıkarmak için)
        $ekipIds = $this->mevcutUyeListesi->pluck('id')->toArray();

        // 3. Gizlenecek Roller Listesi
        $gizlenecekRoller = [
            'Superadmin', 
            'Yonetim', 
            'Dış Avukat', 
            'Müşteri', 
            'Arabuluculuk Finans',
            'Hukuk Yöneticisi' // İhtiyaca göre ekleyebilirsiniz
        ];

        // 4. SORGULAMA VE FİLTRELEME
        $this->bulunanKullanicilar = User::where('name', 'like', '%' . $this->aramaMetni . '%')
            ->where('onaylandi_mi', true)
            
            // FİLTRE 1: Sadece Personel Olanlar (Müşterileri Garanti Gizler)
            // Eğer veritabanınızda is_personnel sütunu varsa bu en garanti yoldur.
            ->where('is_personnel', true) 

            // FİLTRE 2: Zaten ekipte olanları gösterme
            ->whereNotIn('id', $ekipIds)

            // FİLTRE 3: Yasaklı Rollere Sahip Olanları Gösterme
            ->whereDoesntHave('roles', function ($q) use ($gizlenecekRoller) {
                $q->whereIn('name', $gizlenecekRoller);
            })
            
            ->take(5)
            ->get();
    }

    public function uyeEkle($userId)
    {
        $iaa = Iaa::find($this->iaaId);
        
        if (Auth::id() != $this->liderId && !Auth::user()->hasRole('Superadmin')) {
            return; 
        }

        $exists = $iaa->projeEkibi()->where('user_id', $userId)->exists();

        if (!$exists) {
            $isQualityManager = app(\App\Services\ProjectWorkspace\ProjeCalismaAlaniService::class)->isQualityManagerWithInterventionPower(User::find($userId), $iaa);
            $durum = ($isQualityManager && $userId == Auth::id()) ? 'onaylandi' : 'bekliyor';

            $iaa->projeEkibi()->attach($userId, [
                'rol' => 'Üye',
                'durum' => $durum
            ]);

            // --- BİLDİRİMLER ---
            $davetEdilenUser = User::find($userId);
            $lider = Auth::user(); 
            
            if ($davetEdilenUser) {
                // 1. Personele Bildirim (Seni Davet Ettim)
                Notification::send(
                    $davetEdilenUser, 
                    new \App\Notifications\ProjeEkipDaveti($iaa, $lider)
                );

                // 2. Müdüre Bildirim (Personelin Davet Edildi)
                $this->notifyManager($davetEdilenUser, $iaa, 'davet');
            }
            
            session()->flash('success', 'Kullanıcıya davet gönderildi.');
        } else {
            session()->flash('error', 'Bu kullanıcı zaten ekipte veya davet edilmiş.');
        }

        $this->aramaMetni = '';
        $this->bulunanKullanicilar = [];
        $this->loadProjectData();
    }

    public function uyeCikar($userId)
    {
        if ($userId == $this->liderId) {
            return;
        }

        $iaa = Iaa::find($this->iaaId);
        
        if (Auth::id() != $this->liderId && !Auth::user()->hasRole('Superadmin')) {
            return;
        }

        $cikarilacakUye = User::find($userId);
        $lider = Auth::user();

        if ($cikarilacakUye) {
            // 1. Personele Bildirim (Çıkarıldın)
            try {
                Notification::send(
                    $cikarilacakUye, 
                    new \App\Notifications\ProjeEkibindenCikarildi($iaa, $lider)
                );
            } catch (\Exception $e) {
                // Hata olursa logla ama işlemi durdurma
                \Illuminate\Support\Facades\Log::error('Üye çıkarma bildirimi hatası: ' . $e->getMessage());
            }

            // 2. Müdüre Bildirim (Personelin Çıkarıldı)
            // === KRİTİK NOKTA BURASI ===
            $this->notifyManager($cikarilacakUye, $iaa, 'cikarildi');
        }

        $iaa->projeEkibi()->detach($userId);

        $this->loadProjectData();
        session()->flash('success', 'Üye proje ekibinden çıkarıldı.');
    }

    public function davetIptal($userId)
    {
        $iaa = Iaa::find($this->iaaId);
        
        // Yetki Kontrolü
        if (Auth::id() != $this->liderId && !Auth::user()->hasRole('Superadmin')) {
            return;
        }

        // 1. Kullanıcıyı ekipten çıkar (Pivot kaydını sil)
        $iaa->projeEkibi()->detach($userId);

        // 2. Gönderilen bildirimleri temizle (Zil bildirimleri)
        // Hem kullanıcıya giden daveti hem de müdürüne giden bilgilendirmeyi sileriz
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('data->iaa_id', $this->iaaId)
            ->where('data->invited_user_id', $userId)
            ->whereIn('type', [
                'App\Notifications\ProjeEkipDaveti',
                'App\Notifications\PersonelProjeyeDavetEdildi'
            ])
            ->delete();

        $this->loadProjectData();
        session()->flash('success', 'Davet iptal edildi ve gönderilen bildirimler temizlendi.');
        $this->redirect(route('proje.workspace.show', $this->iaaId));
    }

    public function close()
    {
        $this->showModal = false;
        $this->redirect(route('proje.workspace.show', $this->iaaId));
    }

    public function render()
    {
        return view('livewire.admin.squad-yonetim-modal');
    }

    // === MÜDÜRÜ BULMA VE BİLDİRİM GÖNDERME FONKSİYONU ===
    private function notifyManager($personel, $iaa, $islemTipi)
    {
        // 1. Personelin bölümü var mı?
        if ($personel->bolum_id) {
            
            // 2. O bölümün liderlerini bul (Kendisi hariç)
            $mudurler = User::role('Bölüm Lideri')
                            ->where('bolum_id', $personel->bolum_id)
                            ->where('id', '!=', $personel->id)
                            ->get();

            // 3. Müdür varsa bildirimi gönder
            if ($mudurler->isNotEmpty()) {
                // Parametre olarak 'davet' veya 'cikarildi' gönderiyoruz
                Notification::send(
                    $mudurler, 
                    new \App\Notifications\PersonelProjeyeDavetEdildi($iaa, $personel, $islemTipi)
                );
            }
        }
    }
}