# GLOBAL PROJECT RULES

YOU MUST ALWAYS FOLLOW THESE RULES. NEVER IGNORE THEM.

## 1. ENVIRONMENT & DEPLOYMENT
- Proje localde geliştirilir, periyodik olarak canlıya alınır.
- Local ortam: root dizin (/)
- Production: /iaa alt klasöründe çalışır (https://kys.koksan.com/iaa)

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
  bolumler tablosundaki director_id üzerinden belirlenir

- Direktör:
  - sorumlu olduğu bölümlere ait HER şeyi görür
  - tüm hareketlerden haberdar olur

### BİLDİRİMLER

Zil + Mail (mailde tarih olacak):

1. "Direktörlüğünüze bağlı {bolum} bölümüne {baslik} başlıklı yeni bir şikayet eklenmiştir."
2. "Direktörlüğünüze bağlı {bolum} bölümü kullanıcısı olan {kullanici} adına {olusturan} tarafından yeni bir tutanak oluşturulmuştur."
3. "Direktörlüğünüze bağlı {bolum} bölümü kullanıcısı olan {kullanici}, {proje} projesinde ekip üyesi olmuştur."

---

## ROLE INTELLIGENCE RULE

- Yetki kontrolleri controller içinde if ile yapılmaz

- User modeline eklenmeli:
  - isDirector()
  - isDepartmentLeader()
  - getResponsibleDepartments()

- Amaç:
  - temiz kod
  - tekrar azaltma
  - doğru yetkilendirme

---

## 8. COMPLAINT vs IAA

- Tüm kayıtlar iaas tablosunda

- Ayrım:
  - oneri dolu → IAA
  - oneri boş → ŞİKAYET

- Sistem buna göre davranmalı

---

## 9. DEPARTMENT LEADER

- Bölüm lideri = müdür

- Şunları görmeli:
  - kendi bölümüne ait tüm hareketler
  - tüm bildirimler

### BİLDİRİMLER

1. "Lideri olduğunuz {bolum} bölümüne {baslik} başlıklı yeni bir şikayet eklenmiştir."
2. "Lideri olduğunuz {bolum} bölümü kullanıcısı olan {kullanici} adına {olusturan} tarafından yeni bir tutanak oluşturulmuştur."
3. "Lideri olduğunuz {bolum} bölümü kullanıcısı olan {kullanici}, {proje} projesinde görev almaya başlamıştır."

- Mailde tarih zorunlu

---

## 10. FILTERING

- Tüm listelerde:
  - tarih filtresi
  - başlık filtresi

- Default: tüm zamanlar

---

## QUERY OPTIMIZATION RULE

- N+1 YASAK
- Her zaman ->with() kullan

- Büyük veri:
  - pagination zorunlu

- Filtreleme:
  - DB seviyesinde yapılmalı

---

## 11. LOGGING

- Her işlem loglanmalı:
  - user_id
  - işlem
  - entity_id
  - tarih/saat

- UI:
  - son 10 kayıt görünmeli
  - "Tümünü Gör" butonu olmalı
  - "Tümünü Gör" → ayrı sayfa VEYA küçük scroll modal açılmalı
  - ⚠️ Modal küçük ve yönetilebilir boyutta olmalı, tam ekran/kocaman modal YASAK
  - Modal içinde scroll-bar ile kaydırma sağlanmalı

---

## 12. COMMAND RULE

- "php artisan tinker" veya benzeri kontrol/debug komutları istendiğinde:
  → Kullanıcıya "sen çalıştır" diye BIRAKILMAZ
  → Komutu kendin çalıştır, sonucu direkt paylaş
  → "Run et, sana bırakıyorum" demek YASAK

---

## 13. BACKWARD COMPATIBILITY

- Mevcut kod:
  ❌ silinmez
  ❌ özellik kaldırılmaz

- Sadece ekleme yapılır
- Aksini kullanıcı açıkça belirtmedikçe hiçbir mevcut işlevselliğe dokunulmaz

---

# AI BEHAVIOR RULES (AI Kısıtlamaları)

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

# LARAVEL BEST PRACTICES

## CONTROLLER
- Şişirme → YASAK

## SERVICE
- Business logic burada

## VALIDATION
- FormRequest kullan

## MODEL
- fillable zorunlu
- İlişkiler tanımlı olmalı

---

# STRICT MODE (Kesin Uygulama Modu)

- Eksik kod YASAK
- "// ..." YASAK
- "sen tamamla" YASAK
- "sana bırakıyorum" YASAK

- Her feature:
  → tam yazılmalı
  → çalışır halde teslim edilmeli
