# Ürün Gereksinimleri Dokümanı (PRD) - Dinamik İş Akışı (Workflow) Yönetim Sistemi

Bu doküman, **Dinamik İş Akışı (Workflow) Yönetim Sistemi** projesinin işlevsel gereksinimlerini, teknik mimarisini, veri modellerini ve kullanıcı akışlarını tanımlamaktadır.

---

## 1. Proje Genel Bakışı

### 1.1 Amaç
Sistem, kurum içi süreçlerin dijital ortama aktarılmasını, dinamik form tasarımları ile veri toplanmasını ve görsel bir sürükle-bırak arayüzü ile süreç akışlarının (iş akış şemaları) tasarlanıp yürütülmesini amaçlamaktadır.

### 1.2 Ana Özellikler
- **Merkezi SSO Entegrasyonu**: Kullanıcı giriş ve çıkış işlemlerinin tek bir noktadan yönetilmesi.
- **Dinamik Form Şablonları**: Kod yazmadan form alanlarının tanımlanması, kategorize edilmesi ve revizyon takibi.
- **Görsel İş Akışı Tasarımcısı (Vue Flow)**: Sürükle-bırak arayüzüyle süreç düğümlerinin (node) ve geçişlerinin (edge) tasarlanması.
- **Süreç Motoru ve Takibi**: Tasarlanan akışların canlıya alınması, başlatılması ve görsel bir "Tracker" ile izlenmesi.
- **Görev Yönetimi**: Kullanıcıların kendilerine atanan adımları tamamlaması, onaylaması, reddetmesi veya geri alması.
- **Yönetim Paneli**: Kullanıcı, rol, departman ve direktörlük verilerinin merkezi sistemle senkronize edilmesi.

---

## 2. Kullanıcı Rolleri ve Yetkilendirme

Sistem, **Spatie Laravel Permission** paketi ile yetkilendirme altyapısına sahiptir. Kullanıcılar ve roller Merkezi SSO üzerinden senkronize edilir.

| Rol / Yetki Adı | Sistem Yetkisi (`Permission`) | Açıklama |
| :--- | :--- | :--- |
| **Superadmin** | `view_admin_panel` | Tüm sistem ayarlarını yönetir, senkronizasyonları tetikler. |
| **Form Tasarımcısı** | `create_forms` | Form kategorileri ve dinamik form şablonları oluşturur/düzenler. |
| **Süreç Yöneticisi** | `manage_workflows` | Görsel iş akış şablonlarını tasarlar, günceller ve yayınlar. |
| **Süreç Başlatıcı** | `start_processes` | Yetkisi dahilindeki iş akışlarını başlatır ve takip eder. |
| **Çalışan / Kullanıcı** | (Temel Yetki) | Kendisine atanan görevleri görüntüler, form doldurur ve tamamlar. |

---

## 3. Fonksiyonel Gereksinimler

### 3.1 Kimlik Doğrulama ve Entegrasyon
- **Merkezi SSO Girişi (`/login`)**: Sistem kendi içinde şifre barındırmaz. Kullanıcıyı merkezi SSO giriş ekranına yönlendirir.
- **Geri Dönüş ve Token Doğrulama (`/sso/login`)**: Giriş sonrası gelen token doğrulanarak kullanıcı oturumu başlatılır. Rol, departman ve direktörlük bilgileri güncellenir.
- **Güvenli Çıkış (`/logout`)**: Oturum sonlandırılır ve global çıkış için SSO çıkış sayfasına yönlendirilir.

### 3.2 Dinamik Form Şablonları (`FormTemplate`)
- **Tasarım Ekranı**: Form alanları (input, textarea, select, checkbox vb.) JSON formatında şablona kaydedilir.
- **Kategorizasyon**: Formlar, yönetilebilir kategoriler altında gruplanır.
- **Revizyon Kontrolü**: Yayınlanmış form şablonları üzerinde değişiklik yapıldığında yeni bir revizyon kaydı (`FormTemplateRevision`) oluşturulur.
- **Durum Yönetimi**: Şablonlar aktif/pasif hale getirilebilir.

### 3.3 İş Akışı Tasarımcısı (`Workflow`)
- **Vue Flow Entegrasyonu**: Akışlar tarayıcı üzerinde görsel olarak çizilir.
- **Düğümler (Nodes)**:
  - Başlangıç (Start Node)
  - Onay / Görev Adımı (Task/Approval Node): Bu adımda hangi form şablonunun doldurulacağı ve görevin kime atanacağı (Kullanıcı, Rol, Departman bazlı) seçilir.
  - Karar / Koşul Adımı (Decision Node): Form verilerine göre akışın yönlenmesini sağlar.
  - Bitiş (End Node)
- **Kenarlar (Edges)**: Adımlar arasındaki geçiş yönünü ve koşullarını tanımlar.
- **Erişim Kısıtlamaları**: İş akışını hangi departmanların, rollerin veya kullanıcıların başlatabileceği (`allowed_departments`, `allowed_roles`, `allowed_users`) belirlenir.

