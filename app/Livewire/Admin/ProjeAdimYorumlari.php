<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Iaa;
use App\Models\IaaWorkflowStep;
use App\Models\ProjeYorumu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Notification;
use App\Notifications\YeniProjeYorumu;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\YeniYorumBildirimiMail;

class ProjeAdimYorumlari extends Component
{
    use WithFileUploads;

    public Iaa $iaa;
    public IaaWorkflowStep $step;
    
    public $yeniYorum = '';
    public $yeniDosya;
    public $kullaniciYetkiliMi = false;
    public $step_id;

    // === YENİ: Düzenleme için eklendi ===
    public $editingCommentId = null;
    public $editingCommentBody = '';
    // === YENİ SONU ===

    public $isMusteri = false;

    // === YENİ: Cevap verme için eklendi ===
    public $replyingToCommentId = null;
    public $replyingToUserName = '';
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
        $this->step_id = $step->id;
        $this->checkYetki();
    }

    private function checkYetki()
    {
        // 1. SENARYO: SİSTEME GİRİŞ YAPMIŞ KULLANICILAR
        if (Auth::check()) {
            $user = Auth::user();

            // A) GENEL ROL YETKİSİ OLANLAR
            // Bu rollere sahip herhangi biri, hangi proje olursa olsun yorum yapabilir.
            if ($user->hasRole([
                'Superadmin',
                'Yonetim',
                'Müşteri Şikayeti Kurulu',
                'Müşteri Şikayeti Kurulu Yöneticisi',
                'Müşteri Şikayeti Çözüm Lideri',
                'Bölüm Kalite Yöneticisi',
                'Bölüm Lideri',
                'Müşteri Saha Temsilcisi'
            ])) {
                $this->kullaniciYetkiliMi = true;
                return;
            }

            // Bölgesel Kurul Üyeleri Yorum Yetkisi (Yurt İçi / Yurt Dışı)
            if ($this->iaa->musteriSikayeti) {
                $sikayetKonum = $this->iaa->musteriSikayeti->konum_tipi;
                if ($sikayetKonum === 'Yurt İçi' && $user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                    $this->kullaniciYetkiliMi = true;
                    return;
                }
                if ($sikayetKonum === 'Yurt Dışı' && $user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                    $this->kullaniciYetkiliMi = true;
                    return;
                }
            }

            // B) PROJE EKİP ÜYESİ KONTROLÜ (Sadece ilgili projede yetkili)
            // Kullanıcı yukarıdaki rollerden değilse bile, bu projenin takımında mı?
            if ($this->iaa->atananTakim && $this->iaa->atananTakim->uyeler->contains('id', $user->id)) {
                $this->kullaniciYetkiliMi = true;
                return;
            }

            // C) MÜŞTERİ YETKİLİSİ KONTROLÜ
            // Eğer giriş yapan kullanıcı bir firmaya bağlıysa VE proje o firmanın şikayetine aitse
            if (!$user->is_personnel && $this->iaa->musteriSikayeti) {
                $customer_id = $this->iaa->musteriSikayeti->customer_id;
                if ($user->customer_id == $customer_id || $user->customers()->where('customers.id', $customer_id)->exists()) {
                    $this->isMusteri = true;
                    return;
                }
            }
        }

        // 2. SENARYO: DIŞ MÜŞTERİ (Giriş yapmamış, Token ile gelmiş)
        // Sadece o şikayetin sahibi olan müşteri mi?
        if ($this->iaa->musteriSikayeti) {
            $sessionKey = 'sikayet_logged_in_' . $this->iaa->musteriSikayeti->takip_token;
            if (Session::has($sessionKey)) {
                $this->kullaniciYetkiliMi = true; // Form görünsün diye bunu da true bırakabilirsin
                $this->isMusteri = true; // <--- BURAYI GÜNCELLE (Eskiden $kullaniciYetkiliMi idi, isMusteri daha doğru)
                return;
            }
        }

        // HİÇBİR ŞARTA UYMUYORSA
        $this->kullaniciYetkiliMi = false;
    }

    public function addYorum()
    {
        // Sadece yeni yorumla ilgili kuralları doğrula
        $this->validate([
            'yeniYorum' => $this->rules['yeniYorum'],
            'yeniDosya' => $this->rules['yeniDosya'],
        ]);

        if (!$this->kullaniciYetkiliMi && !$this->isMusteri) {
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

        // Yapan Kişiyi Belirleme
        $yapanKisi = "Sistem"; 
        $userId = null; 
        $sikayetId = null;

        if (Auth::check()) {
            $yapanKisi = Auth::user()->name; 
            $userId = Auth::id();
            
            // Eğer müşteri olarak giriş yapmışsa şikayet ID'sini de bağla
            if ($this->isMusteri && $this->iaa->musteriSikayeti) {
                $sikayetId = $this->iaa->musteriSikayeti->id;
            }
        } 
        // Misafir Müşteri (Session ile)
        elseif ($this->isMusteri && $this->iaa->musteriSikayeti) {
            $yapanKisi = $this->iaa->musteriSikayeti->musteri_adi . " (Müşteri)";
            $sikayetId = $this->iaa->musteriSikayeti->id;
        }

        // === GÜNCELLEME: Daha güvenilir olan step_id mülkünü kullan ===
        $yeniYorumKaydi = ProjeYorumu::create([
            'parent_id' => $this->replyingToCommentId, // Cevap ise parent_id ekle
            'iaa_id' => $this->iaa->id,
            'iaa_workflow_step_id' => $this->step_id, // Direkt mülkü kullan
            'user_id' => $userId,
            'musteri_sikayeti_id' => $sikayetId,
            'yapan_kisi_adi' => $yapanKisi,
            'yorum_tipi' => 'yorum',
            'yorum' => $this->yeniYorum,
            'dosya_yolu' => $dosyaYolu,
            'dosya_adi' => $dosyaAdi,
        ]);

        // === BİLDİRİM KODU (HEM E-POSTA HEM IN-APP) ===
    try {
        // 1. İlişkilerin yüklendiğinden emin ol
        $this->iaa->loadMissing('atananTakim.uyeler', 'musteriSikayeti');

        // 2. Takım üyelerini al
        $takimUyeleri = collect();
        if ($this->iaa->atananTakim) {
            $takimUyeleri = $this->iaa->atananTakim->uyeler;
        }

        // 3. Diğer ilgili rolleri al
        $superAdminler = User::role('Superadmin')->get();
        $kurulUyeleri = User::role('Müşteri Şikayeti Kurulu')->get();
        $cozumLiderleri = User::role('Müşteri Şikayeti Çözüm Lideri')->get();

        // 4. Herkesi birleştir
        $bildirimAlacaklar = $takimUyeleri
                            ->merge($superAdminler)
                            ->merge($kurulUyeleri)
                            ->merge($cozumLiderleri)
                            ->unique('id');

        // 5. Yorumu yapan kişiyi listeden çıkar (Eğer misafir değilse)
        if ($userId) { 
            $bildirimAlacaklar = $bildirimAlacaklar->where('id', '!=', $userId);
        }

        // 6. In-App Bildirim (Zil İkonu) Gönder
        if ($bildirimAlacaklar->isNotEmpty()) {
            Notification::send($bildirimAlacaklar, new YeniProjeYorumu($yeniYorumKaydi));
        }

        // === YENİ E-POSTA GÖNDERİMİ BAŞLANGICI ===

        // 7. EKİBE E-POSTA GÖNDER (Erhan ve Kurul dahil)
        // Not: Bu, $bildirimAlacaklar listesindeki herkese mail atar.
        foreach ($bildirimAlacaklar as $kullanici) {
            Mail::to($kullanici->email)
                ->queue(new YeniYorumBildirimiMail($yeniYorumKaydi, $this->iaa));
        }

        // 8. MÜŞTERİYE E-POSTA GÖNDER
        // Eğer yorumu yapan GİRİŞ YAPMIŞ BİR KULLANICIYSA ($userId doluysa)
        // VE bu proje bir MÜŞTERİ ŞİKAYETİNE bağlıysa ($this->iaa->musteriSikayeti varsa)
        // 2. Müşteriye E-Posta (SADECE YORUMU YAPAN MÜŞTERİ DEĞİLSE GİDER)
        // Eğer $isMusteri false ise, demek ki yorumu personel yapmıştır -> Müşteriye mail at.
            if (!$this->isMusteri && $this->iaa->musteriSikayeti && $this->iaa->musteriSikayeti->musteri_iletisim) {
                Mail::to($this->iaa->musteriSikayeti->musteri_iletisim)
                    ->queue(new YeniYorumBildirimiMail($yeniYorumKaydi, $this->iaa));
            }
        // === SON ===

    } catch (\Exception $e) {
        \Log::error('Yeni yorum bildirimi VEYA E-POSTASI gönderilemedi: ' . $e->getMessage());
        \App\Helpers\MailLogHelper::logFailure(
            $this->iaa,
            '"' . $this->iaa->baslik . '" projesinde yeni yorum bildirimi gönderilemedi',
            $bildirimAlacaklar ?? collect(),
            $e->getMessage(),
            null,
            null,
            $this->iaa->bolum_id
        );
    }
    // === BİLDİRİM KODU SONU ===

        $this->reset('yeniYorum', 'yeniDosya', 'replyingToCommentId', 'replyingToUserName');
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

    /**
     * Bir yorumu siler.
     */
    public function deleteComment($commentId)
    {
        $yorum = ProjeYorumu::findOrFail($commentId);

        // Yetki Kontrolü: Sadece yorumu yazan veya Superadmin silebilir
        if (Auth::id() == $yorum->user_id || (Auth::check() && Auth::user()->hasRole('Superadmin'))) {
            $yorum->delete();
            session()->flash('yorum_success', 'Yorum başarıyla silindi.');
        }
    }

    /**
     * Bir yoruma cevap verme modunu açar.
     */
    public function setReply($commentId)
    {
        $parent = ProjeYorumu::findOrFail($commentId);
        $this->replyingToCommentId = $parent->id;
        $this->replyingToUserName = $parent->user ? $parent->user->name : $parent->yapan_kisi_adi;
        
        // Form alanına odaklanmak için dispatchBrowserEvent kullanılabilir (Opsiyonel)
        $this->dispatch('focus-comment-input');
    }

    /**
     * Cevap verme modunu kapatır.
     */
    public function cancelReply()
    {
        $this->reset('replyingToCommentId', 'replyingToUserName');
    }
    // === YENİ FONKSİYONLAR SONU ===


    public function render()
    {
        // === GÜNCELLEME: Query'de step_id mülkünü kullan (Daha güvenilir) ===
        // Sadece ana yorumları çekiyoruz, cevaplar (children) model üzerinden yüklenecek
        $yorumlar = ProjeYorumu::where('iaa_workflow_step_id', $this->step_id)
            ->where('iaa_id', $this->iaa->id)
            ->whereNull('parent_id') // Ana yorumlar
            ->with(['user.bolum', 'user.roles', 'user.customers', 'children.user.bolum', 'children.user.roles', 'children.user.customers']) 
            ->latest() 
            ->get();
            
        // === YENİ EKLENDİ (Yorum Sayıları - Tüm yorumları say) ===
        $toplamYorumSorgusu = ProjeYorumu::where('iaa_workflow_step_id', $this->step_id)
            ->where('iaa_id', $this->iaa->id);
            
        $yorumSayisi = $toplamYorumSorgusu->count();
        // Müşteri yorumlarını filtrele (user_id'si olmayanlar)
        $musteriYorumSayisi = $toplamYorumSorgusu->whereNull('user_id')->count();
        // === YENİ EKLEME SONU ===

        return view('livewire.admin.proje-adim-yorumlari', [
            'yorumlar' => $yorumlar,
            'yorumSayisi' => $yorumSayisi,            
            'musteriYorumSayisi' => $musteriYorumSayisi 
        ]);
    }
}