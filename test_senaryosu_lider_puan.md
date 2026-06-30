# IAA Projesi: Lider Değişikliği ve Puanlama Test Senaryosu

Bu doküman, sistemde yapılan son geliştirmelerin (Lider Senkronizasyonu ve Puan Kaynağı Düzeltmeleri) doğruluğunu test etmek için kullanılacak referans senaryolarını içerir.

---

## 📅 Rota Bilgileri
- **Sistem Sağlık ve Kalibrasyon Paneli:** `/admin/sistem-sagligi`
- **Çözüm Takımları Listesi:** `/admin/cozum-takimlari`

---

## 🧪 Senaryo 1: Lider Değişikliği ve Squad Senkronizasyonu

**Amaç:** Takım lideri değiştiğinde, o takıma atanmış ve henüz tamamlanmamış projelerin ekip listesinde (Squad) liderin otomatik güncellendiğini doğrulamak.

### Adımlar:
1. **Hazırlık:** 
   - Mevcut bir "Şikayet" türündeki takımı seçin (veya yeni bir tane oluşturun).
   - Bu takıma atanmış, durumu **"Devam Ediyor"** veya **"Bölüm Onayı Bekliyor"** olan en az bir proje olduğundan emin olun.
2. **Değişiklik:** 
   - Takımı düzenle ekranına gidin.
   - Mevcut lideri başka bir **"Müşteri Şikayeti Çözüm Lideri"** ile değiştirin ve kaydedin.
3. **Doğrulama:**
   - İlgili projenin **"Proje Çalışma Alanı"** sayfasına gidin.
   - Sağ taraftaki **"Proje Ekibi (Squad)"** listesini kontrol edin.
   - **Sonuç:** Eski liderin listeden çıkarıldığını ve yeni liderin **"Lider"** rolüyle listeye eklendiğini görmelisiniz.

---

## 🧪 Senaryo 2: Şikayet Projesi Tamamlama ve Puan Doğruluğu

**Amaç:** Bir şikayet projesi tamamlandığında, puanın hatalı bir şekilde (örn: 100) değil, şikayetin kendi puanından (örn: 10) alındığını doğrulamak.

### Adımlar:
1. **Hazırlık:**
   - Bir müşteri şikayeti üzerinden proje başlatın.
   - Şikayet puanının kaç olduğunu not edin (Örn: 2 giriş puanı + 10 çözüm puanı = 12 puan).
2. **İşlem:**
   - Projeyi "İadesiz" veya "İadeli" olarak tamamlayın.
   - Superadmin veya Direktör ile projeyi onaylayın.
3. **Doğrulama:**
   - Projenin `iaas` tablosundaki `puan` kolonuna bakın.
   - `iaa_user` pivot tablosundaki `kazanilan_puan` kolonuna bakın.
   - **Sonuç:** Her iki alanda da şikayet puanının (Örn: 12) yazıldığını görmelisiniz. Artık 100 puan gibi hatalı değerler gelmemelidir.

---

## 🛠️ Veri Kalibrasyonu (Geçmişi Düzeltme)

Eğer geçmişte hatalı puanlanmış (100 puan verilmiş şikayetler gibi) kayıtlar varsa:

1. **Sistem Sağlık Paneli**'ne gidin.
2. **"Puan Kalibrasyonu"** sekmesine tıklayın.
3. **"Tüm Kullanıcı Puanlarını Yeniden Hesapla"** veya **"Tam Senkronizasyon"** butonuna basın.
4. Bu işlem; tüm projeleri tarayacak, şikayet projelerini tespit edecek ve puanları doğru kaynaktan (musteri_puan) çekerek herkesin puanını (Kullanıcı, Takım ve Bölüm bazında) düzeltecektir.

---

> [!NOTE]
> Bu senaryolar `app/Http/Controllers/Admin/CozumTakimiController.php` ve `app/Services/ProjectWorkspace/ProjeTamamlamaService.php` dosyalarındaki mantık değişikliklerini doğrular.
