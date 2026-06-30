# 🏆 Puan Sistemi ve Veri Bütünlüğü Kuralları (MANDATORY)

Bu dosya, İAA ve Müşteri Şikayeti projelerinin nasıl ayırt edileceğini ve puanlama kurallarını tanımlar.

---

## 1. Proje Türü Ayrımı (Pattern Matching)
Sistemde projeler `iaas.oneri` kolonundaki metin desenine göre ayırt edilir. Bu ayrım puan kaynağını belirler:

| Proje Türü | `iaas.oneri` İçeriği | Puan Kaynağı |
| :--- | :--- | :--- |
| **Müşteri Şikayeti** | `"Müşteri şikayetinden (ID: {id}) dönüştürüldü."` | `musteri_sikayetleri.musteri_puan` |
| **Saf İAA** | Bu kalıbın dışındaki her türlü metin (Örn: "İAA TEST Çözüm Öneriniz") | `iaas.puan` |

> [!IMPORTANT]
> Kod içinde ayrım yapılırken `str_contains($iaa->oneri, 'Müşteri şikayetinden')` veya Regex kontrolü kullanılmalıdır. `empty()` kontrolü yanıltıcıdır çünkü bu kolon her zaman doludur.

---

## 2. Şikayetten Projeye Dönüşüm (Triyaj) Kuralları
Şikayet, triyaj sırasında projeye dönüştürüldüğünde şu standartlara uyulmalıdır:
1.  **Standart Metin:** `oneri` alanına mutlaka `"Müşteri şikayetinden (ID: {$sikayet->id}) dönüştürüldü."` yazılmalıdır.
2.  **Veri Taşıma:** Kullanıcının asıl çözüm önerisi metni `iaas.oneri` alanına değil, `iaas.yonetici_notu` veya ilgili açıklama alanlarına kaydedilmelidir.
3.  **Puan Transferi:** Şikayetin puanı (`musteri_puan`), projenin `puan` alanına ve pivot tablodaki `kazanilan_puan` alanlarına tam senkronize şekilde aktarılmalıdır.

---

## 3. Personel Ayrılma ve Puan Hak Etme
Personelin bir projeden puan alabilmesi için projenin **tamamlanma (onay) anında** görevde olması şarttır.

### **3.1. Ayrılma Tarihi (Effective Departure Date)**
`DepartureDate = min(termination_date, deleted_at)`
*(Hangisi daha erkense o tarih baz alınır. termination_date gün sonu olarak işleme alınır.)*

### **3.2. Puan Kısıtı**
- Eğer `Proje.tamamlanma_tarihi > DepartureDate` ise, o personel o projeden **0 PUAN** alır.
- Bu kontrol `KullaniciPuanService` ve `DataCalibration` içinde merkezi olarak uygulanır.

---

## 4. Puan Senkronizasyonu (3'lü Zincir)
Bir puan değişikliği yapıldığında şu üç nokta her zaman güncellenmelidir:
1.  **iaas.puan:** Proje ana tablosu.
2.  **iaa_user.kazanilan_puan:** Pivot tablo (Her üyenin kendi hak ettiği puan).
3.  **users.toplam_puan:** Kullanıcı bazlı toplam puan önbelleği (Cache).

---

## 5. Veri Kalibrasyonu ve Onarım
Sistemdeki puan tutarsızlıklarını gidermek için `DataCalibration` aracı kullanılır. Bu araç:
- `oneri` kolonundaki deseni tarayarak projenin şikayet mi yoksa İAA mı olduğunu doğru tespit eder.
- Yanlış kaynaktan beslenen puanları (Örn: Şikayet projesine 100 puan verilmesi gibi) düzeltir.
- İşten ayrılanların haksız puanlarını sıfırlar ve önbellekleri tazeler.
