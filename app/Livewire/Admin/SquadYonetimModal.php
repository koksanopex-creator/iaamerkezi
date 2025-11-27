<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Iaa;
use App\Models\User;
use App\Models\Takim;
use Illuminate\Support\Facades\Auth;

class SquadYonetimModal extends Component
{
    public $showModal = false;
    public $iaaId;
    public $projeBasligi;
    
    public $aramaMetni = ''; 
    public $bulunanKullanicilar = []; // Sadece User objeleri (pivot yok)
    
    public $mevcutUyeListesi = []; // User objeleri + pivot verisi
    public $liderId; 

    protected $listeners = ['openSquadModal' => 'open'];

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
        // with('projeEkibi') sayesinde pivot verileri de gelir
        $iaa = Iaa::with(['projeEkibi', 'atananTakim'])->findOrFail($this->iaaId);
        
        $this->projeBasligi = $iaa->baslik;
        $this->mevcutUyeListesi = $iaa->projeEkibi; // Collection olarak alıyoruz
        $this->liderId = $iaa->atananTakim->lider_user_id;
    }

    public function updatedAramaMetni()
    {
        if (strlen($this->aramaMetni) < 2) {
            $this->bulunanKullanicilar = [];
            return;
        }

        // Mevcut ekipteki ID'leri al ki tekrar listeleme
        // $this->mevcutUyeListesi bir Eloquent Collection olduğu için pluck çalışır
        $ekipIds = $this->mevcutUyeListesi->pluck('id')->toArray();

        // TÜM kullanıcılar tablosunda ara (Onaylı olanlar)
        $this->bulunanKullanicilar = User::where('name', 'like', '%' . $this->aramaMetni . '%')
            ->where('onaylandi_mi', true) // Sadece onaylı kullanıcılar
            ->whereNotIn('id', $ekipIds)
            ->take(5)
            ->get();
    }

    public function uyeEkle($userId)
    {
        $iaa = Iaa::find($this->iaaId);
        
        // Yetki Kontrolü
        if (Auth::id() != $this->liderId && !Auth::user()->hasRole('Superadmin')) {
            return; 
        }

        // --- GÜNCELLEME: DURUM 'bekliyor' OLARAK EKLENİYOR ---
        // syncWithoutDetaching kullanıyoruz ki zaten varsa bozmasın.
        // Ancak updateExistingPivot ile var olanı güncellemeyi de deneyebiliriz.
        // En temizi: attach veya sync, status ile.
        
        // Önce var mı kontrol et
        $exists = $iaa->projeEkibi()->where('user_id', $userId)->exists();

        if (!$exists) {
            $iaa->projeEkibi()->attach($userId, [
                'rol' => 'Üye',
                'durum' => 'bekliyor' // <--- ÖNEMLİ: Bekliyor olarak ekle
            ]);

            // --- BİLDİRİM GÖNDER ---
            $davetEdilenUser = User::find($userId);
            $lider = Auth::user(); // İşlemi yapan kişi liderdir
            
            if ($davetEdilenUser) {
                \Illuminate\Support\Facades\Notification::send(
                    $davetEdilenUser, 
                    new \App\Notifications\ProjeEkipDaveti($iaa, $lider)
                );
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
        // 1. Lider kendini çıkaramaz
        if ($userId == $this->liderId) {
            return;
        }

        $iaa = Iaa::find($this->iaaId);
        
        // 2. Yetki Kontrolü
        if (Auth::id() != $this->liderId && !Auth::user()->hasRole('Superadmin')) {
            return;
        }

        // 3. Çıkarılacak kullanıcıyı bul ve BİLDİRİM GÖNDER
        $cikarilacakUye = User::find($userId);
        $lider = Auth::user();

        if ($cikarilacakUye) {
            try {
                \Illuminate\Support\Facades\Notification::send(
                    $cikarilacakUye, 
                    new \App\Notifications\ProjeEkibindenCikarildi($iaa, $lider)
                );
            } catch (\Exception $e) {
                // Bildirim hatası işlemi durdurmasın
                \Illuminate\Support\Facades\Log::error('Üye çıkarma bildirimi hatası: ' . $e->getMessage());
            }
        }

        // 4. Silme İşlemi (Pivot tablodan kaldır)
        $iaa->projeEkibi()->detach($userId);

        // 5. Listeyi Yenile ve Mesaj Ver
        $this->loadProjectData();
        session()->flash('success', 'Üye proje ekibinden çıkarıldı ve kendisine bildirim gönderildi.');
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
}