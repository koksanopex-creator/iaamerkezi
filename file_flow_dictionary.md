# KÖKSAN IAA & TAKVİM: DOSYA AKIŞ VE İLİŞKİ SÖZLÜĞÜ (V4)

Bu döküman, projedeki kilit dosyaların rollerini ve "Controller -> Model -> View" üçgeninde nasıl haberleştiklerini açıklamaktadır.

---

## 1. MÜŞTERİ ŞİKAYETİ AKIŞI (Complaint Flow)

### 1.1. SikayetController.php (`app/Http/Controllers/Admin/`)
-   **Görevi:** Şikayetlerin CRUD işlemlerini yönetir, puan hesaplar ve bildirimleri tetikler.
-   **Model ile İlişkisi:** `MusteriSikayeti` modelini kullanarak veriyi DB'ye yazar. `SikayetKategori` modelinden yetki kontrolü (bolum_id) yapar.
-   **Haberleşme:** 
    - `store()` metodu: Formdan gelen veriyi alır, `Setting` modelinden standart puanı çeker, `MusteriSikayeti` oluşturur.
    - `ComplaintNotificationTrait` kullanarak ilgili liderlere mail/sistem bildirimi gönderir.

### 1.2. MusteriSikayeti.php (`app/Models/`)
-   **Görevi:** Veritabanı şeması ile kod arasındaki köprüdür.
-   **Controller ile İlişkisi:** `$fillable` dizisi ile Controller'dan hangi verilerin gelebileceğini sınırlar.
-   **Haberleşme:** `teknikDetaylar()`, `dosyalar()`, `iaa()` gibi ilişkiler (Relations) üzerinden bağlı verileri toplar.

### 1.3. create.blade.php / show.blade.php (`resources/views/admin/sikayetler/`)
-   **Görevi:** Kullanıcı arayüzüdür.
-   **Haberleşme:** 
    - Controller'dan `compact('kategoriler', 'sikayet')` ile gelen nesneleri alır.
    - `$sikayet->musteri_durum` değerine göre Blade içinde `@if` blokları ile farklı butonlar/renkler gösterir (Yorumlama).

---

## 2. IAA PROJE MOTORU (Project Engine)

### 2.1. ProjectWorkspaceController.php (`app/Http/Controllers/`)
-   **Görevi:** Proje çalışma alanındaki (Workspace) tüm aksiyonları yönetir. 
-   **Servis Yapısı:** İş mantığını direkt yazmaz; `ProjeAdimIslemleriService`, `ProjeTamamlamaService` gibi servisleri çağırır (Dependency Injection).
-   **Haberleşme:** `show($id)` metodu ile projeyi (`Iaa` modeli) ve tüm ilişkili datayı (SQUAD, Adımlar, Ziyaretler) yükleyip View'e basar.

### 2.2. Iaa.php (`app/Models/`)
-   **Görevi:** Proje ana varlığıdır.
-   **Haberleşme:** `iaa_workflow_steps` tablosu ile projenin hangi aşamada olduğunu tutar. `users()` ilişkisi ile SQUAD ekibiyle haberleşir.

### 2.3. show.blade.php (`resources/views/proje-calisma-alani/`)
-   **Görevi:** Dinamik proje takip ekranıdır.
-   **Haberleşme:** Controller'dan gelen `$steps` dizisini döngüye sokar. Eğer bir adım tamamlanmışsa (`is_completed`), veriyi `IaaProgressUpdate` modelinden çekerek ekrana yansıtır.

---

## 3. SİSTEMLER ARASI SENKRONİZASYON (Cross-System Sync)

### 3.1. PlanVisit.php (IAA - `app/Livewire/Project/`)
-   **Görevi:** Ziyaret planlandığında veya tamamlandığında tetiklenir.
-   **Haberleşme:** `Http::post()` ile Takvim projesindeki API'ye JSON verisi fırlatır.

### 3.2. CustomerSyncController.php (Takvim - `app/Http/Controllers/Api/`)
-   **Görevi:** IAA'dan gelen veriyi karşılar.
-   **Haberleşme:** `sync()` metodu ile gelen `type` bilgisini (customer, visit vb.) kontrol eder ve ilgili Takvim modeline (`CustomerVisit`) yazar.

---

## 4. ÖZET: BİR VERİ NASIL TAŞINIR?

1.  **Request:** Kullanıcı Blade üzerindeki bir butona tıklar (Örn: "Adımı Tamamla").
2.  **Controller:** İstek `ProjectWorkspaceController@storeStep`'e düşer.
3.  **Service/Model:** Controller ilgili servisi çağırır, servis `IaaProgressUpdate` modelini günceller.
4.  **Database:** Veri MySQL'e (`iaa_db`) yazılır.
5.  **View:** Controller kullanıcıyı `back()` veya `redirect()` ile sayfaya geri gönderir. Blade, güncel DB verisini Model üzerinden tekrar okur ve UI'yi yeniler.

**AI İçin İpucu:** Bir hata aldığında; önce Controller'daki `compact()` ile gönderilen veri ismine, sonra Model'deki ilişki ismine (Relations), en son Blade'deki `$variable->field` kullanımına bakmalısın.
