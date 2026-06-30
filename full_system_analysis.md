# KÖKSAN IAA & TAKVİM: TAM KAPSAMLI SİSTEM ANALİZ RAPORU (V3)

Bu rapor, projenin tüm teknik katmanlarını (MVC, Services, DB, Event/Notification) başka bir yapay zekaya (LLM) "sistem uzmanı" seviyesinde aktarmak için hazırlanmıştır.

---

## 1. VERİTABANI VE MODEL KATMANI (DATABASE & MODELS)

Sistem `iaa_db` (Ana Operasyon) ve `takvim` (CRM/Saha) olarak iki ayrı veritabanı üzerinde yükselir.

### 1.1. Temel Tablo Grupları
- **Çekirdek Operasyon:** `iaas` (Projeler), `iaa_workflow_steps` (Dinamik Adımlar), `musteri_sikayetleri` (Giriş Kaynağı).
- **Organizasyon & SQUAD:** `users`, `roles`, `takimlar`, `takim_user`, `bolumler`.
- **İdari Süreçler:** `disciplinary_cases` (Disiplin), `arabuluculuk_cases` (Hukuk), `ciplinary_votes` (Kurul Oyları).
- **Lojistik & CRM (Takvim DB):** `customer_visits`, `events`, `vehicle_assignments`.

### 1.2. Model Akılları (Intelligence)
- **Dinamik Attribute'lar:** Modellerde `getDurumEtiketiAttribute` gibi metodlarla UI statüleri kod seviyesinde yönetilir.
- **Pivot İlişkiler:** `iaa_user` tablosu ile projelere özel "SQUAD" ekipleri kurulur.

---

## 2. İŞ MANTIKLARI VE SERVİSLER (CONTROLLERS & SERVICES)

### 2.1. ProjectWorkspaceController (Sistemin Motoru)
Tüm IAA projelerinin can damarıdır. 
- **Adım Yönetimi:** `storeStep` metodu ile dinamik workflow adımları (Kök Neden, Aksiyon vb.) tamamlanır.
- **Onay Akışı:** Hatalı bildirim (`markAsFaulty`), Ek süre talebi (`requestExtension`) ve Proje Kapama süreçleri burada yönetilir.
- **Yetki Kontrolü:** Sadece SQUAD üyeleri veya Adminler işlem yapabilir.

### 2.2. Service Katmanı
- **`RaporVeriServisi`:** Otomatik mail raporları için ham veri toplar.
- **`KullaniciPuanService`:** Personel performansını parametrik olarak (tamamlanan iş, süre, kalite) hesaplar.

---

## 3. BİLDİRİM VE İLETİŞİM SİSTEMİ (NOTIFICATIONS)

Sistemde **44 farklı bildirim sınıfı** (Notification) bulunur. Bu, sistemin "Yaşayan" bir yapı olduğunu kanıtlar.
- **Kritik Bildirimler:** `YeniMusteriSikayeti`, `IaaProjesineTalepGeldi`, `DisiplinKurulunaSevkEdildi`, `ZiyaretOnayBekliyor`.
- **Kanal:** Laravel Notification üzerinden hem Veritabanı (`notifications` tablosu) hem de Mail kanalı eşzamanlı kullanılır.

---

## 4. ROTALAMA VE GÜVENLİK (ROUTES & MIDDLEWARE)

### 4.1. Route Mimarisi (`web.php`)
- **770 Satırlık Dev Yapı:** Sistem Roller ve Prefix'ler (admin/, disiplin/, arabuluculuk/) ile bölümlenmiştir.
- **Müşteri/Misafir Portalı:** `PublicSikayetController` ve `GuestIaaController` üzerinden token tabanlı (giriş gerektirmeyen) erişim sağlanır.
- **Middleware Gücü:** `BlockCustomerAccess` ile personel/müşteri ayrımı, `role:Superadmin` ile admin paneli korunur.

---

## 5. UI/UX VE FRONTEND (BLADE & LIVEWIRE)

- **Livewire Dominant:** `SikayetGorevlerim`, `MusteriYonetimi`, `ZiyaretListesi` gibi karmaşık UI bileşenleri Livewire ile real-time çalışır.
- **Layouts:** `admin.blade.php`, `guest.blade.php` ve `app.blade.php` olarak 3 ana tema bulunur.
- **Responsive:** Tüm UI bileşenleri mobil uyumlu `Tailwind` ve `Custom CSS` ile güçlendirilmiştir.

---

## 6. SİSTEMİN AYIRICI ÖZELLİKLERİ (FOR LLM)

1.  **Hibrit Mimari:** Laravel 10+, Livewire 3 ve çift DB entegrasyonu.
2.  **Dinamik Workflow:** Adımlar sabittir ama içerikleri (Widget'lar) dinamik olarak `ProjectWorkspace` üzerinden yönetilir.
3.  **Role-Based Logic:** Yetki statik bir `Role` değil, o andaki Proje-User-Bölüm-Direktör ilişkisinin bir sonucudur.
4.  **Audit Log:** Hemen her modelin `Log` tablosu vardır (Örn: `ArabuluculukLog`).

**Özet:** Bu proje; teknik olarak karmaşık, idari olarak katı onay mekanizmalarına sahip, operasyonel olarak ise esnek bir SQUAD yapısını barındıran dev bir kurumsal platformdur.