### 3.4 Süreç Motoru ve Yürütme (`ProcessInstance`)
- **Süreç Başlatma**: İlgili iş akışı seçilerek süreç tetiklenir. Başlangıç form verileri kaydedilir.
- **Adım İlerlemesi**: Aktif düğümdeki görev tamamlandığında, tanımlı kenarlara (edges) bakılarak bir sonraki düğüm tespit edilir ve yeni bir görev (`Task`) oluşturulur.
- **Süreç Takibi (Tracker)**: Sürecin hangi aşamada olduğu, geçmişte hangi adımlardan geçtiği Vue Flow grafiği üzerinde yeşil/mavi/kırmızı renk kodlarıyla görsel olarak gösterilir.

### 3.5 Görev Yönetimi (`Task`)
- **Görev Kutusu**: Kullanıcılar kendilerine atanmış aktif görevleri listeler.
- **Görev Detayı ve Tamamlama**: Kullanıcı ilgili form verilerini doldurur, kararını verir (Onay/Red/Revizyon) ve kaydeder.
- **Geri Alma (Undo)**: Hatalı işlem durumunda, belirli kurallar çerçevesinde görev geri alınabilir.

### 3.6 Yönetim Paneli
- **Departman ve Direktörlük Senkronizasyonu**: Merkezi veritabanından departman ve direktörlük listelerinin çekilmesi ve güncellenmesi.
- **Kullanıcı Senkronizasyonu**: Merkezi veritabanındaki kullanıcıların sisteme aktarılması ve rol tanımlamalarının yapılması.
- **Sistem Ayarları**: Genel çalışma parametrelerinin (örn. loglama tercihleri, bildirim ayarları) yönetilmesi.

---

## 4. Teknik Mimari ve Veri Modeli

### 4.1 Teknoloji Yığını
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3, Inertia.js 2.0, TailwindCSS
- **Veri Görselleştirme**: `@vue-flow/core`
- **Veritabanı**: MySQL 8.0 / MariaDB

### 4.2 Veri Modelleri ve Alanları

#### `Workflow` (İş Akış Şablonu)
- `id` (Primary Key)
- `name` (string) - Akış adı
- `description` (text, nullable) - Açıklama
- `category` (array) - Kategoriler
- `allowed_departments` (array, nullable) - Başlatabilecek departmanlar
- `allowed_roles` (array, nullable) - Başlatabilecek roller
- `allowed_users` (array, nullable) - Başlatabilecek kullanıcılar
- `form_template_id` (foreignId) - İlişkili form şablonu
- `nodes` (json/array) - Vue Flow düğüm verileri (koordinatlar, atamalar, adımlar)
- `edges` (json/array) - Vue Flow kenar verileri (geçiş koşulları, bağlantılar)
- `status` (string) - Aktif, Pasif, Taslak
- `version` (integer) - Versiyon numarası
- `created_by` (foreignId) - Oluşturan kullanıcı

#### `ProcessInstance` (Çalışan Süreç Örneği)
- `id` (Primary Key)
- `workflow_id` (foreignId) - İlgili iş akışı şablonu
- `current_node_id` (string) - Mevcut aktif düğümün benzersiz ID'si
- `status` (string) - Çalışıyor (running), Tamamlandı (completed), İptal Edildi (cancelled)
- `started_by` (foreignId) - Süreci başlatan kullanıcı
- `data` (json/array) - Süreç boyunca formlardan toplanan kümülatif veri havuzu

#### `Task` (Süreç Görev Adımı)
- `id` (Primary Key)
- `process_instance_id` (foreignId) - İlişkili süreç örneği
- `node_id` (string) - Akış tasarımındaki ilgili düğüm ID'si
- `assigned_to` (foreignId, nullable) - Atanan spesifik kullanıcı
- `assigned_role` (string, nullable) - Atanan rol adı
- `assigned_role_id` (integer, nullable) - Atanan rol ID'si
- `type` (string) - Onay, Form Doldurma, Bilgilendirme
- `status` (string) - Bekliyor (pending), Tamamlandı (completed), Reddedildi (rejected)
- `comment` (text, nullable) - İşlem açıklaması / Red nedeni
- `due_date` (datetime, nullable) - Son tamamlanma tarihi
- `completed_at` (datetime, nullable) - Tamamlanma tarihi

---

## 5. Kritik Geliştirme Kuralları ve Performans

- **N+1 Sorgu Engelleme**: Veritabanı ilişkileri yüklenirken mutlaka `with()` eager loading kullanılmalıdır.
- **Sayfalama (Pagination)**: Büyük veri listelerinde sunucu taraflı sayfalama zorunludur.
- **Ortam Bağımsız URL'ler**: `/storage` veya `/admin` gibi statik adresler yerine Laravel `route()`, `url()`, `asset()` yardımcıları kullanılmalıdır.
- **Loglama**: Kullanıcıların yaptığı tüm hassas işlemler (süreç başlatma, görev tamamlama, senkronizasyon vb.) veritabanına loglanmalıdır.
