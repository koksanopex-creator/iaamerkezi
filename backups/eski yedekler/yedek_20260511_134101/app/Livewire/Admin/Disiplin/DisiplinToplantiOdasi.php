<?php

namespace App\Livewire\Admin\Disiplin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\DisiplinKuruluToplanti;
use App\Models\ToplantiAksiyon;
use App\Models\ToplantiOylama;
use App\Models\ToplantiOy;
use App\Models\ToplantiGizliNot;
use App\Models\ToplantiPano;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ToplantiAksiyonNotification;
use App\Notifications\ToplantiDurumNotification;
use Carbon\Carbon;

class DisiplinToplantiOdasi extends Component
{
    use WithFileUploads;

    public $toplantiId;
    public $toplanti;
    public $panoIcerik;
    public $aksiyonIcerik;
    public $aksiyonUser;
    public $oylamaKonu;
    public $gizliNot;
    public $showErteleModal = false;
    public $showIptalModal = false;
    public $ertelemeSebepi;
    public $ertelemeTarihi;
    public $iptalSebepi;
    public $activeWidgets = [];
    public $toplantiKarari;
    public $kararDosya;
    
    // Phase 3 Yenilikleri
    public $yeniMadde;
    public $yeniMaddeSorumlu;
    public $katilimciDurumlari = [];
    public $katilmamaNedenleri = [];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount($toplantiId)
    {
        $this->toplantiId = $toplantiId;
        $this->loadData();
    }

    public function loadData()
    {
        $this->toplanti = DisiplinKuruluToplanti::with([
            'katilimcilar.user', 
            'oylamalar.oylar', 
            'pano',
            'kararMaddeleri.sorumlu'
        ])->findOrFail($this->toplantiId);
        
        $this->panoIcerik = $this->toplanti->pano->first()->icerik ?? '';
        $this->activeWidgets = $this->toplanti->active_widgets ?? []; // Varsayılan olarak hepsi kapalı
        $this->toplantiKarari = $this->toplanti->toplanti_karari;

        // Yoklama verilerini eşitle
        foreach($this->toplanti->katilimcilar as $kat) {
            $this->katilimciDurumlari[$kat->id] = $kat->katilim_durumu;
            $this->katilmamaNedenleri[$kat->id] = $kat->katilmama_nedeni;
        }
        
        $not = ToplantiGizliNot::where('toplanti_id', $this->toplantiId)->where('user_id', Auth::id())->first();
        $this->gizliNot = $not ? $not->not_icerigi : '';

        // Otomatik Yoklama (Eğer toplantı başlamış veya devam ediyorsa)
        if (in_array($this->toplanti->durum, ['devam_ediyor', 'tamamlandı'])) {
            $this->attendance();
        }
    }

    // Karar ve Dosya Kaydet
    public function saveResolution()
    {
        if (!$this->isAuthorized()) return;

        $updateData = ['toplanti_karari' => $this->toplantiKarari];

        if ($this->kararDosya) {
            $this->validate([
                'kararDosya' => 'max:10240', // 10MB
            ]);

            // rules1.md formatı: storage/{ana_klasor}/{kullanici}/{kayit_id}/{tarih_saat}_{random}.{ext}
            $anaKlasor = 'disiplin_toplanti_kararlari';
            $kullanici = Str::slug(Auth::user()->name);
            $kayitId   = $this->toplanti->id;
            $tarihSaat = now()->format('d.m.Y_H.i');
            $random    = Str::random(2);
            $ext       = $this->kararDosya->extension();

            $filename = "{$tarihSaat}_{$random}.{$ext}";
            $path = "{$anaKlasor}/{$kullanici}/{$kayitId}";

            $finalPath = $this->kararDosya->storeAs($path, $filename, 'public');
            $updateData['karar_dosya_yolu'] = $finalPath;
            $this->kararDosya = null;
        }

        $this->toplanti->update($updateData);
        $this->loadData();
        session()->flash('success', 'Kararlar ve ekli dosya başarıyla kaydedildi.');
    }

    // Sorumluya Karar Maddesi Ata
    public function addDecisionItem()
    {
        if (!$this->isAuthorized()) return;

        $this->validate([
            'yeniMadde' => 'required',
            'yeniMaddeSorumlu' => 'nullable|exists:users,id'
        ]);

        $madde = \App\Models\ToplantiKararMadde::create([
            'toplanti_id' => $this->toplantiId,
            'madde_metni' => $this->yeniMadde,
            'sorumlu_user_id' => $this->yeniMaddeSorumlu,
            'durum' => 'beklemede'
        ]);

        if ($this->yeniMaddeSorumlu) {
            $user = User::find($this->yeniMaddeSorumlu);
            $user->notify(new \App\Notifications\DisiplinToplantiBildirimi($this->toplanti, 'aksiyon', 'Toplantıda size yeni bir görev atandı: ' . $this->yeniMadde));
        }

        $this->yeniMadde = '';
        $this->yeniMaddeSorumlu = null;
        $this->loadData();
        session()->flash('success', 'Karar maddesi eklendi.');
    }

