# RULES KARŞILAŞTIRMA SONUÇ RAPORU

Mevcut RULES.md (256 satır) vs Önerilen Rules vs Final rules1.md karşılaştırması.
Kaynak: `master_file_flow_dictionary.md` derin sistem analizi ile doğrulanmış gerçek kod bulguları.

---

## 1. EKLENEN MADDELER (rules1.md'ye girdi)

| # | Öncelik | Madde | Kaynak | Neden Eklendi |
|---|---|---|---|---|
| 1 | 🔴 Yüksek | iaas.oneri kırılganlık uyarısı — NULL vs '' farkı, if/else zorunlu | Önerilen Rules + master analiz P.10 | Mevcut rules'da sadece "dolu/boş" vardı, NULL ayrımı kritik |
| 2 | 🔴 Yüksek | Şişmiş controller listesi (5 dosya, KB'larıyla) | Önerilen Rules + master analiz P.11 | Mevcut rules'da genel "şişirme yasak" vardı, somut dosya isimleri yok |
| 3 | 🔴 Yüksek | ProjectWorkspaceController with() uyarısı | Önerilen Rules | Gerçek N+1 riski var, spesifik dosya uyarısı gerekli |
| 4 | 🔴 Yüksek | SyncAllToTakvim chunk(100) uyarısı | Önerilen Rules + master analiz P.4 | Customer::all() bellek taşırıyor |
| 5 | 🔴 Yüksek | Queue zorunluluğu — yeni bildirimde ShouldQueue şart | Önerilen Rules + master analiz P.1 | Mevcut rules'da Queue hakkında HİÇ kural yoktu |
| 6 | 🟡 Orta | Gerçek rol listesi (19 rol, ID'leriyle) | Önerilen Rules | AI'ın yetki yazarken doğru rol kullanması için şart |
| 7 | 🟡 Orta | Direktör rolü (ID=8) vs bolumler.director_id farkı | Önerilen Rules | İkisini karıştırmak ciddi yetki hatası üretir |
| 8 | 🟡 Orta | Settings tablosu key-value listesi (18 key) | Önerilen Rules + master analiz P.6 | AI'ın yeni key eklerken mevcut yapıyı bozmaması için |
| 9 | 🟡 Orta | SoftDeletes olan modeller listesi (5 model) | Önerilen Rules | withTrashed() farkındalığı kritik |
| 10 | 🟡 Orta | Observer-Model bağlantı tablosu (6 kayıt) | Master analiz P.2 | Observer'a dokunurken hangi model etkilendiği bilinmeli |
| 11 | 🟡 Orta | Observer sonsuz döngü koruması (is_syncing) | Önerilen Rules + master analiz P.2 | Bu flag silinirse sonsuz döngü oluşur |
| 12 | 🟡 Orta | API endpoint güvenlik uyarısı | Master analiz P.3 | CustomerSyncController middleware'siz açık |
| 13 | 🟡 Orta | Rate limiting uyarısı — yeni public endpoint'te throttle ekle | Önerilen Rules | Spam koruması için |
| 14 | 🟡 Orta | Mevcut servis sınıfları listesi (16 servis) | Master analiz P.7 | "Önce bak sonra oluştur" prensibi |
| 15 | 🟡 Orta | Queue'lu bildirim/mail tam listesi (13+4) | Master analiz P.1 | Yeni eklerken referans |
| 16 | 🟡 Orta | Deprecated dosya uyarısı | Önerilen Rules | SikayetCozumGorevlerim(silinecek).php |
| 17 | 🟡 Orta | Workflow yapısı açıklaması | Master analiz | AI'ın iş akışı mantığını anlaması için |
| 18 | 🟡 Orta | AI entegrasyonu açıklaması | Master analiz P.5 | GeminiService genişletme kuralı |
| 19 | 🟡 Orta | iaas tablosu ek alanlar (hatali_bildirim_*, talep_direktor_*) | Önerilen Rules | Tablo yapısı farkındalığı |
| 20 | 🟡 Orta | Kritik tablo ilişkileri (users.is_mavi_yaka, takimlar.tur vb.) | Önerilen Rules | FK bilgi zenginliği |
| 21 | 🟡 Orta | DATABASE kuralı — Migration + Foreign key zorunlu | Önerilen Rules | Mevcut rules'da yoktu |
| 22 | 🟡 Orta | AUTH kuralı — Policy/Gate + mevcut policy listesi | Önerilen Rules | Mevcut rules'da yoktu |
| 23 | 🟡 Orta | MVC tanımı — Controller yönlendirir, Service iş yapar, Model veri | Önerilen Rules | Daha açık rol tanımı |

---

## 2. EKLENMEYEN MADDELER (Bilinçli olarak çıkarıldı)

| # | Madde | Neden Eklenmedi |
|---|---|---|
| 1 | CACHE → Cache::remember() kullan | Projede aktif cache kullanımı sınırlı, gereksiz kural olur |
| 2 | Takvim web.php route uyarısı | Bu IAA RULES dosyası, Takvim projesinin route bilgisi burada kafa karıştırır |
| 3 | Takvim'e ait crm.security middleware notu | IAA projesinde bu middleware yok, karışıklık yaratır |
| 4 | ROUTE → RESTful yapı | Çok genel, projeye spesifik bir şey eklemiyor |
| 5 | STORAGE → Storage::put() kullan | Zaten 4. maddedeki FILE UPLOAD kuralında kapsanıyor |

---

## 3. MEVCUT RULES.md'DEN KORUNAN MADDELER (Değiştirilmeden aktarıldı)

| # | Madde |
|---|---|
| 1 | Environment & Deployment (Local vs Production) |
| 2 | Environment Safe URL Rule (asset/url/route zorunlu) |
| 3 | Responsive Design |
| 4 | Layout Rule (overflow yasak) |
| 5 | File Upload Structure (tam format) |
| 6 | User Profile Linking |
| 7 | Customer Profile Linking |
| 8 | Bildirim şablonları (Direktör + Bölüm Lideri) |
| 9 | Filtering kuralları |
| 10 | Logging kuralları (Modal boyut uyarısı dahil) |
| 11 | Command Rule (kendin çalıştır) |
| 12 | Backward Compatibility |
| 13 | AI Behavior Rules (10 madde) |
| 14 | Strict Mode (eksik kod yasak) |

---

## 4. GENİŞLETİLEN MADDELER (Mevcut vardı, detay eklendi)

| # | Madde | Eklenen Detay |
|---|---|---|
| 1 | Director Permissions | `bolumler.director_id → users.id (Rol ID=8)` doğrulaması eklendi |
| 2 | Role Intelligence Rule | `isDirector()` ve `isDepartmentLeader()` için Rol ID referansı eklendi |
| 3 | Complaint vs IAA | Kırılganlık uyarısı + iaas tablosu ek alanları eklendi |
| 4 | Department Leader | `Rol ID 2 "Bölüm Lideri"` referansı eklendi |
| 5 | Query Optimization | ProjectWorkspaceController ve SyncAllToTakvim spesifik uyarıları eklendi |
| 6 | Controller şişirme yasak | 5 somut dosya ismi ve KB değerleri eklendi |
| 7 | Model kuralları | SoftDeletes olan 5 model listesi eklendi |

---

## 5. SAYISAL KARŞILAŞTIRMA

| Metrik | Mevcut RULES.md | Önerilen Rules | Final rules1.md |
|---|---|---|---|
| Toplam satır | 256 | ~450 | ~380 |
| Genel kurallar | 13 madde | 13 madde | 13 madde (değişmedi) |
| Projeye özel bağlam | YOK | ✅ Detaylı | ✅ Detaylı |
| Rol listesi | YOK | 19 rol | 19 rol |
| Settings key listesi | YOK | 18 key + değer | 18 key + değer |
| Servis listesi | YOK | 16 servis | 16 servis |
| Observer tablosu | YOK | 6 observer | 6 observer |
| Queue durumu | YOK | 13+4 liste | 13+4 liste |
| SoftDeletes listesi | YOK | 5 model | 5 model |
| Şişmiş dosya uyarısı | Genel | 5 dosya + KB | 5 dosya + KB |
| Laravel Best Practices | 4 madde | 12 madde | 10 madde |
| Güvenlik notları | YOK | 3 uyarı | 3 uyarı |
