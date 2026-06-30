# KÖKSAN IAA & TAKVİM SİSTEM MİMARİSİ ANALİZİ (V2 - TAM KAPSAM)

Bu döküman, projenin tüm modüllerini ve teknik katmanlarını başka bir yapay zekaya (LLM) eksiksiz aktarmak üzere hazırlanmıştır.

---

## 1. SİSTEMİN KALBİ: IAA (İyileştirme ve Aksiyon)

Proje sadece bir takip aracı değil, kurumsal hafızayı yöneten çok yönlü bir ERP-CRM hibritidir.

### 1.1. Modüller ve İşlevleri
1.  **Öneri Sistemi:** Çalışanların ve kayıtsız kullanıcıların (Misafir Formu) iyileştirme fikirlerini toplar. `oneri` alanı doluysa sistem bunu bir **IAA Projesi** olarak ele alır.
2.  **Şikayet Yönetimi:** `MusteriSikayeti` modeli üzerinden dış kaynaklı sorunları toplar. Eğer çözüm bir proje gerektiriyorsa `iaa_id` ile bir IAA projesine dönüştürülür.
3.  **Disiplin Süreçleri (`DisciplinaryCase`):** Personel ihlallerini, savunma toplama, kurul oylaması ve ceza puanlama mekanizmalarını yönetir.
4.  **Arabuluculuk (`ArabuluculukCase`):** Hukuki süreçleri, yönetim onaylarını ve ödeme takiplerini maskelenmiş statüler (`publicStatus`) ile yönetir.

---

## 2. TEKNİK MOTORLAR VE OTOMASYON (RPA)

### 2.1. Puanlama Sistemi (`SyncTeamScores.php`)
Sistem, projelerin karmaşıklığına ve tamamlanma durumuna göre dinamik puanlar üretir. 
- **Takım Puanı:** Atanmış ve "Tamamlandı" statüsündeki `Iaa` puanlarının toplamıdır.
- **Personel Puanı:** `KullaniciPuanService` üzerinden bireysel katkılara göre hesaplanır ve `User` tablosundaki `toplam_puan` alanına senkronize edilir.

### 2.2. Otomatik Raporlama (`RaporlariKontrolEt.php`)
Cron tab tabanlı çalışan bu motor, `RaporKurali` modeline göre:
- Günlük/Haftalık/Aylık periyotlarda veri toplar (`RaporVeriServisi`).
- Dinamik alıcı listelerine (Rol bazlı veya Mail listesi) otomatik rapor gönderir.
- Gönderimleri loglar ve mükerrer raporu engeller.

### 2.3. Dinamik Ayarlar (`Setting.php`)
Sistemdeki birçok eşik değer ve statü kuralı veritabanındaki `settings` tablosundan yönetilir. Kod içinde hardcoded değer yerine daima `Setting::get('key')` kullanılır.

---

## 3. ERİŞİM VE YETKİ KATMANLARI (INTELLIGENCE)

### 3.1. Kayıtsız Kullanıcı & Müşteri Girişi
- **Misafir Formu:** Kullanıcı girişi yapmadan `guest_name`, `guest_email` ile öneri/şikayet bırakabilir. Sistem `takip_token` ile bu kullanıcıya kendi kaydını izleme yetkisi verir.
- **Müşteri Girişi:** CRM tarafındaki yetkililer için özel şifreleme (`guest_password_hash`) ile sadece kendi şikayetlerini görebildikleri bir portal sunulur.

### 3.2. Rol Hiyerarşisi (Role Intelligence)
Yetki kontrolleri Model seviyesindeki akıllı metodlarla yapılır:
- **Direktör:** `bolumler.director_id` ile bağlı tüm bölümleri görür.
- **Bölüm Lideri (Müdür):** Kendi bölümünün tüm hareketlerini ve bildirimlerini yönetir.
- **SQUAD (Proje Ekibi):** `iaa_user` pivot tablosu üzerinden projeye özel yetkilendirilen ekiptir.

---

## 4. SENKRONİZASYON VE VERİ AKIŞI

IAA (8000) projesindeki her kritik değişiklik (Ziyaret Planı, Dönüş Tarihi, Dosya) Takvim (8001) projesine API üzerinden anlık akar.
- **Mekanizma:** `CustomerSyncController` (Takvim) $\leftrightarrow$ `PlanVisit.php` (IAA).
- **Zorunlu Kural:** Dosya yolları daima `storage/modul/user/id/tarih_random.ext` formatında senkronize edilir.

---

## 5. DEVELOPER & AI İÇİN KRİTİK NOTLAR (RULES)

- **Geliştirme Kuralı:** Asla `// ...` veya "sen tamamla" deme. Kodu tam yaz.
- **DB Erişimi:** Tinker komutlarını kendin çalıştır ve sonucu paylaş.
- **Geri Uyum:** Mevcut fonksiyonları silme, sadece üzerine ekle.
- **Sorgu:** N+1'den kaçın, daima `->with()` kullan.

Bu sistem, teknik bir araçtan ziyade Köksan'ın tüm operasyonel, idari ve saha süreçlerini birbirine bağlayan devasa bir ekosistemdir.