    public function deleteDecisionItem($id)
    {
        if (!$this->isAuthorized()) return;
        \App\Models\ToplantiKararMadde::destroy($id);
        $this->loadData();
    }

    public function updateAttendance($katilimciId)
    {
        if (!$this->isAuthorized()) return;

        $kat = $this->toplanti->katilimcilar->find($katilimciId);
        if ($kat) {
            $kat->update([
                'katilim_durumu' => $this->katilimciDurumlari[$katilimciId] ?? 'beklemede',
                'katilmama_nedeni' => $this->katilmamaNedenleri[$katilimciId] ?? null
            ]);
            session()->flash('success', 'Yoklama durumu güncellendi.');
        }
    }

    public function attendance()
    {
        $katilimci = $this->toplanti->katilimcilar()->where('user_id', Auth::id())->first();
        if ($katilimci && $katilimci->katilim_durumu !== 'katildi') {
            $katilimci->update(['katilim_durumu' => 'katildi']);
        }
    }

    public function toggleWidget($widget)
    {
        if (!$this->isAuthorized()) return;

        $widgets = $this->activeWidgets;
        if (in_array($widget, $widgets)) {
            $widgets = array_diff($widgets, [$widget]);
        } else {
            $widgets[] = $widget;
        }

        $this->toplanti->update(['active_widgets' => array_values($widgets)]);
        $this->activeWidgets = array_values($widgets);
        $this->loadData();
    }

    public function saveKarar()
    {
        if (!$this->isAuthorized()) return;
        $this->toplanti->update(['toplanti_karari' => $this->toplantiKarari]);
        session()->flash('success', 'Toplantı kararları kaydedildi.');
    }

    // Toplantı Başlat
    public function startMeeting()
    {
        if (!$this->isAuthorized()) return;

        $this->toplanti->update([
            'baslatilma_at' => now(),
            'durum' => 'devam_ediyor'
        ]);
        
        // Bildirim Gönder
        $this->sendNotification('baslatıldı', 'Toplantı şu an başladı. Katılım sağlayabilirsiniz.');

        $this->loadData();
    }

    // Toplantı Bitir
    public function endMeeting()
    {
        if (!$this->isAuthorized()) return;

        $this->toplanti->update([
            'bitirilme_at' => now(),
            'durum' => 'tamamlandı'
        ]);

        // Disiplin Dosyası Durumunu Güncelle
        if ($this->toplanti->disciplinary_case_id) {
            $case = $this->toplanti->disiplinDosyasi;
            if ($case) {
                // Eğer dosya zaten daha ileri bir aşamada değilse
                $case->update([
                    'durum' => 'Karar Verildi',
                    'karar_tarihi' => now()
                ]);
            }
        }

        // Tüm katılımcılara bildirim gönder
        $this->sendNotification('tamamlandı', 'Toplantı başarıyla sonlandırıldı. Alınan kararları inceleyebilirsiniz.');

        $this->loadData();
        session()->flash('success', 'Toplantı başarıyla sonlandırıldı ve katılımcılara bildirildi.');
    }

    // Aksiyon Ata
    public function addAction()
    {
        if (!$this->isAuthorized()) {
            session()->flash('error', 'Sadece moderatörler aksiyon atayabilir.');
            return;
        }

        $this->validate([
            'aksiyonIcerik' => 'required',
            'aksiyonUser'   => 'required|exists:users,id'
        ]);

        $aksiyon = ToplantiAksiyon::create([
            'toplanti_id' => $this->toplantiId,
            'user_id'     => $this->aksiyonUser,
            'icerik'      => $this->aksiyonIcerik,
            'durum'       => 'beklemede'
        ]);

        // Bildirim Gönder
        $user = User::find($this->aksiyonUser);
        $user->notify(new ToplantiAksiyonNotification($aksiyon, Auth::user()->name));

        $this->aksiyonIcerik = '';
        $this->aksiyonUser = null;
        $this->loadData();
        session()->flash('success', 'Aksiyon başarıyla atandı.');
    }

