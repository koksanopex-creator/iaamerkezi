YOU MUST ALWAYS FOLLOW THESE RULES. NEVER IGNORE THEM.

================================================================================
# GLOBAL PROJECT RULES
================================================================================

## 1. ENVIRONMENT & DEPLOYMENT
- Proje localde geliştirilir, periyodik olarak canlıya alınır.
- Local ortam: root dizin (/)
- Production: /iaa alt klasöründe çalışır (https://kys.koksan.com/iaa)
- Serverdaki işletim sistemim Ubuntu 24.04.3 LTS
- Serverdaki veritabanım MySQL 8.0.45
- Serverdaki PHP versiyonum 8.3.6
- Serverdaki Laravel versiyonum 12.31.1
- Serverdaki Livewire versiyonum 3.6.4
- Serverdaki nginx versiyonum 1.24.0
- Serverdaki node versiyonum 22.21.0
- Serverdaki npm versiyonum 10.9.4
- Serverdaki composer versiyonum 2.9.2
- Serverdaki git versiyonum 2.43.0
- Serverdaki phpmyadmin versiyonum 5.2.1

### SUNUCU PROJE DİZİNLERİ VE CANLI URL'LER (PRODUCTION)
1. **IAA Projesi (Şikayetler, Disiplin vb.)**
   - Sunucu Dizini: `/var/www/kys_koksan/iaa`
   - Canlı URL: `https://kys.koksan.com/iaa/`

2. **Merkezi API (Merkezi Yönetim Sistemi Core)**
   - Sunucu Dizini: `/var/www/kys_koksan/merkezi_yonetim_sistemi_core`
   - Canlı URL: `https://kys.koksan.com/merkezi_yonetim_sistemi/`

3. **Koksan Takvim (İş Süreçleri / CRM / Saha)**
   - Sunucu Dizini: `/var/www/koksan-takvim`
   - Canlı URL: `https://kys.koksan.com/koksan_is_surecleri/login`

### SUNUCU ERİŞİM BİLGİLERİ (VPN & SSH)
- **VPN Türü**: SSL VPN (WatchGuard)
- **VPN Public IP**: `82.222.9.114` (Port: `10443`, Protokol: `TCP`)
- **İç Ağ Sunucu IP**: `172.20.5.10`
- **SSH Kullanıcı**: `tamadmin`
- **SSH Parola**: `8LUMFS0UVX90.`

### ENVIRONMENT SAFE URL RULE
- Hardcoded URL/path kullanmak YASAK:
  ❌ /storage/...
  ❌ /admin/...
  ❌ sabit string URL

- Her zaman kullan:
  ✅ asset()
  ✅ url()
  ✅ route()
  ✅ Storage::url()

- Amaç: ortam değiştiğinde linkler kırılmamalı

---

## 2. RESPONSIVE DESIGN
- Tüm sayfalar %100 responsive olmalı (mobil uyumlu)

---

## 3. LAYOUT RULE
- Ana container'larda scroll (overflow) kullanmak YASAK
- Tasarım her zaman ekrana sığmalı

---

## 4. FILE UPLOAD STRUCTURE

- Tüm upload işlemleri storage mantığı ile yapılmalı

### ZORUNLU DOSYA YAPISI:
storage/{ana_klasor}/{kullanici_veya_firma}/{kayit_id}/{tarih_saat}_{random}.{ext}

### ÖRNEK:
storage/disiplin_kanitlar/Cihangir_Kaplan/1/31.03.2025_10.33_uT.jpg

### KURALLAR:
- ana_klasor → modül bazlı
- kullanıcı/firma → yükleyen veya ilgili kişi
- kayıt_id → ilgili entity id
- tarih formatı → dd.MM.yyyy_HH.mm
- sona 2 karakter random eklenmeli
- uzantı korunmalı

---

## 5. USER PROFILE LINKING
- Kullanıcı ismi geçen her yerde clickable olmalı
- URL: /kullanici-profil/{id}

---

## 6. CUSTOMER PROFILE LINKING
- Müşteri ismi geçen her yerde clickable olmalı
- URL: /musteri-profil/{id}

---

## 7. DIRECTOR PERMISSIONS

- Direktör yetkisi:
  bolumler tablosundaki director_id sütununa göre belirlenir
- Veritabanı doğrulaması: bolumler.director_id → users.id (Rol ID=8 "Direktör")
- Direktör:
  - sorumlu olduğu bölümlere ait HER şeyi görür
  - tüm hareketlerden haberdar olur

### BİLDİRİMLER (Zil + Mail, mailde tarih zorunlu)

1. "Direktörlüğünüze bağlı {bolum} bölümüne {baslik} başlıklı yeni bir şikayet eklenmiştir."
2. "Direktörlüğünüze bağlı {bolum} bölümü kullanıcısı olan {kullanici} adına {olusturan} tarafından yeni bir tutanak oluşturulmuştur."
3. "Direktörlüğünüze bağlı {bolum} bölümü kullanıcısı olan {kullanici}, {proje} projesinde ekip üyesi olmuştur."

---

## ROLE INTELLIGENCE RULE

- Yetki kontrolleri controller içinde if ile yapılmaz
- User modeline eklenmeli:
  - isDirector()        → bolumler.director_id = user.id kontrolü
  - isDepartmentLeader() → Rol ID=2 "Bölüm Lideri" kontrolü
  - getResponsibleDepartments() → director_id veya bolum_id üzerinden

- Amaç: temiz kod, tekrar azaltma, doğru yetkilendirme

---

## 8. COMPLAINT vs IAA

- Tüm kayıtlar iaas tablosunda tutulur
- Ayrım: iaas.oneri sütunu
  - oneri DOLU  → IAA (iyileştirme önerisi mevcut)
  - oneri BOŞ   → ŞİKAYET (projeye dönüşmüş müşteri şikayeti)

### ⚠️ KIRILGANLIK UYARISI (Gerçek koddan doğrulandı)
- iaas.oneri kolonu nullable TEXT alanı — bu kırılgan ama mevcut çalışan yapı
- Yeni özellik eklerken bu ayrımı her zaman if/else ile kontrol et
- NULL ile boş string ('') farkına dikkat et — her ikisi de "şikayet" sayılır
- İleride type ENUM('iaa','sikayet') sütunu eklenirse bu kural güncellenecek

### iaas tablosunda dikkat edilmesi gereken diğer alanlar:
- durum: 'Onay Bekliyor' default
- direktor_onay_tarihi: Direktör onay döngüsü bu alanda
- atanan_takim_id: Atanan takım FK
- hatali_bildirim_*: Hatalı bildirim sürecinin tüm alanları burada
- talep_direktor_*: Direktör talep sürecinin alanları burada

---

## 9. DEPARTMENT LEADER

- Bölüm lideri = müdür = Rol ID 2 "Bölüm Lideri"
- Kendi bölümündeki tüm kullanıcı hareketlerini görmeli ve bildirim almalı

### BİLDİRİMLER (Zil + Mail, mailde tarih zorunlu)

1. "Lideri olduğunuz {bolum} bölümüne {baslik} başlıklı yeni bir şikayet eklenmiştir."
2. "Lideri olduğunuz {bolum} bölümü kullanıcısı olan {kullanici} adına {olusturan} tarafından yeni bir tutanak oluşturulmuştur."
3. "Lideri olduğunuz {bolum} bölümü kullanıcısı olan {kullanici}, {proje} projesinde görev almaya başlamıştır."

---

## 10. FILTERING
- Tüm listelerde tarih filtresi + başlık/ad filtresi olmalı
- Default: tüm zamanlar (sayfa açıldığında filtre uygulanmadan tüm veri)

---

## QUERY OPTIMIZATION RULE

- N+1 YASAK → her zaman ->with() kullan
- Büyük veri → pagination zorunlu
- Filtreleme → DB seviyesinde (PHP'de değil)

### ⚠️ KRİTİK: ProjectWorkspaceController
- Bu controller çok sayıda ilişki yüklüyor (SQUAD, Adımlar, Ziyaretler vb.)
- Bu dosyaya dokunurken ->with() zincirini mutlaka kontrol et
- Gereksiz eager load ekleme, mevcut olanları koru

### ⚠️ KRİTİK: SyncAllToTakvim Command
- Şu an Customer::all() ile tüm tabloyu çekiyor — büyük veri riski
- Bu command'a dokunurken chunk(100) ekle

---

## 11. LOGGING

- Her işlem loglanmalı: user_id, işlem, entity_id, tarih/saat
- UI: son 10 kayıt + "Tümünü Gör" butonu
- "Tümünü Gör" → ayrı sayfa VEYA küçük scroll modal
- ⚠️ Modal küçük ve yönetilebilir boyutta olmalı, tam ekran/kocaman modal YASAK
- Modal içinde scroll-bar ile kaydırma sağlanmalı

---

## 12. COMMAND RULE

- php artisan tinker veya benzeri komutlar istendiğinde:
  → Kullanıcıya "sen çalıştır" diye BIRAKILMAZ
  → Komutu kendin çalıştır, sonucu paylaş
  → "Run et, sana bırakıyorum" demek YASAK

---

## 13. BACKWARD COMPATIBILITY

- Mevcut kod silinmez, özellik kaldırılmaz
- Sadece ekleme yapılır
- Aksini kullanıcı açıkça belirtmedikçe hiçbir mevcut işlevselliğe dokunulmaz

---

================================================================================
# PROJE MİMARİSİ & BAĞLAM (Derin Sistem Analizi ile Doğrulanmış)
================================================================================

## TEKNİK YAPI
- Framework: Laravel 10+ / Livewire 3 / MariaDB 10.4
- Ana DB: iaa_db (Port 8000) — 68 tablo
- İkincil DB: takvim (Port 8001, CRM/Saha)
- IAA ↔ Takvim köprüsü: PlanVisit.php → Http::post() → CustomerSyncController.php
- Rol yönetimi: Spatie Permission

## REQUEST AKIŞI
web.php → Middleware → Controller → Service → Policy → Model → Notification → Blade/Livewire

## GERÇEK ROL LİSTESİ (SQL doğrulandı)
| ID | Rol Adı                         |
|----|----------------------------------|
| 1  | Superadmin                       |
| 2  | Bölüm Lideri                     |
| 3  | Kullanıcı                        |
| 4  | Müşteri Şikayeti Kurulu          |
| 5  | Müşteri Şikayeti Çözüm Lideri   |
| 6  | Müşteri Temsilcisi               |
| 7  | Bölüm Kalite Yöneticisi         |
| 8  | Direktör                         |
| 9  | Hukuk Admini                     |
| 10 | Hukuk Yöneticisi                |
| 11 | Disiplin Kurulu Başkanı         |
| 12 | Disiplin Kurulu Üyesi           |
| 13 | Arabuluculuk Personel Lideri    |
| 14 | Arabuluculuk Personel           |
| 15 | Arabuluculuk Kurulu Başkanı     |
| 16 | Arabuluculuk Kurulu Üyesi       |
| 17 | Arabuluculuk Finans             |
| 18 | Dış Avukat                       |
| 19 | Yonetim                          |

### ROL KULLANIM KURALLARI:
- Yetki yazarken rol adını değil rol ID'sini veya Spatie'nin name alanını kullan
- "Direktör" rolü (ID=8) VE bolumler.director_id ayrı kavramlar — ikisini karıştırma
  → Direktör rolü: sistemdeki genel direktör yetkisi
  → bolumler.director_id: hangi bölümün direktörü olduğunu gösterir

## KRİTİK TABLO İLİŞKİLERİ
- iaas ↔ musteri_sikayetleri → iaa_id FK (şikayet projeye dönüşebilir)
- bolumler.director_id → users.id (direktör ataması)
- bolumler → neredeyse tüm operasyonel modeller bolum_id'ye bağlı
- users.bolum_id → bolumler.id (kullanıcının bölümü)
- users.customer_id → customers.id (müşteri kullanıcıları için)
- users.is_mavi_yaka → mavi yaka personel flag'i
- users.can_issue_disciplinary → disiplin tutanağı açabilme yetkisi
- users.onaylandi_mi → kayıt onay sistemi (settings'de kayit_onay_sistemi=1)
- takim_user → pivot (kullanıcı birden fazla takımda olabilir)
- takimlar.tur → 'iaa' veya 'sikayet' (takım tipi)

## SETTINGS TABLOSU — GERÇEK KEY'LER (Koddan doğrulandı)
| Key | Değer | Açıklama |
|-----|-------|----------|
| musteri_sikayeti_standart_puan | 2 | Şikayet giriş puanı |
| musteri_sikayeti_cozum_carpan | 10 | Çözüm puanı çarpanı |
| kurul_default_puan | 2 | Kurul varsayılan puan |
| iaa_oneri_puani | 2 | İyileştirme öneri puanı |
| standart_puan | 100 | Proje tamamlama standart puanı |
| sikayet_direktor_onayi_aktif | 1 | Direktör onay döngüsü (1=açık) |
| kayit_onay_sistemi | 1 | Yeni kayıtlarda admin onayı zorunlu |
| site_logo | logos/... | Logo path |
| sikayet_response_time_hours | 72 | Müşteri yanıt süresi (saat) |
| sikayet_notify_bolum_lideri | 0 | Bölüm liderine şikayet bildirimi |
| new_user_notify_bolum_lideri | 1 | Yeni kullanıcıda bölüm liderine bildir |
| new_user_notify_direktor | 0 | Yeni kullanıcıda direktöre bildir |
| kvkk_text | (metin) | KVKK sözleşme metni |
| sikayet_onay_email_subject/body | (şablon) | Mail şablon içeriği |
| new_customer_email_subject/body | (dolu) | Müşteri hoşgeldin maili |
| para_birimleri | TL,USD,EUR | Para birimleri |
| sikayet_atama_notify_manual_emails | (liste) | Manuel bildirim e-posta listesi |
| dashboard_tab_order_{user_id} | (json) | Kişiye özel tab sıralaması |

- Settings'e yeni key eklerken mevcut key'leri silme
- Setting::where('key', '...')->value('value') ile oku

## ŞİŞMİŞ DOSYALAR — KRİTİK UYARI
Bu dosyalara yeni kod eklerken MUTLAKA Service'e taşı:
  - IaaYonetimController.php     (~67KB) → IaaYonetimService oluştur
  - SikayetController.php        (~35KB) → SikayetService oluştur
  - DashboardController.php      (~26KB) → Mevcut Dashboard servisleri kullan
  - IaaController.php            (~25KB) → Service'e taşı
  - ProfileController.php        (~20KB) → Service'e taşı

## MEVCUT SERVİS SINIFLARI (önce bak, sonra yeni oluştur)
  - ProjeAdimIslemleriService    → ProjectWorkspaceController
  - ProjeTamamlamaService        → ProjectWorkspaceController
  - ProjeCalismaAlaniService     → ProjectWorkspaceController
  - ProjeTalepYonetimService     → ProjectWorkspaceController
  - MusteriBildirimService       → ProjectWorkspaceController
  - KullaniciPuanService         → DashboardController, SyncTeamScores
  - RaporVeriServisi             → RaporlariKontrolEt (Command)
  - BolumDashboardService        → DashboardController
  - SikayetDashboardService      → DashboardController
  - SuperAdminDashboardService   → DashboardController
  - HukukDashboardService        → DashboardController
  - MusteriDashboardService      → DashboardController
  - YonetimDashboardService      → DashboardController
  - KullaniciIstatistikService   → DashboardController
  - GeminiService                → GlobalChatBot (Livewire)
  - AiTools                      → GlobalChatBot (Livewire)

## QUEUE DURUMU (Kod incelemesinden — kritik bilgi)
- Queue'lu Notification (13/44): IaaProjesineTalepGeldi, IaaTalebiSonuclandi,
  MusteriGeriBildirimBildirimi, NewUserAddedNotification, PersonelProjeyeDavetEdildi,
  PersonelTakimBildirimi, ProjeDavetYaniti, TakimaDavetEdildi, TakimdanCikarildi,
  TakimDavetiYanitlandi, TakimIstegiKabulEdildi, VisitStatusChanged,
  YeniMusteriSikayetiBildirimi
- Queue'lu Mail (4/11): SikayetAtamaBildirimi, SikayetAtamaBilgilendirmesi,
  SikayetOnayMail, YeniSikayetBildirimi
- ⚠️ Geri kalan 31 bildirim ve 7 mail SENKRON çalışıyor — yeni bildirim eklerken
  mutlaka ShouldQueue implement et

## OBSERVER - MODEL BAĞLANTILARI (AppServiceProvider satır 85-91)
| Observer | Model | Tetiklenme |
|---|---|---|
| MusteriSikayetiObserver | MusteriSikayeti | Şikayet oluşturma/güncelleme → Takvim senkron |
| IaaObserver | Iaa | Proje oluşturma/güncelleme |
| TakimDavetiyesiObserver | TakimDavetiyesi | Davetiye statü değişimi → Bildirim |
| CustomerObserver | Customer | Müşteri değişikliği → Takvim senkron |
| ContactObserver | User | Personel değişikliği → Takvim senkron |
| SikayetIadesiObserver | SikayetIadesi | İade değişikliği → Takvim senkron |

### OBSERVER SONSUZ DÖNGÜ KORUMASI
- is_syncing flag'i ile Observer→API→Observer zinciri korunuyor
- Observer'larda Takvim sync varsa bu flag'i koru, dokunma

## SOFTDELETES OLAN MODELLER
- users (deleted_at)
- musteri_sikayetleri (deleted_at)
- arabuluculuk_cases (deleted_at)
- arabulucular (deleted_at)
- bolumler (deleted_at)
- Sorgu yazarken withTrashed() farkında ol

## GÜVENLİK NOTLARI
- CustomerSyncController.php (Takvim API) → herhangi bir middleware OLMADAN açık
  → Production'da IP kısıtlaması veya token koruması eklenmeli
- Rate limiting sadece /oneri-yap ve /sikayet rotalarında aktif
- Yeni public endpoint açarken throttle middleware ekle

## DEPRECATED DOSYA
- SikayetCozumGorevlerim(silinecek).php → yeni kod EKLEME, dokunma

## WORKFLOW YAPISI
- IaaWorkflow + IaaWorkflowStep → şablon (8D, Kaizen vb.)
- IaaProgressUpdate → adım tamamlanma verisi
- IaaStepAssignment → adıma özel personel ataması

## AI ENTEGRASYONU
- GlobalChatBot.php (Livewire) → GeminiService (Google Gemini API) + AiTools
- Her istekte kullanıcı/bölüm/URL context dinamik inject ediliyor
- Yeni AI özelliği eklenecekse GeminiService'i genişlet, yeni servis açma

---

================================================================================
# AI BEHAVIOR RULES
================================================================================

## THINK FIRST
- Önce analiz et, sonra kod yaz

## CONTEXT AWARE
- Mevcut yapıyı bozmadan ilerle

## MINIMUM CHANGE
- Sadece gerekli değişiklikleri yap

## DRY
- Tekrar eden kod yazma

## SECURITY
- XSS, SQL Injection, Mass Assignment her zaman göz önünde bulundur

## EXPLICIT CODE
- Açık ve okunabilir kod yaz

## STEP BY STEP
- Sıralama: migration → model → controller → view

## DEBUG FRIENDLY
- Loglanabilir yapı kur

## PERFORMANCE
- Gereksiz query yazma

## OUTPUT FORMAT
- Dosya adını belirt
- Nereye ekleneceğini yaz

---

================================================================================
# TEST ACCOUNT CREDENTIALS (DEVELOPMENT ONLY)
================================================================================

Aşağıdaki bilgiler sadece local ve test ortamlarında yetki kontrollerini simüle etmek için kullanılmalıdır. 
Şifreler, e-posta adresleri ile aynıdır.

| Rol | Kullanıcı Adı | E-posta / Şifre |
| :--- | :--- | :--- |
| **Bölüm Kalite Yöneticisi** | Serkan Tölek | serkan.tolek1@koksan.com |
| **Direktör** | Şenol Kanat | senol.kanat1@koksan.com |
| **Bölüm Lideri (Preform)** | Emrah Al | emrah.al1@koksan.com |
| **Disiplin Kurulu Başkanı** | - | disiplin.kurul.baskani@koksan.com |
| **Disiplin Kurulu Üyesi** | - | disiplin.kurul.uyesi@koksan.com |
| **Hukuk Admini** | Beyza Açıkalın | beyza.acikalin1@koksan.com |
| **Hukuk Yöneticisi** | - | hukuk.yoneticisi@koksan.com |
| **Müşteri Şikayeti Çözüm Lideri** | Erhan Cesur | erhan.cesur1@koksan.com |
| **Müşteri Temsilcisi** | Caner Demir | caner.demir@arasatlas.com |

⚠️ **NOT:** Bu kullanıcılar üzerinden işlem yaparken `Log` ve `Notification` sistemlerinin bu ID'ler üzerinden tetikleneceğini unutma.

================================================================================
# LARAVEL BEST PRACTICES
================================================================================

## MVC
- Controller: yönlendirir
- Service: iş mantığı
- Model: veri

## CONTROLLER → Şişirme YASAK

## SERVICE → Business logic burada

## VALIDATION → FormRequest kullan

## MODEL
- fillable zorunlu
- İlişkiler tanımlı olmalı
- SoftDeletes olan modeller: users, musteri_sikayetleri, arabuluculuk_cases,
  arabulucular, bolumler (deleted_at sütunu mevcut)

## DATABASE
- Migration zorunlu
- Foreign key kullan

## ROUTE → RESTful yapı

## STORAGE → Storage::put() kullan

## AUTH
- Policy/Gate kullan
- Mevcut: IaaPolicy.php ve MusteriSikayetiPolicy.php
- Yeni modüllere de Policy ekle

## BLADE → Business logic YAZMA

## QUEUE
- Mail/bildirim ShouldQueue implement edilmeli
- Yeni notification veya mail eklerken Queue zorunlu

## ERROR → try-catch kullan

---

================================================================================
# STRICT MODE
================================================================================

- Eksik kod YASAK
- "// ..." YASAK
- "sen tamamla" YASAK
- "sana bırakıyorum" YASAK

- Her feature:
  → tam yazılmalı
  → çalışır halde teslim edilmeli

- Değişiklik gerekiyorsa:
  → açıkça belirt
  → nedenini yaz
