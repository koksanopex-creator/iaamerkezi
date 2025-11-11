<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Iaa;
use App\Models\IaaWorkflowStep;
use App\Models\ProjeYorumu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProjeAdimYorumlari extends Component
{
    use WithFileUploads;

    public Iaa $iaa;
    public IaaWorkflowStep $step;
    
    public $yeniYorum = '';
    public $yeniDosya;
    public $kullaniciYetkiliMi = false;

    // === YENİ: Düzenleme için eklendi ===
    public $editingCommentId = null;
    public $editingCommentBody = '';
    // === YENİ SONU ===
    
    protected $rules = [
        'yeniYorum' => 'required_without:yeniDosya|string|min:3|nullable',
        'yeniDosya' => 'required_without:yeniYorum|nullable|file|max:5120',
        
        // === YENİ: Düzenleme kuralı ===
        'editingCommentBody' => 'required|string|min:3',
    ];

    protected $validationAttributes = [
        'yeniYorum' => 'Yorum',
        'yeniDosya' => 'Dosya',
        'editingCommentBody' => 'Düzenlenen yorum',
    ];

    public function mount(Iaa $iaa, IaaWorkflowStep $step)
    {
        $this->iaa = $iaa;
        $this->step = $step;
        $this->checkYetki();
    }

    private function checkYetki()
    {
        // ... (Bu fonksiyon aynı kalıyor, değişiklik yok) ...
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri'])) {
                $this->kullaniciYetkiliMi = true; return;
            }
            if ($this->iaa->atananTakim && $this->iaa->atananTakim->uyeler->contains($user)) {
                $this->kullaniciYetkiliMi = true; return;
            }
        }
        if ($this->iaa->musteriSikayeti) {
            $sessionKey = 'sikayet_logged_in_' . $this->iaa->musteriSikayeti->takip_token;
            if (Session::has($sessionKey)) {
                $this->kullaniciYetkiliMi = true; return;
            }
        }
        $this->kullaniciYetkiliMi = false;
    }

    public function addYorum()
    {
        // Sadece yeni yorumla ilgili kuralları doğrula
        $this->validate([
            'yeniYorum' => $this->rules['yeniYorum'],
            'yeniDosya' => $this->rules['yeniDosya'],
        ]);

        if (!$this->kullaniciYetkiliMi) {
            session()->flash('yorum_error', 'Yorum yapma yetkiniz bulunmamaktadır.');
            return;
        }

        $dosyaYolu = null;
        $dosyaAdi = null;

        if ($this->yeniDosya) {
            $dosyaAdi = $this->yeniDosya->getClientOriginalName();
            $dosyaYolu = $this->yeniDosya->storeAs(
                'proje_yorum_dosyalari/' . $this->iaa->id,
                uniqid() . '_' . $dosyaAdi,
                'public'
            );
        }

        // ... (Yapan kişiyi belirleme kodu aynı) ...
        $yapanKisi = "Sistem"; $userId = null; $sikayetId = null;
        if (Auth::check()) {
            $yapanKisi = Auth::user()->name; $userId = Auth::id();
        } elseif ($this->iaa->musteriSikayeti && Session::has('sikayet_logged_in_' . $this->iaa->musteriSikayeti->takip_token)) {
            $yapanKisi = $this->iaa->musteriSikayeti->musteri_adi . " (Müşteri)";
            $sikayetId = $this->iaa->musteriSikayeti->id;
        }

        ProjeYorumu::create([
            'iaa_id' => $this->iaa->id,
            'iaa_workflow_step_id' => $this->step->id,
            'user_id' => $userId,
            'musteri_sikayeti_id' => $sikayetId,
            'yapan_kisi_adi' => $yapanKisi,
            'yorum_tipi' => 'yorum',
            'yorum' => $this->yeniYorum,
            'dosya_yolu' => $dosyaYolu,
            'dosya_adi' => $dosyaAdi,
        ]);

        $this->reset('yeniYorum', 'yeniDosya');
        session()->flash('yorum_success', 'Yorumunuz başarıyla eklendi.');
    }

    // === YENİ: DÜZENLEME FONKSİYONLARI ===

    /**
     * Bir yorumu düzenleme moduna alır.
     */
    public function editComment($commentId)
    {
        $yorum = ProjeYorumu::findOrFail($commentId);

        // Yetki Kontrolü: Sadece yorumu yazan veya Superadmin düzenleyebilir
        if (Auth::id() == $yorum->user_id || (Auth::check() && Auth::user()->hasRole('Superadmin'))) {
            $this->editingCommentId = $yorum->id;
            $this->editingCommentBody = $yorum->yorum;
        }
    }

    /**
     * Düzenlemeyi iptal eder.
     */
    public function cancelEdit()
    {
        $this->reset('editingCommentId', 'editingCommentBody');
    }

    /**
     * Düzenlenen yorumu kaydeder.
     */
    public function updateComment()
    {
        $this->validate(['editingCommentBody' => $this->rules['editingCommentBody']]);

        $yorum = ProjeYorumu::findOrFail($this->editingCommentId);

        // Yetki Kontrolü
        if (Auth::id() == $yorum->user_id || (Auth::check() && Auth::user()->hasRole('Superadmin'))) {
            $yorum->update([
                'yorum' => $this->editingCommentBody
            ]);
            $this->cancelEdit(); // Düzenleme modunu kapat
        }
    }
    // === YENİ FONKSİYONLAR SONU ===


    public function render()
    {
        $yorumlar = ProjeYorumu::where('iaa_workflow_step_id', $this->step->id)
            ->where('iaa_id', $this->iaa->id) // Bu filtre zaten doğruydu
            ->with('user') 
            ->latest() 
            ->get();
            
        // === YENİ EKLENDİ (Yorum Sayıları) ===
        $yorumSayisi = $yorumlar->count();
        // Müşteri yorumlarını filtrele (user_id'si olmayanlar)
        $musteriYorumSayisi = $yorumlar->whereNull('user_id')->count();
        // === YENİ EKLEME SONU ===

        return view('livewire.admin.proje-adim-yorumlari', [
            'yorumlar' => $yorumlar,
            'yorumSayisi' => $yorumSayisi,             // <-- Sayıyı View'a gönder
            'musteriYorumSayisi' => $musteriYorumSayisi // <-- Müşteri sayısını View'a gönder
        ]);
    }
}