    // Moderatörlük Yetkisi Değiştir (Delegation)
    public function toggleModerator($katilimciId)
    {
        if (!$this->isAuthorized()) return;

        $katilimci = $this->toplanti->katilimcilar()->find($katilimciId);
        if ($katilimci) {
            $katilimci->update(['is_moderator' => !$katilimci->is_moderator]);
            $this->loadData();
            session()->flash('success', $katilimci->user->name . ' yetki durumu güncellendi.');
        }
    }

    // Oylama Başlat
    public function startVote()
    {
        if (!$this->isAuthorized()) {
            session()->flash('error', 'Sadece moderatörler oylama başlatabilir.');
            return;
        }

        $this->validate(['oylamaKonu' => 'required']);

        ToplantiOylama::where('toplanti_id', $this->toplantiId)->update(['aktif' => false]);

        ToplantiOylama::create([
            'toplanti_id' => $this->toplantiId,
            'baslatan_id' => Auth::id(),
            'konu'        => $this->oylamaKonu,
            'aktif'       => true
        ]);

        $this->oylamaKonu = '';
        $this->loadData();
    }

    // Oy Kullan
    public function castVote($oylamaId, $oy)
    {
        ToplantiOy::updateOrCreate(
            ['oylama_id' => $oylamaId, 'user_id' => Auth::id()],
            ['oy' => $oy]
        );
        $this->loadData();
    }

    // Pano Kaydet
    public function updatedPanoIcerik($value)
    {
        if (!$this->isAuthorized()) return;

        ToplantiPano::updateOrCreate(
            ['toplanti_id' => $this->toplantiId],
            ['icerik' => $value]
        );
    }

    // Gizli Not Kaydet
    public function updatedGizliNot($value)
    {
        ToplantiGizliNot::updateOrCreate(
            ['toplanti_id' => $this->toplantiId, 'user_id' => Auth::id()],
            ['not_icerigi' => $value]
        );
    }

    // Erteleme
    public function reschedule()
    {
        if (!$this->isAuthorized()) return;

        $this->validate([
            'ertelemeSebepi' => 'required',
            'ertelemeTarihi' => 'required|after:now'
        ]);

        $this->toplanti->update([
            'durum' => 'planlandı',
            'baslangic_tarihi' => $this->ertelemeTarihi,
            'erteleme_sebebi' => $this->ertelemeSebepi
        ]);

        // Bildirim
        $this->sendNotification('ertelendi', $this->ertelemeSebepi);

        $this->showErteleModal = false;
        $this->loadData();
    }

    // İptal
    public function cancel()
    {
        if (!$this->isAuthorized()) return;

        $this->validate(['iptalSebepi' => 'required']);

        $this->toplanti->update([
            'durum' => 'iptal',
            'iptal_sebebi' => $this->iptalSebepi
        ]);

        // Bildirim
        $this->sendNotification('iptal', $this->iptalSebepi);

        $this->showIptalModal = false;
        $this->loadData();
    }

    public function isAuthorized()
    {
        return $this->toplanti->canUserManage(Auth::user());
    }



    public function getRemainingTimeProperty()
    {
        if (!$this->toplanti->baslatilma_at || $this->toplanti->bitirilme_at) return null;

        $bitisVakti = $this->toplanti->baslatilma_at->addMinutes($this->toplanti->planlanan_sure_dk);
        $diff = now()->diffInSeconds($bitisVakti, false);
        
        return $diff > 0 ? $diff : 0;
    }

    public function render()
    {
        return view('livewire.admin.disiplin.toplanti-odasi', [
            'canManage'  => $this->isAuthorized(),
            'isFinished' => $this->toplanti->durum === 'tamamlandı' || $this->toplanti->durum === 'iptal'
        ]);
    }

    public function sendNotification($tur, $mesaj)
    {
        // 1. Toplantı Katılımcıları (Zaten User olanlar)
        $katilimciUserIds = $this->toplanti->katilimcilar->pluck('user_id')->filter()->toArray();

        // 2. Tüm Disiplin Kurulu Üyeleri
        $kurulUyeUserIds = \App\Models\DisiplinKuruluUyelik::where('aktif', true)->pluck('user_id')->toArray();

        // Benzersiz User ID listesi
        $userIds = array_unique(array_merge($katilimciUserIds, $kurulUyeUserIds));
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            /** @var \App\Models\User $user */
            $user->notify(new \App\Notifications\DisiplinToplantiBildirimi($this->toplanti, $tur, $mesaj));
            
            // Mail Bildirimi
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\DisiplinToplantiMail($this->toplanti, $tur, $mesaj));
            } catch (\Exception $e) {
                \Log::error('Toplantı mail gönderim hatası: ' . $e->getMessage());
            }
        }
    }
}
