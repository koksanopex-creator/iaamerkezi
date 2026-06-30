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
use Illuminate\Support\Facades\Log; // <-- YENİ EKLENDİ (4. ADIM) (Loglama için)
use Illuminate\Support\Facades\Mail; // <-- YENİ EKLENDİ (4. ADIM) (Mail göndermek için)
use App\Mail\SikayetAtamaBildirimi; // <-- YENİ EKLENDİ (4. ADIM) (Atama mail sınıfı)
use App\Mail\SikayetAtamaBilgilendirmesi;

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
        $this->cozumPuaniCarpan = (int) (Setting::where('key', 'musteri_sikayeti_cozum_carpan')->value('value') ?? 10);
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

            // === YENİ DEĞİŞKEN (Mail göndermek için takım objesini tut) ===
            $yeniAtananTakim = null;

            // 1. Takım Atama Kontrolü (Mevcut kodun)
            if ($sikayet->atanan_cozum_takimi_id != $this->atanan_cozum_takimi_id && !empty($this->atanan_cozum_takimi_id)) {

                // Takımı bul ve değişkene ata (Mail göndermek için)
                $yeniAtananTakim = Takim::find($this->atanan_cozum_takimi_id);
                $takimAdi = $yeniAtananTakim->ad ?? 'Bilinmeyen Takım';

                $logAciklamalari[] = "Çözüm takımı '{$takimAdi}' olarak atandı.";

                // !!!!!!!!!! ÖNEMLİ: E-POSTA GÖNDERİMİ BURADAN KALDIRILDI !!!!!!!!!!

                // ==========================================================
                // === YENİ (KURUL OTOMASYONU) BAŞLANGIÇ ===================
                // ==========================================================
                if ($user->hasRole('Müşteri Şikayeti Kurulu') && !$user->hasRole('Superadmin')) {
                    // ... (Mevcut Kurul Otomasyonu kodunuz - Değişiklik yok)
                    if (empty($this->musteri_cozum_son_tarihi)) {
                        $this->musteri_cozum_son_tarihi = now()->addHours(72);
                        $logAciklamalari[] = "Çözüm son tarihi otomatik olarak 72 saat sonrasına ayarlandı.";
                    }
                    if (empty($this->etki_puani) && empty($this->karmasiklik_puani)) {
                        $defaultPuan = (int) (Setting::where('key', 'kurul_default_puan')->value('value') ?? 0);
                        if ($defaultPuan > 0) {
                            $this->musteri_puan = $defaultPuan;
                            $logAciklamalari[] = "Kurul atamasıyla otomatik olarak {$defaultPuan} default puan ayarlandı.";
                        }
                    }
                }
                // ==========================================================
                // === YENİ (KURUL OTOMASYONU) SON =========================
                // ==========================================================

                if ($yeniDurum == 'Yeni') {
                    $yeniDurum = 'İşlemde';
                    $logAciklamalari[] = "Durum otomatik olarak 'İşlemde' yapıldı.";
                }

                // ================== YENİ KÖPRÜ KODU BAŞLANGICI ==================
                // (Mevcut kodunuz - Dokunulmadı)
                if (is_null($sikayet->iaa_id)) {

                    // a. Yeni Iaa projesini (iaas tablosuna) oluştur
                    $yeniProje = Iaa::create([
                        'baslik' => $sikayet->musteri_sikayet_konusu,
                        'mevcut_durum' => $sikayet->musteri_sikayet_detayi,
                        'oneri' => 'Müşteri şikayetinden (ID: ' . $sikayet->id . ') dönüştürüldü.',
                        'durum' => 'Atandı',
                        'puan' => $this->musteri_puan ?? 100, // Triyajda belirlenen puan (default veya hesaplanan)
                        'gonderen_user_id' => null,
                        'guest_name' => $sikayet->musteri_adi,
                        'guest_email' => $sikayet->musteri_iletisim,
                        'onaylayan_user_id' => $user->id,
                        'onaylanma_tarihi' => now(),
                        'atanan_takim_id' => $this->atanan_cozum_takimi_id,
                        'atamadaki_lider_id' => $yeniAtananTakim->lider_user_id ?? null
                    ]);

                    // b. Proje Atamasını (iaa_talepleri tablosuna) oluştur
                    // YENİ ŞABLON (WORKFLOW) MANTIĞI:
                    // 1. Önce şikayetin bağlı olduğu bölümün özel bir şablonu var mı bak:
                    $workflowId = null;
                    $bolum = $sikayet->sikayetKategori->bolum ?? null;

                    if ($bolum && $bolum->sikayet_workflow_id) {
                        $workflowId = $bolum->sikayet_workflow_id;
                    }

                    // 2. Bölüme özel şablon yoksa, sistem varsayılanını bul:
                    if (!$workflowId) {
                        $defaultWorkflow = IaaWorkflow::where('is_default', true)->first();
                        $workflowId = $defaultWorkflow ? $defaultWorkflow->id : 2; // Hiçbiri yoksa fallback ID: 2
                    }

                    // Snapshot Al
                    $workflow = IaaWorkflow::with('steps')->find($workflowId);
                    $stepsSnapshot = $workflow ? $workflow->steps->toArray() : null;

                    DB::table('iaa_talepleri')->insert([
                        'iaa_id' => $yeniProje->id,
                        'takim_id' => $this->atanan_cozum_takimi_id,
                        'talep_eden_user_id' => $user->id,
                        'durum' => 'onaylandi',
                        'iaa_workflow_id' => $workflowId,
                        'workflow_snapshot' => $stepsSnapshot ? json_encode($stepsSnapshot) : null,
                        'start_date' => now(),
                        'due_date' => $this->musteri_cozum_son_tarihi ?? now()->addDays(14), // Triyajdaki tarihi (72 saat veya manuel)
                        'status' => 'Devam Ediyor',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // c. Şikayet kaydını, yeni Proje ID'si ile güncellemeye hazırla
                    $sikayet->iaa_id = $yeniProje->id; // Obje üzerinde ayarla

                    // --- EKSİK OLAN PARÇA: LİDERİ SQUAD'A EKLE ---
                    // Atanan takımı bul (Zaten $this->atanan_cozum_takimi_id elimizde var)
                    $atananTakim = Takim::find($this->atanan_cozum_takimi_id);

                    if ($atananTakim && $atananTakim->lider_user_id) {
                        // Lideri, bu projenin özel ekibine (iaa_user) "Lider" rolüyle ekle
                        $yeniProje->projeEkibi()->syncWithoutDetaching([
                            $atananTakim->lider_user_id => ['rol' => 'Lider']
                        ]);
                    }
                    // ----------------------------------------------

                    $logAciklamalari[] = "Şikayet, ID:{$yeniProje->id} ile IAA Projesine dönüştürüldü.";
                }
                // ================== YENİ KÖPRÜ KODU SONU ==================

                // === YENİ (MEVCUT PROJEYİ GÜNCELLE) ===
                // Eğer zaten bir proje varsa (iaa_id doluysa), o projenin de ekibini güncelle
                if (!is_null($sikayet->iaa_id)) {
                    $mevcutProje = Iaa::find($sikayet->iaa_id);
                    // Proje varsa ve (henüz güncellenmemişse veya farklıysa)
                    if ($mevcutProje) {

                        // 1. Eski Takım Liderini Bul ve Çıkar (Temizlik)
                        // Şikayet üzerindeki takım henüz güncellenmediği için eski takımdır.
                        // Ancak garanti olsun diye $sikayet'ten alalım.
                        $eskiTakimId = $sikayet->atanan_cozum_takimi_id;
                        if ($eskiTakimId) {
                            $eskiTakim = Takim::find($eskiTakimId);
                            if ($eskiTakim && $eskiTakim->lider_user_id) {
                                // Eski lideri pivot tablodan çıkar
                                // Ancak dikkat: Belki adam aynı zamanda Squad üyesidir?
                                // Şimdilik sadece "Lider" rolüyle eklenenleri veya direkt detach yapalım.
                                // Detach yapmak en temizidir, eğer Squad'da kalması gerekiyorsa ayrıca eklenmeliydi.
                                // Ama burada "Takım Lideri" sıfatıyla oradaysa, takım değişince gitmeli.
                                $mevcutProje->projeEkibi()->detach($eskiTakim->lider_user_id);
                            }
                        }

                        // 2. Projenin atanan takımını ve o andaki liderini güncelle
                        $mevcutProje->update([
                            'atanan_takim_id' => $this->atanan_cozum_takimi_id,
                            'atamadaki_lider_id' => $yeniAtananTakim->lider_user_id ?? null
                        ]);

                        // 3. Talep kaydını güncelle (iaa_talepleri)
                        DB::table('iaa_talepleri')
                            ->where('iaa_id', $mevcutProje->id)
                            ->update(['takim_id' => $this->atanan_cozum_takimi_id]);

                        // 4. Yeni Takım Liderini Ekle
                        // $yeniAtananTakim değişkeni yukarıda zaten bulunmuştu
                        if ($yeniAtananTakim && $yeniAtananTakim->lider_user_id) {
                            $mevcutProje->projeEkibi()->syncWithoutDetaching([
                                $yeniAtananTakim->lider_user_id => ['rol' => 'Lider']
                            ]);
                        }

                        $logAciklamalari[] = "Bağlı proje (ID:{$mevcutProje->id}) yeni takıma senkronize edildi.";
                    }
                }
                // === PROJE GÜNCELLEME SONU ===
            }

            // ... (Puanlama, Tarih vs. diğer mevcut kodlarınız) ...

            // 2. Tarih Değişikliği Kontrolü (Mevcut kodun)
            if ($sikayet->musteri_cozum_son_tarihi != $this->musteri_cozum_son_tarihi && !($user->hasRole('Müşteri Şikayeti Kurulu') && !$user->hasRole('Superadmin'))) {
                if (strpos(implode(" ", $logAciklamalari), "72 saat") === false) {
                    $logAciklamalari[] = "Çözüm son tarihi '{$this->musteri_cozum_son_tarihi}' olarak ayarlandı.";
                }
                if ($this->ek_sure_talep_durumu == 'Talep Edildi') {
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
                    $logIndex = -1;
                    foreach ($logAciklamalari as $index => $log) {
                        if (strpos($log, 'default puan') !== false) {
                            $logIndex = $index;
                            break;
                        }
                    }
                    if ($logIndex !== -1) {
                        unset($logAciklamalari[$logIndex]);
                    }
                    $logAciklamalari[] = "Çözüm puanı (Superadmin) {$hesaplananPuan} olarak hesaplandı.";
                }

                // !!! İPTAL EDİLEN KISIM BURASI !!!
                // Aşağıdaki if bloğunu sildiğimiz için artık sadece puanı güncelleyecek,
                // statüyü "Kapatıldı" yapmayacak.
                /*
                if ($this->musteri_puan > 0 && in_array($orijinalDurum, ['İşlemde', 'Çözümlendi'])) {
                    $yeniDurum = 'Kapatıldı';
                    $logAciklamalari[] = "Puanlama yapıldığı için durum otomatik olarak 'Kapatıldı' yapıldı.";
                }
                */
            }
            // Puan Dağıtımı (Mevcut kodun)
            if ($yeniDurum == 'Kapatıldı' && $this->musteri_puan > 0 && $sikayet->musteri_puan != $this->musteri_puan) {
                $this->dagitPuan($sikayet->atanan_cozum_takimi_id, $this->musteri_puan);
                $logAciklamalari[] = "Puan takıma dağıtıldı.";
            }


            // === VERİ GÜNCELLEME DİZİSİ (GÜNCELLENDİ) ===
            $etkiPuani = !empty($this->etki_puani) ? (int)$this->etki_puani : null;
            $karmasiklikPuani = !empty($this->karmasiklik_puani) ? (int)$this->karmasiklik_puani : null;
            $musteriPuan = $this->musteri_puan;

            // Eğer puanlama alanları boşsa ve puan henüz 0 ise varsayılan puanı uygula
            if (is_null($etkiPuani) && is_null($karmasiklikPuani) && ($musteriPuan == 0 || empty($musteriPuan))) {
                $musteriPuan = (int) (Setting::where('key', 'kurul_default_puan')->value('value') ?? 0);
            }

            $updateData = [
                'atanan_cozum_takimi_id' => $this->atanan_cozum_takimi_id,
                'musteri_durum' => $yeniDurum,
                'musteri_cozum_son_tarihi' => $this->musteri_cozum_son_tarihi,
                'etki_puani' => $etkiPuani,
                'karmasiklik_puani' => $karmasiklikPuani,
                'musteri_puan' => $musteriPuan,
                'ek_sure_talep_durumu' => $this->ek_sure_talep_durumu,
                'iaa_id' => $sikayet->iaa_id // <-- YENİ PROJE ID'SİNİ KAYDET
            ];

            // === Düzenleme Kilidi Mantığı (Mevcut kodun) ===
            if (!empty($this->atanan_cozum_takimi_id) && is_null($sikayet->edit_locked_at)) {
                $updateData['edit_locked_at'] = now();
                $logAciklamalari[] = "Şikayet işleme alındı ve müşteri düzenlemesine kilitlendi.";
            }
            // === KİLİT MANTIĞI SONU ===

            // !!!!! ÖNCE GÜNCELLEME YAP !!!!!
            $sikayet->update($updateData);


            // !!!!! ŞİMDİ MAİL GÖNDER (ÇÜNKÜ $sikayet->iaa_id ARTIK DOLU) !!!!!
            // === YENİ E-POSTA GÖNDERİM YERİ ===
            // Sadece bu işlemde yeni bir takım atandıysa mail gönder
            if ($yeniAtananTakim) { // $yeniAtananTakim'ı yukarıda (if bloğunda) doldurmuştuk
                try {
                    // $sikayet objesi artık güncel (iaa_id'si dolu)
                    // $yeniAtananTakim objesi de dolu
                    $this->notifyTeamAboutAssignment($sikayet, $yeniAtananTakim, null);
                } catch (\Exception $e) {
                    Log::error('Atama maili gönderilemedi (Triyaj). Hata: ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure(
                        $sikayet,
                        '"' . $sikayet->musteri_sikayet_konusu . '" şikayetinin takım atamasında bildirim gönderilemedi',
                        $yeniAtananTakim->uyeler ?? collect(),
                        $e->getMessage(),
                        null,
                        null,
                        $sikayet->sikayetKategori->bolum_id ?? null
                    );
                }
            }
            // === E-POSTA GÖNDERİM SONU ===


            // Loglama (Mevcut kodun - Eylem adı güncellendi)
            if (!empty($logAciklamalari)) {
                $eylemAdi = ($orijinalDurum == 'Yeni' && $yeniDurum == 'İşlemde') ? 'Atama Yapıldı (Triyaj)' : 'Şikayet Güncellendi (Triyaj)';

                $sikayet->loglar()->create([
                    'user_id' => $user->id,
                    'eylem' => 'Şikayet Güncellendi (Triyaj)', // 'Şikayet Güncellendi' yerine
                    'aciklama' => $user->name . " tarafından: " . implode(' ', array_unique($logAciklamalari)), // Tekrarlanan logları engelle
                ]);
            }

        }); // <-- DB::transaction kapanışı

        // Modal Kapatma (Mevcut kodun)
        $this->showModal = false;
        $this->dispatch('sikayetGuncellendi');
        // === YENİ OLAYI BURADA TETİKLE ===
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

        // === YENİ KOD: ATAMAYI KALDIR & PROJEYİ SIFIRLA ===
        if (!is_null($sikayet->iaa_id)) {
            $mevcutProje = Iaa::find($sikayet->iaa_id);
            if ($mevcutProje) {
                // 1. Eski Lideri Çıkar
                // $sikayet->cozumTakimi ilişkisi, update öncesi yüklendiyse eskiyi getirir.
                // Ancak update sonrası null dönebilir. O yüzden $takimAdi'ni aldığımız yerde id'yi de almalıydık.
                // Neyse ki $sikayet->cozumTakimi çağrısı yukarıda yapıldı ($takimAdi için).
                // Ama garanti olsun diye tekrar bakalım:
                // Eloquent cache mekanizması bazen yanıltabilir, o yüzden manuel query daha güvenli olabilir
                // Ama $takimAdi satırında $sikayet->cozumTakimi çağrıldığı için ilişki yüklü olmalı.
                if ($sikayet->cozumTakimi && $sikayet->cozumTakimi->lider_user_id) {
                    $mevcutProje->projeEkibi()->detach($sikayet->cozumTakimi->lider_user_id);
                }

                // 2. Projeyi Güncelle (Takımsız Hale Getir)
                $mevcutProje->update([
                    'atanan_takim_id' => null,
                    'durum' => 'Yeni' // Projeyi başlangıç durumuna çek
                ]);

                // 3. Talep Kaydını Güncelle
                DB::table('iaa_talepleri')
                    ->where('iaa_id', $mevcutProje->id)
                    ->update(['takim_id' => null, 'durum' => 'bekliyor']); // Talep durumunu da sıfırla

                // Log ekle (Transaction içinde değiliz burada ama olsun)
                $sikayet->loglar()->create([
                    'user_id' => $user->id,
                    'eylem' => 'Atama Kaldırıldı (Senkronizasyon)',
                    'aciklama' => "Şikayet ataması kaldırıldığı için bağlı proje (ID:{$mevcutProje->id}) de takımsız hale getirildi."
                ]);
            }
        }
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
            case 'Acil':
                return 4;
            case 'Yüksek':
                return 3;
            case 'Normal':
                return 2;
            case 'Düşük':
                return 1;
            default:
                return 1;
        }
    }

    private function dagitPuan($takimId, $puan)
    {
        if (!$takimId)
            return;

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

    /**
     * Atanan ekibe ve ayarlardaki ekstra kişilere bildirim gönderir.
     * GÜNCELLENDİ: Artık 2 farklı mail gönderiyor.
     * 1. SikayetAtamaBildirimi -> Ekip üyelerine ("Tarafınıza atandı")
     * 2. SikayetAtamaBilgilendirmesi -> Manuel listeye ("Ekibe atandı" bilgisi)
     */
    private function notifyTeamAboutAssignment(MusteriSikayeti $sikayet, Takim $team, ?User $user)
    {
        // 1. Ekip üyelerinin e-postaları
        $teamEmails = $team->uyeler()->pluck('email')->filter()->unique();

        if ($teamEmails->isNotEmpty()) {
            $mailable = new SikayetAtamaBildirimi($sikayet, $team, $user); // "Tarafınıza" maili
            foreach ($teamEmails as $recipient) {
                if (filter_var(trim($recipient), FILTER_VALIDATE_EMAIL)) {
                    Mail::to(trim($recipient))->queue($mailable);
                }
            }
        }

        // 2. Ayarlardan gelen ekstra manuel e-postalar
        $manualEmails = collect();
        $manualEmailsSetting = Setting::where('key', 'sikayet_atama_notify_manual_emails')->value('value');
        if (!empty($manualEmailsSetting)) {
            $manualEmails = collect(explode(',', $manualEmailsSetting))->filter()->unique();
        }

        if ($manualEmails->isNotEmpty()) {
            $mailableInfo = new SikayetAtamaBilgilendirmesi($sikayet, $team); // "Bilgilendirme" maili
            foreach ($manualEmails as $recipient) {
                if (filter_var(trim($recipient), FILTER_VALIDATE_EMAIL)) {
                    Mail::to(trim($recipient))->queue($mailableInfo);
                }
            }
        }

        // 3. Veritabanı ve Zil Bildirimleri (SikayetTakimaAtandiBildirimi)
        try {
            $recipients = collect();
            $snapshotEntries = []; // Snapshot'a eklenecek yeni kayıtlar

            $addToSnapshot = function ($user, $roleLabel) use (&$snapshotEntries) {
                $snapshotEntries[] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->telefon ?? $user->phone ?? null,
                    'photo' => $user->profile_photo_path,
                    'role_label' => $roleLabel . ' (Takım Atandı)',
                    'notified_at' => now()->toDateTimeString(),
                ];
            };
            
            // a) Takım Lideri
            try {
                if ($team && $team->lider_user_id) {
                    $teamLeader = User::find($team->lider_user_id);
                    if ($teamLeader) {
                        $teamLeader->notify(new \App\Notifications\SikayetTakimaAtandiBildirimi($sikayet, 'lider'));
                        $addToSnapshot($teamLeader, 'Takım Lideri');
                    }
                }
            } catch (\Exception $e) {
                Log::error('Takım lideri bildirim hatası: ' . $e->getMessage());
            }

            // b) Direktör, Kalite Yöneticisi ve Bölüm Lideri
            $kategoriId = $sikayet->sikayet_kategorisi_id;
            if ($kategoriId) {
                $kategori = \App\Models\SikayetKategori::find($kategoriId);
                if ($kategori && $kategori->bolum_id) {
                    $bolum = $kategori->bolum;

                    // Direktör
                    try {
                        if ($bolum && $bolum->director_id) {
                            $direktor = User::find($bolum->director_id);
                            if ($direktor) {
                                $direktor->notify(new \App\Notifications\SikayetTakimaAtandiBildirimi($sikayet, 'direktor'));
                                $addToSnapshot($direktor, 'Bölüm Direktörü');
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Direktör bildirim hatası: ' . $e->getMessage());
                    }

                    // Kalite Yöneticileri (Mapping üzerinden)
                    try {
                        $kaliteYoneticileriMapping = User::whereHas('yonettigiSikayetKategorileri', function($q) use ($kategoriId) {
                            $q->where('sikayet_kategori_id', $kategoriId);
                        })->get();
                        
                        // Ek olarak rol bazlı kontrol (Bölüm bazlı kalite yöneticileri varsa)
                        // ÖNEMLİ: Karakter kodlaması ve rolün varlığı kontrol ediliyor.
                        $roleName = 'Bölüm Kalite Yöneticisi';
                        $roleExists = \Spatie\Permission\Models\Role::where('name', $roleName)->exists();
                        
                        $kaliteYoneticileriRole = collect();
                        if ($roleExists) {
                            $kaliteYoneticileriRole = User::role([$roleName])
                                ->where('bolum_id', $kategori->bolum_id)
                                ->get();
                        }

                        $allKalite = $kaliteYoneticileriMapping->merge($kaliteYoneticileriRole)->unique('id');

                        foreach ($allKalite as $ky) {
                            $ky->notify(new \App\Notifications\SikayetTakimaAtandiBildirimi($sikayet, 'kalite'));
                            $addToSnapshot($ky, 'Bölüm Kalite Yöneticisi');
                        }
                    } catch (\Exception $e) {
                        Log::error('Kalite yöneticisi bildirimleri hatası: ' . $e->getMessage());
                    }

                    // Bölüm Liderleri
                    try {
                        $roleNameBL = 'Bölüm Lideri';
                        $roleBLExists = \Spatie\Permission\Models\Role::where('name', $roleNameBL)->exists();
                        
                        $bolumLiderleri = collect();
                        if ($roleBLExists) {
                            $bolumLiderleri = User::role([$roleNameBL])->where('bolum_id', $kategori->bolum_id)->get();
                        }
                        
                        foreach ($bolumLiderleri as $bl) {
                            $bl->notify(new \App\Notifications\SikayetTakimaAtandiBildirimi($sikayet, 'bolum_lideri'));
                            $addToSnapshot($bl, 'Bölüm Lideri');
                        }
                    } catch (\Exception $e) {
                        Log::error('Bölüm lideri bildirimleri hatası: ' . $e->getMessage());
                    }
                }
            }

            // c) Müşteri Temsilcileri ve Ek İlgililer (Tekilleştirilmiş)
            try {
                $customerUsers = collect();
                if ($sikayet->customer_id) {
                    $customerUsers = User::where('customer_id', $sikayet->customer_id)->get();
                }

                $ekYetkililer = $sikayet->ekYetkililer ?: collect();
                
                // İki grubu birleştir ve ID'ye göre tekilleştir
                // Öncelik: Eğer kullanıcı her iki listede de varsa, 'Ek İlgili' olarak işaretlenebilir (veya tam tersi)
                // Burada customerUsers'ı temel alıp ekYetkililer'i üzerine ekliyoruz.
                $allMusteriRecipients = $customerUsers->merge($ekYetkililer)->unique('id');

                foreach ($allMusteriRecipients as $u) {
                    // Kullanıcı tipini belirle (Snapshot için)
                    $isEkYetkili = $ekYetkililer->contains('id', $u->id);
                    $roleLabel = $isEkYetkili ? 'Ek İlgili' : 'Müşteri Yetkilisi';
                    $notifyType = $isEkYetkili ? 'ek_ilgili' : 'musteri';

                    $u->notify(new \App\Notifications\SikayetTakimaAtandiBildirimi($sikayet, $notifyType));
                    $addToSnapshot($u, $roleLabel);
                }
            } catch (\Exception $e) {
                Log::error('Müşteri/Ek İlgili bildirimleri hatası: ' . $e->getMessage());
            }


            // d) Snapshot Güncelleme
            if (!empty($snapshotEntries)) {
                $currentSnapshot = json_decode($sikayet->notified_snapshot, true) ?: [];
                
                foreach ($snapshotEntries as $entry) {
                    $currentSnapshot[] = $entry;
                }

                // Tekilleştirme: Kullanıcı bazlı en son bildirimi tut
                $uniqueSnapshot = collect($currentSnapshot)->unique(function ($item) {
                    return $item['user_id'] . $item['role_label'];
                })->values()->toArray();

                $sikayet->update(['notified_snapshot' => json_encode($uniqueSnapshot)]);
            }

        } catch (\Exception $e) {
            Log::error('Takım atama zil bildirimleri hatası: ' . $e->getMessage() . ' | Dosya: ' . $e->getFile() . ':' . $e->getLine());
            \App\Helpers\MailLogHelper::logFailure(
                $sikayet,
                '"' . $sikayet->musteri_sikayet_konusu . '" şikayetinin atama zil bildirimleri gönderilemedi',
                collect(),
                $e->getMessage(),
                null,
                null,
                $sikayet->sikayetKategori->bolum_id ?? null
            );
        }
    }
    // === YENİ EKLENEN METODUN SONU ===

    public function render()
    {
        return view('livewire.admin.sikayet-triyaj-modal');
    }
}
