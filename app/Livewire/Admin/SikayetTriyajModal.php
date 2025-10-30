<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\MusteriSikayeti;
use App\Models\Takim;
use App\Models\Setting;
use App\Models\MusteriSikayetiLog;
use App\Models\User; // User modelini ekledik
use App\Models\Iaa; // <-- YENİ EKLENDİ
use App\Models\IaaWorkflow; // <-- YENİ EKLENDİ (veya DB::table kullanacağız)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Carbon'u ekledi

class SikayetTriyajModal extends Component
{
    public $sikayetId;
    public $musteriAdi;
    public $sikayetKonusu;
    
    public $atanan_cozum_takimi_id;
    // public $musteri_durum; // KALDIRILDI - Artık otomatik yönetilecek
    public $musteri_cozum_son_tarihi;
    public $etki_puani;
    public $karmasiklik_puani;
    public $musteri_puan; // Hesaplanan çözüm puanı

    public $ek_sure_talep_aciklamasi;
    public $ek_sure_talep_durumu;
    public $yeni_ek_sure_aciklamasi = '';

    public $cozumTakimlari = [];
    // public $sikayetDurumlari = ['Yeni', 'İşlemde', 'Çözümlendi', 'Kapatıldı', 'Yeniden Açıldı']; // KALDIRILDI
    public $etkiPuanlari = [
        1 => 'Çok Düşük',
        2 => 'Düşük',
        3 => 'Orta',
        4 => 'Yüksek',
        5 => 'Çok Yüksek'
    ];
    
    public $karmasiklikPuanlari = [
        1 => 'Düşük (Basit, Tek Adımlı)',
        2 => 'Az Orta (Küçük Koordinasyon Gerekli)',
        3 => 'Orta (Birden Fazla Adım, Takip Gerekli)',
        4 => 'Yüksek (Bölümler Arası Koordinasyon)',
        5 => 'Çok Yüksek (Kök Neden Analizi ve Geniş Katılım)'
    ];
    

    public $cozumPuaniCarpan = 10;
    
    public $showModal = false;

    // Livewire event'lerini dinle
    protected $listeners = [
        'openTriyajModal' => 'loadSikayet'
    ];

    public function mount()
    {
        // Çözüm takımlarını bir kez yükle ('sikayet' türündekileri)
        $this->cozumTakimlari = Takim::where('tur', 'sikayet')->pluck('ad', 'id');
        // Çözüm puanı çarpanını ayarlardan al
        $this->cozumPuaniCarpan = (int)(Setting::where('key', 'musteri_sikayeti_cozum_carpan')->value('value') ?? 10);
    }

    // === DÜZELTME 2 (Modal Hatası): loadSikayet($sikayetId) -> loadSikayet($id) ===
    public function loadSikayet($id)
    {
        $sikayet = MusteriSikayeti::findOrFail($id);
        $this->sikayetId = $sikayet->id;
        $this->musteriAdi = $sikayet->musteri_adi;
        $this->sikayetKonusu = $sikayet->musteri_sikayet_konusu;
        
        $this->atanan_cozum_takimi_id = $sikayet->atanan_cozum_takimi_id;
        // $this->musteri_durum = $sikayet->musteri_durum; // KALDIRILDI
        $this->musteri_cozum_son_tarihi = $sikayet->musteri_cozum_son_tarihi ? Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('Y-m-d\TH:i') : null;
        $this->etki_puani = $sikayet->etki_puani;
        $this->karmasiklik_puani = $sikayet->karmasiklik_puani;
        $this->musteri_puan = $sikayet->musteri_puan;

        $this->ek_sure_talep_durumu = $sikayet->musteri_ek_sure_talep_durumu;
        $this->ek_sure_talep_aciklamasi = $sikayet->ek_sure_talep_aciklamasi;
        $this->yeni_ek_sure_aciklamasi = '';

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'atanan_cozum_takimi_id' => 'nullable|exists:takimlar,id',
            'musteri_cozum_son_tarihi' => 'nullable|date',
            'etki_puani' => 'nullable|integer',
            'karmasiklik_puani' => 'nullable|integer',
        ]);

        // === GÜNCELLEME: Tüm işlemi DB Transaction içine alıyoruz ===
        DB::transaction(function () {
            
            $sikayet = MusteriSikayeti::findOrFail($this->sikayetId);
            $user = Auth::user();
            $logAciklamalari = [];

            // === OTOMATİK DURUM YÖNETİMİ (Mevcut kodun) ===
            $orijinalDurum = $sikayet->musteri_durum; 
            $yeniDurum = $orijinalDurum; 

            // 1. Takım Atama Kontrolü (Mevcut kodun)
            if ($sikayet->atanan_cozum_takimi_id != $this->atanan_cozum_takimi_id && !empty($this->atanan_cozum_takimi_id)) {
                $takimAdi = Takim::find($this->atanan_cozum_takimi_id)->ad ?? 'Bilinmeyen Takım';
                $logAciklamalari[] = "Çözüm takımı '{$takimAdi}' olarak atandı.";
                
                if ($yeniDurum == 'Yeni') {
                    $yeniDurum = 'İşlemde';
                    $logAciklamalari[] = "Durum otomatik olarak 'İşlemde' yapıldı.";
                }

                // ================== YENİ KÖPRÜ KODU BAŞLANGICI ==================
                // Eğer bu şikayet için henüz bir IAA projesi oluşturulmamışsa (iaa_id = null), şimdi oluştur.
                if (is_null($sikayet->iaa_id)) {
                    
                    // a. Yeni Iaa projesini (iaas tablosuna) oluştur
                    $yeniProje = Iaa::create([
                        'baslik' => $sikayet->musteri_sikayet_konusu, // Şikayet konusunu proje başlığı yap
                        'mevcut_durum' => $sikayet->musteri_sikayet_detayi, // Şikayet detayını mevcut durum yap
                        'oneri' => 'Müşteri şikayetinden (ID: ' . $sikayet->id . ') dönüştürüldü.',
                        'durum' => 'Atandı', // Otomatik olarak 'Atandı' yap
                        'puan' => $this->musteri_puan ?? 100, // Triyajda belirlenen puanı al (varsa, yoksa 100)
                        'gonderen_user_id' => null, // Sistem oluşturdu (veya $user->id)
                        'guest_name' => $sikayet->musteri_adi,
                        'guest_email' => $sikayet->musteri_iletisim,
                        'onaylayan_user_id' => $user->id, // İşlemi yapan admin
                        'onaylanma_tarihi' => now(), // Havuza alınma tarihi
                        'atanan_takim_id' => $this->atanan_cozum_takimi_id // Seçilen takıma ata
                    ]);

                    // b. Proje Atamasını (iaa_talepleri tablosuna) oluştur
                    // DB dump'ına göre 'Müşteri Şikayeti Çözüm Şablonu' ID: 2
                    $workflowId = 2; 

                    DB::table('iaa_talepleri')->insert([
                        'iaa_id' => $yeniProje->id,
                        'takim_id' => $this->atanan_cozum_takimi_id,
                        'talep_eden_user_id' => $user->id, // Admin talep etmiş/atamış gibi
                        'durum' => 'onaylandi', // Otomatik onay
                        'iaa_workflow_id' => $workflowId, 
                        'start_date' => now(),
                        'due_date' => $this->musteri_cozum_son_tarihi ?? now()->addDays(14), // Triyajdaki tarihi veya 14 gün sonrasını al
                        'status' => 'Devam Ediyor', // Proje durumu
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // c. Şikayet kaydını, yeni Proje ID'si ile güncellemeye hazırla
                    $sikayet->iaa_id = $yeniProje->id;
                    
                    $logAciklamalari[] = "Şikayet, ID:{$yeniProje->id} ile IAA Projesine dönüştürüldü.";
                }
                // ================== YENİ KÖPRÜ KODU SONU ==================
            }

            // 2. Tarih Değişikliği Kontrolü (Mevcut kodun)
            if ($sikayet->musteri_cozum_son_tarihi != $this->musteri_cozum_son_tarihi) {
                $logAciklamalari[] = "Çözüm son tarihi '{$this->musteri_cozum_son_tarihi}' olarak ayarlandı.";
                if($this->ek_sure_talep_durumu == 'Talep Edildi') {
                    $this->ek_sure_talep_durumu = 'Onaylandı';
                    $logAciklamalari[] = "Ek süre talebi onaylandı.";
                }
            }

            // 3. Puanlama ve Kapatma Kontrolü (Mevcut kodun)
            $hesaplananPuan = 0;
            if ($this->etki_puani && $this->karmasiklik_puani) {
                $oncelikCarpan = $this->getOncelikCarpan($sikayet->musteri_oncelik);
                $hesaplananPuan = ($this->etki_puani + $this->karmasiklik_puani) * $oncelikCarpan * $this->cozumPuaniCarpan;
            
                if ($this->musteri_puan != $hesaplananPuan) {
                    $this->musteri_puan = $hesaplananPuan;
                    $logAciklamalari[] = "Çözüm puanı {$hesaplananPuan} olarak hesaplandı.";
                }

                if ($this->musteri_puan > 0 && in_array($orijinalDurum, ['İşlemde', 'Çözümlendi'])) {
                    $yeniDurum = 'Kapatıldı';
                    $logAciklamalari[] = "Puanlama yapıldığı için durum otomatik olarak 'Kapatıldı' yapıldı.";
                }
            }
            
            // Puan Dağıtımı (Mevcut kodun)
            if ($yeniDurum == 'Kapatıldı' && $this->musteri_puan > 0 && $sikayet->musteri_puan != $this->musteri_puan) {
                $this->dagitPuan($sikayet->atanan_cozum_takimi_id, $this->musteri_puan);
                $logAciklamalari[] = "Puan takıma dağıtıldı.";
            }
            
            // === VERİ GÜNCELLEME DİZİSİ (GÜNCELLENDİ) ===
            $updateData = [
                'atanan_cozum_takimi_id' => $this->atanan_cozum_takimi_id,
                'musteri_durum' => $yeniDurum, 
                'musteri_cozum_son_tarihi' => $this->musteri_cozum_son_tarihi,
                'etki_puani' => $this->etki_puani,
                'karmasiklik_puani' => $this->karmasiklik_puani,
                'musteri_puan' => $this->musteri_puan,
                'ek_sure_talep_durumu' => $this->ek_sure_talep_durumu,
                'iaa_id' => $sikayet->iaa_id // <-- YENİ PROJE ID'SİNİ KAYDET
            ];

            // === Düzenleme Kilidi Mantığı (Mevcut kodun) ===
            if (!empty($this->atanan_cozum_takimi_id) && is_null($sikayet->edit_locked_at)) {
                $updateData['edit_locked_at'] = now(); 
                $logAciklamalari[] = "Şikayet işleme alındı ve müşteri düzenlemesine kilitlendi.";
            }
            // === KİLİT MANTIĞI SONU ===

            $sikayet->update($updateData);
            
            
            // Loglama (Mevcut kodun - Eylem adı güncellendi)
            if (!empty($logAciklamalari)) {
                $sikayet->loglar()->create([
                    'user_id' => $user->id,
                    'eylem' => 'Şikayet Güncellendi (Triyaj)', // 'Şikayet Güncellendi' yerine
                    'aciklama' => $user->name . " tarafından: " . implode(' ', $logAciklamalari),
                ]);
            }
        
        }); // <-- DB::transaction kapanışı

        // Modal Kapatma (Mevcut kodun)
        $this->showModal = false;
        $this->dispatch('sikayetGuncellendi');
        // === YENİ OLAYI BURADA TETİKLE ===
        // (Bir durum değişikliği oldu, raporlar kendini yenilesin)
        try {
            event(new \App\Events\SikayetDurumuDegisti());
        } catch (\Exception $e) {
            Log::error('Broadcast olayı gönderilemedi: ' . $e->getMessage());
        }
        // === TETİKLEME SONU ===
        session()->flash('success', 'Şikayet başarıyla güncellendi.');
    }
    
    // Atamayı Kaldır
    public function removeAtama()
    {
        $sikayet = MusteriSikayeti::findOrFail($this->sikayetId);
        $user = Auth::user();

        $takimAdi = $sikayet->cozumTakimi->ad ?? 'Bilinmeyen Takım';

        // === GÜNCELLEME BURADA ===
        $sikayet->update([
            'atanan_cozum_takimi_id' => null,
            'musteri_durum' => 'Yeni', // Durumu 'Yeni'ye geri çek
            'musteri_cozum_son_tarihi' => null,
            'edit_locked_at' => null, // Düzenleme kilidini kaldır
            // Puanlama alanlarını da sıfırlayabiliriz (isteğe bağlı)
            // 'etki_puani' => null,
            // 'karmasiklik_puani' => null,
            // 'musteri_puan' => null,
        ]);
        // === GÜNCELLEME SONU ===

        $sikayet->loglar()->create([
            'user_id' => $user->id,
            'eylem' => 'Atama Kaldırıldı',
            'aciklama' => $user->name . " tarafından '{$takimAdi}' takımının ataması kaldırıldı. Durum 'Yeni'ye döndürüldü ve düzenleme kilidi açıldı.",
        ]);

        $this->showModal = false;
        $this->dispatch('sikayetGuncellendi');
        session()->flash('success', 'Takım ataması kaldırıldı.');
    }

    private function getOncelikCarpan($oncelik)
    {
        switch ($oncelik) {
            case 'Acil': return 4;
            case 'Yüksek': return 3;
            case 'Normal': return 2;
            case 'Düşük': return 1;
            default: return 1;
        }
    }

    private function dagitPuan($takimId, $puan)
    {
        if (!$takimId) return;

        $takim = Takim::find($takimId);
        if ($takim) {
            // Puanı sadece bir kez eklemek için (eğer daha önce eklenmemişse)
            // Bu mantık, 'musteri_puan'ın update içinde kontrolüyle sağlandı.
            $takim->increment('toplam_puan', $puan);
            foreach ($takim->uyeler as $uye) {
                $uye->increment('toplam_puan', $puan);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.sikayet-triyaj-modal');
    }
}
