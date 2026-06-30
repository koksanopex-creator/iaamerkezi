# KÖKSAN IAA & TAKVİM: NİHAİ 360° TEKNİK SİSTEM RAPORU

Bu döküman projedeki **istisnasız tüm** dosya, tablo ve teknik yapıları içermektedir.
Toplam: **49 Controller, 55 Model, 44 Notification, 11 Mail, 24 Livewire, 16 Service, 6 Observer, 2 Policy, 3 Artisan Command, 2 Trait, 86+ Blade dosyası, 68 Veritabanı Tablosu**.

---
---

# BÖLÜM B: SİSTEM MİMARİSİ VE TEKNİK ALTYAPI

## B.1. Request Flow (İstek Akışı)
`web.php (Route)` → `Middleware (Security)` → `Controller (Request)` → `Service (Business Logic)` → `Policy (Auth)` → `Model (DB)` → `Notification (Alert)` → `Blade/Livewire (Response)`

## B.2. IAA - Takvim Entegrasyonu
- **IAA** (Port 8000) ve **Takvim** (Port 8001) iki ayrı Laravel projesidir.
- **Veritabanları:** `iaa_db` (Ana Operasyon) ve `takvim` (CRM/Saha).
- **API Bridge:** `PlanVisit.php` (IAA) → `Http::post()` → `CustomerSyncController.php` (Takvim).

## B.3. Puanlama Sistemi
- `SyncTeamScores.php` artisan komutu: Takım ve Personel puanlarını hesaplar.
- `KullaniciPuanService`: Bireysel katkılara göre hesaplar, `User.toplam_puan`'a yazar.

## B.4. Otomatik Raporlama
- `RaporlariKontrolEt.php` artisan komutu: Cron bazlı, `RaporKurali` modeline göre periyodik rapor gönderir.
- `RaporVeriServisi`: Ham veri toplar, `OtomatikYoneticiRaporu` mail şablonu ile gönderir.

## B.5. Dinamik Ayarlar
- `Setting.php` modeli: Key-Value bazlı. Kodda `Setting::get('key')` ile çekilir.

---
---

# BÖLÜM C: DOSYA AKIŞ SÖZLÜĞÜ (Controller → Model → View Akışı)

## C.1. Şikayet Akışı
- **SikayetController@store:** Form → Validasyon → `MusteriSikayeti` oluştur → `Setting` den standart puan çek → `ComplaintNotificationTrait` ile bildirim.
- **MusteriSikayeti model:** `$fillable` ile veri sınırlar. `teknikDetaylar()`, `dosyalar()`, `iaa()` ilişkileri.
- **Blade (create/show):** Controller'dan `compact('kategoriler','sikayet')` ile veri gelir. `$sikayet->musteri_durum` ile koşullu UI.

## C.2. IAA Proje Motoru
- **ProjectWorkspaceController@show:** `Iaa` modeli + tüm ilişkili datayı (SQUAD, Adımlar, Ziyaretler) yükler.
- **Service Yapısı:** `ProjeAdimIslemleriService`, `ProjeTamamlamaService` gibi servisler çağırılır.
- **Blade (proje-calisma-alani/show):** Controller'dan gelen `$steps` dizisini döngüye sokar. Tamamlanmış adımlar `IaaProgressUpdate` modelinden ekrana yansıtılır.

## C.3. Sistemler Arası Senkronizasyon
- **PlanVisit.php (IAA Livewire):** `Http::post()` ile Takvim API'ye JSON fırlatır.
- **CustomerSyncController.php (Takvim API):** Gelen `type` bilgisini kontrol eder, `CustomerVisit` modeline yazar.

---
---

# BÖLÜM D: CONTROLLER KATMANI (49 DOSYA - TAM LİSTE)

## D.1. Admin Controllerlar (`app/Http/Controllers/Admin/`) - 27 Dosya
1. **`ArabulucuController.php`** — Arabulucu kişilerin listelenmesi, eklenmesi, aktif/pasif statüleri.
2. **`ArabuluculukController.php`** — Hukuki vakaların tüm yaşam döngüsü, ödeme onayları, dosya kapatma.
3. **`ArabuluculukTanimController.php`** — Anlaşma maddeleri, tutarlar, hukuki sabitlerin tanımlanması.
4. **`BolumController.php`** — Fabrika bölümleri (Kapak, Preform vb.), makine/hammadde eşleştirmeleri.
5. **`BolumKaliteYoneticisiController.php`** — Bölümlere özel kalite denetçisi atama.
6. **`BolumKategorisiController.php`** — Bölüm ana gruplandırmaları (Gıda, Lojistik vb.).
7. **`CozumTakimiController.php`** — Şikayet çözüm ekiplerinin yönetimi.
8. **`CustomerProfileController.php`** — Müşteri firma profili, yetkili yönetimi, geçmiş şikayet analizi.
9. **`DirectorAssignmentController.php`** — Direktör-Bölüm onay yetkisi haritalama.
10. **`DisciplinaryController.php`** — Disiplin tutanakları, savunma toplama, kurul karar mekanizması.
11. **`DisciplinarySettingsController.php`** — Disiplin suçları, ceza puanları, katsayılar, ceza skalası.
12. **`DisiplinSorumlusuController.php`** — Bölüm bazlı disiplin takipçisi yetkilendirme.
13. **`ExecutiveReportController.php`** — Üst yönetim raporları Excel/PDF export.
14. **`ExternalLawyerController.php`** — Dış avukat tanımlama ve vaka atama.
15. **`IaaWorkflowController.php`** — Dinamik iş akışı şablonları (8D, Kaizen adımları) kurgusu.
16. **`IaaYonetimController.php`** — Tüm IAA projelerinin merkez üssü; Onay/Red/Puan/Arşiv (67KB).
17. **`LoginLogController.php`** — Giriş-çıkış hareketleri güvenli izleme.
18. **`MachineLogController.php`** — Makine işlem ve arıza geçmişi loglama.
19. **`MaviYakaController.php`** — Mavi yaka personel yönetimi ve yetkilendirme.
20. **`MusteriLogController.php`** — Müşteri kartı değişiklik tarihçesi.
21. **`RaporController.php`** — Şikayet analizi, IAA performansı, operasyonel raporlar.
22. **`ReportController.php`** — Periyodik otomatik rapor şablonları yönetimi.
23. **`SikayetController.php`** — Şikayet oluşturma, teknik detay, dosya yönetimi, çözüm takibi (35KB).
24. **`SikayetKategoriController.php`** — Şikayet türleri ve alt kategorileri kırılımları.
25. **`SistemAyarController.php`** — Global değişkenler, logo, puan eşikleri, parametreler.
26. **`TakimYonetimController.php`** — Personel takımları kurulması, lider ataması, proje görevlendirme.
27. **`UserController.php`** — Personel yönetimi, rol atama (Spatie), e-posta doğrulama, hesap aktifliği.

## D.2. Kök Controllerlar (`app/Http/Controllers/`) - 12 Dosya
1. **`Controller.php`** — Temel Laravel Controller.
2. **`DashboardController.php`** — Ana pano verileri, merkezi widget yönetimi (26KB).
3. **`GuestIaaController.php`** — Kayıtsız kullanıcı (Misafir) öneri portal yönetimi.
4. **`IaaController.php`** — Operasyonel proje listeleme ve aksiyonlar (25KB).
5. **`NotificationController.php`** — Bildirim silme/okundu işlemleri.
6. **`ProfileController.php`** — Kullanıcı hesap ayarları ve puan takibi (20KB).
7. **`ProjectWorkspaceController.php`** — SQUAD çalışma alanı, servis entegrasyonu (14KB).
8. **`PublicSikayetController.php`** — Dış portal şikayet yönetimi, token tabanlı erişim (31KB).
9. **`TakimController.php`** — Kullanıcı seviyesi takım davetleri ve üyelik (23KB).
10. **`UserDirectoryController.php`** — Şirket içi personel rehberi.
11. **`VisitFileUploadController.php`** — Ziyaret dökümanlarının asenkron yüklenmesi.
12. **`WelcomeController.php`** — Karşılama (Landing) ekranı.

## D.3. Auth Controllerlar (`app/Http/Controllers/Auth/`) - 9 Dosya
1. **`AuthenticatedSessionController.php`** — Login/Logout.
2. **`ConfirmablePasswordController.php`** — Kritik işlem öncesi şifre doğrulama.
3. **`EmailVerificationNotificationController.php`** — Doğrulama maili yeniden gönderme.
4. **`EmailVerificationPromptController.php`** — E-posta doğrulamaya zorlama.
5. **`NewPasswordController.php`** — Şifre sıfırlama sonrası yeni şifre.
6. **`PasswordController.php`** — Giriş yapmış kullanıcı şifre güncelleme.
7. **`PasswordResetLinkController.php`** — "Şifremi Unuttum" maili tetikleme.
8. **`RegisteredUserController.php`** — Yeni kullanıcı kayıt süreci.
9. **`VerifyEmailController.php`** — Mail'deki link ile hesap onaylama.

## D.4. API Controller (`app/Http/Controllers/Api/`) - 1 Dosya
1. **`CustomerSyncController.php`** — IAA-Takvim veri köprüsü (Ziyaret/Müşteri senkronu).

---
---

# BÖLÜM E: MODEL KATMANI (55 DOSYA - TAM LİSTE + İLİŞKİLER)

## E.1. IAA & İş Akışı Grubu
1. **`Iaa.php`** (18KB) — Proje ana varlığı. **İlişkiler:** `belongsTo(User)` Gönderen/Onaylayan, `belongsTo(Bolum)`, `belongsTo(Takim)` Atanan, `hasMany(IaaResim)`, `hasOne(MusteriSikayeti)` via `iaa_id`, `hasOne(IaaTalep)`, `hasOne(IaaZiyaretPlani)`, `belongsToMany(Takim)` via `iaa_talepleri`, `hasOneThrough(IaaWorkflow, IaaTalep)`.
2. **`IaaLog.php`** — Proje statü/veri değişim logları. `belongsTo(Iaa)`.
3. **`IaaProgressUpdate.php`** — Workflow adımlarının tamamlanma verisi. `belongsTo(Iaa)`, `belongsTo(IaaWorkflowStep)`.
4. **`IaaResim.php`** — Projeye yüklenmiş görseller. `belongsTo(Iaa)`.
5. **`IaaStepAssignment.php`** — Adıma özel atanan personel. `belongsTo(Iaa)`, `belongsTo(User)`, `belongsTo(IaaWorkflowStep)`.
6. **`IaaTalep.php`** — Takımların projeyi alma talepleri. `belongsTo(Iaa)`, `belongsTo(Takim)`, `belongsTo(IaaWorkflow)`.
7. **`IaaWorkflow.php`** — Proje akış şablonu (8D, Kaizen). `hasMany(IaaWorkflowStep)`.
8. **`IaaWorkflowStep.php`** — Şablon içindeki adım tanımları. `belongsTo(IaaWorkflow)`.
9. **`IaaZiyaretPlani.php`** — Müşteri saha ziyareti planı. `belongsTo(Iaa)`, `belongsTo(Customer)`. Takvim API ile haberleşir.

## E.2. Disiplin Grubu
10. **`DisciplinaryCase.php`** — Disiplin dosyası (Tutanak). `belongsTo(User)` Suçlu/Bildiren, `belongsTo(DisciplinaryBehavior)`, `belongsTo(DisciplinaryImpact)`, `belongsTo(DisciplinaryScope)`, `hasMany(DisciplinaryLog)`, `hasMany(DisciplinaryVote)`, `hasMany(DisciplinaryComment)`, `hasOneThrough(DisciplinaryCategory, DisciplinaryBehavior)`.
11. **`DisciplinaryBehavior.php`** — Suç maddeleri. `belongsTo(DisciplinaryCategory)`.
12. **`DisciplinaryCategory.php`** — Suç kategorileri (Ağır/Hafif kusur).
13. **`DisciplinaryComment.php`** — Dosya altı tartışmalar.
14. **`DisciplinaryCommentHistory.php`** — Silinen/düzenlenen yorumların yedeği.
15. **`DisciplinaryImpact.php`** — Olayın şirkete etkisi (Düşük/Yüksek).
16. **`DisciplinaryLog.php`** — Disiplin süreci işlem geçmişi.
17. **`DisciplinaryMultiplier.php`** — Tekrar eden suçlarda ceza katsayısı.
18. **`DisciplinaryPenaltyScale.php`** — Puan aralığına göre ceza skalası.
19. **`DisciplinaryScope.php`** — Olayın kapsamı (Bireysel/Grup).
20. **`DisciplinaryVote.php`** — Kurul üyelerinin ceza oylama verisi.

## E.3. Arabuluculuk Grubu
21. **`ArabuluculukCase.php`** — Hukuki vaka ana dökümü. `belongsTo(Arabulucu)`, `belongsTo(User)` Çalışan/Avukat/Creator, `hasMany(ArabuluculukFile)`, `hasMany(ArabuluculukPayment)`, `hasMany(ArabuluculukLog)`, `hasMany(ArabuluculukKurulDegerlendirme)`.
22. **`Arabulucu.php`** — Arabulucu şahıs verisi.
23. **`ArabulucuLog.php`** — Arabulucu bilgi değişiklik tarihçesi.
24. **`ArabuluculukAnlasmaMaddesi.php`** — Hukuki anlaşma tipleri.
25. **`ArabuluculukFile.php`** — Vakaya bağlı hukuki evraklar.
26. **`ArabuluculukKurul.php`** — Arabuluculuk karar heyeti tanımı.
27. **`ArabuluculukKurulDegerlendirme.php`** — Kurul üye görüşleri.
28. **`ArabuluculukLog.php`** — Vaka aşama logları.
29. **`ArabuluculukPayment.php`** — Anlaşılan tutar ve ödeme takvimi.
30. **`ArabuluculukSettingLog.php`** — Hukuki ayar logları.

## E.4. Müşteri & Şikayet Grubu
31. **`MusteriSikayeti.php`** (12KB) — Şikayet ana beyni. SoftDeletes. `belongsTo(Customer)`, `belongsTo(Iaa)` via `iaa_id`, `belongsTo(SikayetKategori)`, `belongsTo(SikayetAltKategori)`, `belongsTo(Takim)` Çözüm Takımı, `belongsTo(User)` Kurul Üyesi, `hasMany(MusteriSikayetiDosyasi)`, `hasMany(MusteriSikayetiLog)`, `hasOneThrough(Bolum, SikayetKategori)`.
32. **`Customer.php`** — Müşteri firma verisi. Takvim ile senkron.
33. **`MusteriSikayetiDosyasi.php`** — Şikayete eklenen kanıtlar.
34. **`MusteriSikayetiLog.php`** — Şikayet durum değişim tarihçesi.
35. **`MusteriLog.php`** — Müşteri kartı saha logları.
36. **`SikayetKategori.php`** — Şikayet türleri/bölümleri. `belongsTo(Bolum)`.
37. **`SikayetAltKategori.php`** — Şikayet detay kırılımları.
38. **`SikayetGuestPassword.php`** — Kayıtsız müşteri link erişim şifreleri.
39. **`SikayetIadesi.php`** — Şikayet sonucu ticari iadeler.
40. **`SikayetTeknikDetay.php`** — Lot no, Makine, Hammadde. `belongsTo(MusteriSikayeti)`, `belongsTo(Machine)`, `belongsTo(GenelHammadde)`, `belongsTo(UrunVersiyonu)`.
41. **`IadeTanimi.php`** — İade seçenekleri tanımları.

## E.5. Organizasyon & Sistem Grubu
42. **`User.php`** (8KB) — Personel ana modeli. `belongsTo(Bolum)`, `belongsToMany(Takim)`, `hasMany(Iaa)`, `hasMany(DisciplinaryCase)`. Spatie Permission ile roller.
43. **`Takim.php`** — Çalışma grupları (Team). `belongsTo(User)` Lider, `belongsToMany(User)` Üyeler.
44. **`TakimDavetiyesi.php`** — Ekipler arası geçiş davetleri.
45. **`Bolum.php`** — Fabrika şeması. `hasMany(User)`, `hasMany(SikayetKategori)`.
46. **`BolumKategorisi.php`** — Bölüm üst grupları.
47. **`Machine.php`** — Üretim hattı makineleri. `belongsTo(Bolum)`.
48. **`MachineLog.php`** — Makine-Bölüm-Proje arası ilişki logları.
49. **`GenelHammadde.php`** — Şikayetlerde seçilecek hammadde listesi.
50. **`UrunVersiyonu.php`** — Üretilen ürünlerin versiyon takibi.
51. **`Setting.php`** — Sistem parametreleri (Key-Value).
52. **`RaporKurali.php`** — Otomatik rapor zamanlama kuralları.
53. **`LoginActivity.php`** — Güvenlik/Denetim giriş kayıtları.
54. **`ProfileComment.php`** — Kullanıcı profili performans yorumları.
55. **`ProjeYorumu.php`** — IAA Workspace tartışmaları.

---
---

# BÖLÜM F: BİLDİRİM KATMANI (44 DOSYA - TAM LİSTE)

1. **`AdimSorumlusuAtandi.php`** — Proje adımına sorumlu atandığında kişiye bildirim.
2. **`DisiplinKararVerildi.php`** — Disiplin sonucu (ceza/beraat) ilgililere.
3. **`DisiplinKurulunaSevkEdildi.php`** — Vaka kurul oylamasına geçti, üyelere.
4. **`DisiplinOylamaBaslatildi.php`** — Kurul başkanı oylama açtı, tüm üyelere.
5. **`DisiplinTutanagiOlusturuldu.php`** — Yeni tutanak, bölüm lideri ve İK'ya.
6. **`EkSureTalebiBildirimi.php`** — Ek süre talebi lidere/onaycıya.
7. **`HataliBildirimBildirimi.php`** — Hatalı bildirim iddiası kalite yöneticisine.
8. **`IaaHavuzaEklendi.php`** — Yeni öneri havuza düştü, tüm takımlara.
9. **`IaaProjesineTalepGeldi.php`** — Takım projeyi talep etti, adminlere.
10. **`IaaTalebiSonuclandi.php`** — Talep onay/red, takıma.
11. **`MusteriGeriBildirimBildirimi.php`** — Müşteri puan/yorum, ilgili ekibe.
12. **`NewUserAddedNotification.php`** — Yeni kullanıcıya hoş geldin + şifre.
13. **`PersonelProjeyeDavetEdildi.php`** — SQUAD daveti, kişiye.
14. **`PersonelSavunmaVerdi.php`** — Savunma yüklendi, disiplin sorumlusuna.
15. **`PersonelTakimBildirimi.php`** — Takıma eklendi/çıkarıldı.
16. **`PersonelTutanakOlusturduBildirimi.php`** — Tutanak girildi.
17. **`ProfilYorumBildirimi.php`** — Profile yorum bırakıldı.
18. **`ProjeAdimiGuncellendi.php`** — Workflow step içeriği değişti.
19. **`ProjeDavetYaniti.php`** — Davet kabul/red, proje liderine.
20. **`ProjeDurumuDegisti.php`** — Proje statüsü değişti, tüm paydaşlara.
21. **`ProjeEkibindenCikarildi.php`** — Projeden çıkarıldı.
22. **`ProjeEkipDaveti.php`** — Yeni SQUAD daveti.
23. **`ResetPasswordNotification.php`** — Şifre sıfırlama bildirimi.
24. **`RolAtandiNotification.php`** — Yeni rol tanımlandı.
25. **`SikayetTakimaAtandiBildirimi.php`** — Şikayet çözüm takımına atandı.
26. **`SorumluAtandiBildirimi.php`** — Spesifik sorumlu seçildi.
27. **`TakimDavetiAldin.php`** — Takım daveti geldi.
28. **`TakimDavetiYanitlandi.php`** — Davet kabul/red, liderine.
29. **`TakimIstegiKabulEdildi.php`** — Katılma isteği onaylandı.
30. **`TakimKatilmaIstegi.php`** — Katılma isteği, takım liderine.
31. **`TakimaDavetEdildi.php`** — Takıma ekleme isteği.
32. **`TakimdanCikarildi.php`** — Takımdan uzaklaştırıldı.
33. **`TalepBildirimi.php`** — Genel sistem talepleri adminlere.
34. **`VerifyEmailNotification.php`** — E-posta doğrulama linki.
35. **`VisitStatusChanged.php`** — Ziyaret onay/red/revize, IAA'ya.
36. **`YeniDisiplinYorumu.php`** — Disiplin dosyasına yeni yorum.
37. **`YeniIaaOnerisi.php`** — Yeni iyileştirme fikri, havuz yöneticilerine.
38. **`YeniMusteriSikayetiBildirimi.php`** — Yeni şikayet, kalite ekibine anlık.
39. **`YeniProjeYorumu.php`** — Workspace tartışma panosuna yeni yorum.
40. **`ZiyaretOnayBekliyorBildirimi.php`** — Ziyaret amir onayına sunuldu.
41. **`ZiyaretOnayDurumuBildirimi.php`** — Ziyaret onay döngüsü son durumu.
42. **`ZiyaretPlanlandiBilgilendirme.php`** — Ziyaret finalize edildi.
43. **`ZiyaretRevizyonBildirimi.php`** — Ziyaret planında revizyon istendi.
44. **`ZiyaretciAtandiBildirimi.php`** — Ziyarete eşlik edecek kişilere bilgi.

---
---

# BÖLÜM G: MAİL KATMANI (11 DOSYA - TAM LİSTE)

1. **`NewCustomerUserCreated.php`** — Müşteri portala eklendiğinde giriş bilgileri.
2. **`OtomatikYoneticiRaporu.php`** — Periyodik operasyon özetleri PDF olarak amirlere.
3. **`SikayetAcildiMusteriBildirimi.php`** — Müşteriye "Kaydınız alındı, Takip No: X".
4. **`SikayetAtamaBildirimi.php`** — Çözüm takımı atandığında personele.
5. **`SikayetAtamaBilgilendirmesi.php`** — Üst yönetime atanan işler bilgisi.
6. **`SikayetAtandiMusteriBildirimi.php`** — Müşteriye "Ekip atandı" bilgisi.
7. **`SikayetOnayMail.php`** — Müşterinin son onayını isteyen mail.
8. **`SikayetTakipBilgilendirmeMail.php`** — Süreç içi ara bilgilendirmeler.
9. **`WelcomeUserMail.php`** — Yeni personele sistem tanıtım maili.
10. **`YeniSikayetBildirimi.php`** — Kurul üyelerine yeni iş.
11. **`YeniYorumBildirimiMail.php`** — Kritik dökümana yorum yazıldığında amir uyarı.

---
---

# BÖLÜM H: LİVEWİRE BİLEŞENLERİ (24 DOSYA - TAM LİSTE)

## H.1. Admin Livewire (`app/Livewire/Admin/`)
1. **`Ayarlar/RaporKurallari.php`** — Otomatik rapor zamanlama kuralları UI.
2. **`IadeTanimlariYonetimi.php`** — İade seçenekleri CRUD.
3. **`MusteriYonetimi.php`** — Müşteri portföy yönetimi (Real-time tablo).
4. **`ProjeAdimYorumlari.php`** — Proje adımlarına yorum sistemi.
5. **`RaporlarTablosu.php`** — Rapor verileri tablo bileşeni.
6. **`SikayetCozumGorevlerim(silinecek).php`** — Eski görev bileşeni (Deprekated).
7. **`SikayetMusteriSecimi.php`** — Şikayet formunda müşteri seçme bileşeni.
8. **`SikayetRaporSayfasi.php`** — Şikayet analiz rapor sayfası.
9. **`SikayetTriyajModal.php`** — Şikayet triyaj (önceliklendirme) modalı.
10. **`SikayetlerTablosu.php`** — Şikayet listesi dinamik tablo.
11. **`SquadYonetimModal.php`** — SQUAD ekibi oluşturma/düzenleme modalı.
12. **`TakvimMapping.php`** — IAA-Takvim bölüm eşleştirme arayüzü.
13. **`WorkflowStepsManager.php`** — İş akışı adımları sürükle-bırak yöneticisi.
14. **`ZiyaretListesi.php`** — Ziyaret kayıtları listesi.
15. **`ZiyaretPlanlarim.php`** — Kullanıcının planlı ziyaretleri.

## H.2. Dashboard Livewire
16. **`Dashboard/SuperAdminVisitTable.php`** — SuperAdmin ziyaret özet tablosu.
17. **`Dashboard/VisitApprovalWidget.php`** — Ziyaret onay widget'ı.

## H.3. Genel Livewire
18. **`ExecutiveReport.php`** — Üst yönetim real-time kokpit paneli.
19. **`GlobalChatBot.php`** — Sistem içi mesajlaşma ve AI asistan.
20. **`MusteriSikayetRaporu.php`** — Detaylı tablo ve filtreleme motoru.
21. **`NotificationsIndex.php`** — Tüm bildirimlerin yönetim ekranı.
22. **`SikayetGorevlerim.php`** — Kişiye atanan aktif şikayet görevleri.

## H.4. Project Livewire
23. **`Project/ActiveStep.php`** — Aktif adım bileşeni.
24. **`Project/PlanVisit.php`** — Saha ziyareti planlama (Takvim senkronu).

---
---

# BÖLÜM I: SERVİS KATMANI (16 DOSYA - TAM LİSTE)

## I.1. AI Servisleri
1. **`Ai/AiTools.php`** — AI araç tanımları.
2. **`Ai/GeminiService.php`** — Google Gemini API entegrasyonu.

## I.2. Dashboard Servisleri
3. **`Dashboard/BolumDashboardService.php`** — Bölüm bazlı dashboard verileri.
4. **`Dashboard/HukukDashboardService.php`** — Hukuk modülü dashboard verileri.
5. **`Dashboard/KullaniciIstatistikService.php`** — Kullanıcı istatistikleri.
6. **`Dashboard/KullaniciPuanService.php`** — Bireysel performans puan hesabı.
7. **`Dashboard/MusteriDashboardService.php`** — Müşteri portalı dashboard.
8. **`Dashboard/SikayetDashboardService.php`** — Şikayet istatistikleri.
9. **`Dashboard/SuperAdminDashboardService.php`** — SuperAdmin tam dashboard.
10. **`Dashboard/YonetimDashboardService.php`** — Yönetim seviyesi dashboard.

## I.3. ProjectWorkspace Servisleri
11. **`ProjectWorkspace/MusteriBildirimService.php`** — Müşteri bildirim işlemleri.
12. **`ProjectWorkspace/ProjeAdimIslemleriService.php`** — Adım tamamlama iş mantığı.
13. **`ProjectWorkspace/ProjeCalismaAlaniService.php`** — Workspace veri hazırlama.
14. **`ProjectWorkspace/ProjeTalepYonetimService.php`** — Hatalı bildirim ve talep yönetimi.
15. **`ProjectWorkspace/ProjeTamamlamaService.php`** — Proje kapama ve iade iş mantığı.

## I.4. Genel Servisler
16. **`RaporVeriServisi.php`** — Otomatik rapor ham veri toplama.

---
---

# BÖLÜM J: OBSERVER, POLİCY, TRAİT, COMMAND (13 DOSYA)

## J.1. Observerlar (6 Dosya)
1. **`ContactObserver.php`** — İletişim kaydı değişikliklerini izler.
2. **`CustomerObserver.php`** — Müşteri kaydı değişikliğinde log ve senkron.
3. **`IaaObserver.php`** — IAA projesi oluşturulduğunda/güncellendiğinde tetiklenir.
4. **`MusteriSikayetiObserver.php`** — Şikayet oluşma/güncelleme olaylarını yakalar.
5. **`SikayetIadesiObserver.php`** — İade kaydı değişikliğinde tetiklenir.
6. **`TakimDavetiyesiObserver.php`** — Davetiye statüsü değişince bildirim gönderir.

## J.2. Policy'ler (2 Dosya)
1. **`IaaPolicy.php`** — IAA projelerine erişim yetkilendirmesi.
2. **`MusteriSikayetiPolicy.php`** — Şikayetlere erişim yetkilendirmesi.

## J.3. Trait'ler (2 Dosya)
1. **`ComplaintNotificationTrait.php`** — Şikayet bildirimlerini toplu gönderim.
2. **`NotifiesManager.php`** — Yönetici bildirim gönderim mekanizması.

## J.4. Artisan Command'lar (3 Dosya)
1. **`RaporlariKontrolEt.php`** — Cron ile periyodik rapor gönderir.
2. **`SyncAllToTakvim.php`** — Tüm verileri Takvim'e toplu senkronize eder.
3. **`SyncTeamScores.php`** — Takım ve personel puanlarını hesaplar.

---
---

# BÖLÜM K: MİDDLEWARE & PROVIDERS (5 DOSYA)

## K.1. Middleware (2 Dosya)
1. **`BlockCustomerAccess.php`** — Müşteri kullanıcılarının personel alanlarına girmesini engeller.
2. **`UserActivity.php`** — Kullanıcının son görülme zamanını (last_seen) günceller.

## K.2. Providers (3 Dosya)
1. **`AppServiceProvider.php`** — Global Blade değişkenleri, makro tanımları.
2. **`AuthServiceProvider.php`** — Policies ve Gate yetkilerinin kaydedilmesi.
3. **`BroadcastServiceProvider.php`** — Broadcast kanal tanımları.

---
---

# BÖLÜM L: BLADE/VIEW KATMANI (86+ DOSYA)

## L.1. Admin Blade Dosyaları
- **admin/arabulucular/** — `create`, `edit`, `index`, `logs`, `show` (5 dosya)
- **admin/arabuluculuk/** — `create`, `index`, `show` + `parcalar/` (12 partial) + `tanimlar/` (2 dosya)
- **admin/ayarlar/** — `index`, `_mail_notification_settings` (2 dosya)
- **admin/bolumler/** — `create`, `dashboard`, `edit`, `index` (4 dosya)
- **admin/bolum_kategorileri/** — `index` (1 dosya)
- **admin/bolum_kalite_yoneticileri/** — `index` (1 dosya)
- **admin/cozum_takimlari/** — `create`, `edit`, `index`, `show` (4 dosya)
- **admin/direktor_atamalari/** — `index` (1 dosya)
- **admin/dis_avukatlar/** — `create`, `index` (2 dosya)
- **admin/disiplin/** — `create`, `edit`, `index`, `settings`, `show` + `partials/` (6 partial) + `sorumlular/` (1)
- **admin/mavi_yaka/** — `create`, `edit`, `index` (3 dosya)
- **admin/raporlar/** — `index` (1 dosya)
- **admin/sikayetler/** — `create`, `edit`, `index`, `kurul_girdileri`, `show` + `partials/` (varsa)
- **admin/takim_yonetim/** — `create`, `edit`, `index`, `show` (4 dosya)
- **admin/users/** — `create`, `edit`, `index` (3 dosya)
- **admin/workflows/** — `create`, `edit`, `index`, `show`, `steps` (5 dosya)

## L.2. Genel Blade Dosyaları
- **auth/** — `confirm-password`, `forgot-password`, `login`, `register`, `reset-password`, `verify-email`
- **components/** — Ana UI parçaları (Alert, Button, Layout vb.)
- **dashboard/** — Rol bazlı dashboard partial'ları
- **emails/** — Mail şablonları (HTML/Text)
- **errors/** — 404, 403, 500 hata sayfaları
- **guest/** — Misafir portal arayüzleri
- **iaa/** — İyileştirme önerileri listeleme/detay
- **layouts/** — `admin.blade.php`, `app.blade.php`, `guest.blade.php` (3 ana iskelet)
- **livewire/** — Livewire bileşenlerinin blade dosyaları
- **notifications/** — Bildirim merkezi
- **profile/** — Kullanıcı ayarları/şifre sayfaları
- **proje-calisma-alani/** — SQUAD workspace UI (partiallar dahil)
- **public/** — Tokenlı şikayet takip sayfaları
- **takimlar/** — Takım yönetim/davetiye ekranları
- **user-directory/** — Şirket rehberi
- **vendor/** — Pagination vb. özelleştirilmiş şablonlar

## L.3. Kök Blade Dosyaları
- **`dashboard.blade.php`** (19KB) — Ana dashboard sayfası.
- **`kullanici-puanlari.blade.php`** (16KB) — Personel puan detayları.
- **`puan-durumu.blade.php`** (20KB) — Puan sıralama tablosu.
- **`welcome.blade.php`** (8KB) — Karşılama/Landing sayfası.

---
---

# BÖLÜM M: VERİTABANI ŞEMASI (68 TABLO - TAM LİSTE)

## M.1. IAA & İş Akışı Tabloları
| Tablo | Açıklama |
|---|---|
| `iaas` | Tüm IAA projeleri ve şikayetlerin ana tablosu |
| `iaa_logs` | Proje statü değişim logları |
| `iaa_progress_updates` | Workflow adımları tamamlanma verisi |
| `iaa_resimler` | Projeye yüklenen görseller |
| `iaa_step_assignments` | Adıma özel personel atamaları |
| `iaa_talepleri` | Takımların proje talepleri (Pivot) |
| `iaa_user` | Proje-Kullanıcı SQUAD ilişki pivot tablosu |
| `iaa_workflows` | İş akışı şablonları (8D, Kaizen vb.) |
| `iaa_workflow_steps` | Şablon içindeki adım tanımları |
| `iaa_ziyaret_planlari` | Müşteri saha ziyaret planları |

## M.2. Disiplin Tabloları
| Tablo | Açıklama |
|---|---|
| `disciplinary_cases` | Disiplin dosyaları (Tutanaklar) |
| `disciplinary_behaviors` | Suç madde tanımları |
| `disciplinary_categories` | Suç kategorileri |
| `disciplinary_comments` | Dosya altı tartışmalar |
| `disciplinary_comment_histories` | Yorum düzenleme/silme geçmişi |
| `disciplinary_impacts` | Olay şiddeti tanımları |
| `disciplinary_logs` | Süreç işlem geçmişi |
| `disciplinary_multipliers` | Tekrar ceza katsayıları |
| `disciplinary_penalty_scales` | Puan-Ceza skalası |
| `disciplinary_scopes` | Olay kapsamı tanımları |
| `disciplinary_votes` | Kurul oyları |

## M.3. Arabuluculuk Tabloları
| Tablo | Açıklama |
|---|---|
| `arabulucular` | Arabulucu şahıs bilgileri |
| `arabulucu_logs` | Arabulucu bilgi değişiklik logları |
| `arabuluculuk_cases` | Hukuki vaka dosyaları |
| `arabuluculuk_files` | Vaka evrakları |
| `arabuluculuk_logs` | Vaka aşama logları |
| `arabuluculuk_payments` | Ödeme planları |
| `arabuluculuk_anlasma_maddesis` | Anlaşma madde şablonları |
| `arabuluculuk_kurul_degerlendirmeleri` | Kurul üye değerlendirmeleri |
| `arabuluculuk_setting_logs` | Hukuki ayar logları |

## M.4. Müşteri & Şikayet Tabloları
| Tablo | Açıklama |
|---|---|
| `customers` | Müşteri firma kartları |
| `musteri_sikayetleri` | Şikayet ana verileri (SoftDeletes) |
| `musteri_sikayeti_dosyalari` | Şikayet kanıt dosyaları |
| `musteri_sikayeti_loglari` | Şikayet durum değişim logları |
| `musteri_logs` | Müşteri kartı değişiklik logları |
| `sikayet_kategorileri` | Şikayet ana türleri |
| `sikayet_alt_kategorileri` | Şikayet alt kırılımları |
| `sikayet_guest_passwords` | Dış müşteri geçici şifreleri |
| `sikayet_iadeleri` | Ticari iade kayıtları |
| `sikayet_teknik_detaylari` | Lot/Makine/Hammadde teknik verisi |
| `iade_tanimlari` | İade seçenekleri |

## M.5. Organizasyon Tabloları
| Tablo | Açıklama |
|---|---|
| `users` | Tüm personel ve müşteri kullanıcıları |
| `roles` | Spatie rol tanımları |
| `permissions` | Spatie izin tanımları |
| `model_has_roles` | Kullanıcı-Rol eşleştirme |
| `model_has_permissions` | Kullanıcı-İzin eşleştirme |
| `role_has_permissions` | Rol-İzin eşleştirme |
| `takimlar` | Çalışma grupları |
| `takim_user` | Takım-Kullanıcı pivot |
| `takim_davetiyeleri` | Davetiye kayıtları |
| `bolumler` | Fabrika bölümleri |
| `bolum_kategorileri` | Bölüm üst grupları |
| `bolum_kalite_yoneticileri` | Kalite yöneticisi atamaları |
| `machines` | Üretim makineleri |
| `machine_logs` | Makine işlem logları |
| `genel_hammaddeler` | Hammadde listesi |
| `urun_versiyonlari` | Ürün versiyonları |

## M.6. Sistem & Altyapı Tabloları
| Tablo | Açıklama |
|---|---|
| `settings` | Sistem parametreleri (Key-Value) |
| `rapor_kurallari` | Otomatik rapor zamanlama |
| `notifications` | Laravel bildirim kuyruğu |
| `login_activities` | Giriş/Çıkış güvenlik logları |
| `profile_comments` | Profil performans yorumları |
| `proje_yorumlari` | Workspace tartışma yorumları |
| `sessions` | Aktif oturum verileri |
| `cache` / `cache_locks` | Laravel önbellek |
| `jobs` / `job_batches` / `failed_jobs` | Kuyruk sistemi |
| `migrations` | Veritabanı migrasyon geçmişi |
| `password_reset_tokens` | Şifre sıfırlama tokenları |
| `personal_access_tokens` | API erişim tokenları |

---
---

# BÖLÜM N: ROTA HARİTASI (web.php - 770 SATIR)

## N.1. Public & Guest Rotaları (Giriş Serbest)
- `/` → `WelcomeController@index` (Landing)
- `/oneri-yap` → `GuestIaaController@create/store` (Misafir öneri, `throttle:10,1`)
- `/sikayet` → `PublicSikayetController@create/store` (Kayıtsız şikayet girişi)
- `/sikayetler/{token}` → `PublicSikayetController@show/edit/update/guestLogin/storeFeedback` (Token bazlı takip)
- `/api/get-alt-kategoriler/{id}` → Dinamik kategori API

## N.2. Yetkili Personel Alanı (Auth + BlockCustomerAccess)
- `/dashboard` → `DashboardController@index` (Kişiye özel ana sayfa, `verified` zorunlu)
- `/puan-durumu` | `/tum-personel` | `/kullanici-puanlari/{user}` → Performans tabloları
- `/profile` → `ProfileController@edit/update/destroy`
- `/kullanici-listesi` → `UserDirectoryController@index`
- `/musteri-profil/{customer}` → `CustomerProfileController@show`
- `/iyilestirme/yeni` | `/havuz` → `IaaController` (Öneri oluşturma ve havuz)
- `/proje-calisma-alani/{iaa}` → `ProjectWorkspaceController@show` (SQUAD çalışma alanı)
- `/proje-calisma-alani/{id}/...` → Adım tamamlama, iade, revizyon, export PDF/Excel, hatalı bildirim, ek süre, müşteri bildirim, gizlilik vb.
- `/takimlar` → `TakimController` Resource + davet/üyelik işlemleri
- `/takim-projeleri` → `IaaController@takimProjeleri`
- `/yonetim` → `ExecutiveReport` Livewire (Sadece Superadmin/Yönetim)
- `/notifications` → `NotificationController` (Bildirim yönetimi)
- `/ziyaretler` | `/ziyaret-planlarim` → Livewire ziyaret bileşenleri

## N.3. Admin Panel (`/admin` prefix)
- `/admin/users` → `UserController` Resource (Superadmin)
- `/admin/sistem-ayarlari` → `SistemAyarController` (Superadmin)
- `/admin/workflows` → `IaaWorkflowController` Resource + adım yönetimi (Superadmin)
- `/admin/bolumler` → `BolumController` Resource + Makine/Hammadde/Versiyon CRUD
- `/admin/bolum-kategorileri` → `BolumKategorisiController` (Superadmin)
- `/admin/kalite-yoneticileri` → `BolumKaliteYoneticisiController` (Superadmin)
- `/admin/direktorler` → `DirectorAssignmentController` (Superadmin)
- `/admin/iaa-yonetim` → `IaaYonetimController` (Onay/Red/Puan/Atama/Arşiv + Bölüm/Direktör onay döngüsü)
- `/admin/takim-yonetim` → `TakimYonetimController` (Superadmin)
- `/admin/sikayetler` → `SikayetController` Resource + `kurulGirdileri`
- `/admin/sikayet-kategorileri` → `SikayetKategoriController` + Alt Kategori CRUD
- `/admin/musteriler` → `MusteriYonetimi` Livewire
- `/admin/raporlar` → `RaporController` (Excel/PDF)
- `/admin/musteri-sikayet-raporlari` | `/admin/iaa-raporlari` → Canlı raporlar

## N.4. Disiplin Süreçleri (`/admin/disiplin` ve `/disiplin`)
- Tutanak oluşturma, savunma, yorum, kurul oylama, ceza onayı, karar geri alma
- Disiplin ayarları: Kategori/Etki/Kapsam/Suç/Katsayı/Skala CRUD
- Personel savunma verme: `/disiplin/{case}/savunma-ver`

## N.5. Arabuluculuk Süreçleri (`/admin/arabuluculuk`)
- Vaka CRUD, dosya yükleme/silme, durum değişikliği, arabulucu atama
- Kurul değerlendirmesi, ödeme kaydetme/onaylama/reddetme
- Dosya kapatma, geri alma
- Tanımlar: Anlaşma maddeleri CRUD
- Arabulucu/Dış Avukat yönetimi

## N.6. Güvenlik Katmanları (Middleware)
- **`auth`** → Giriş zorunlu.
- **`verified`** → E-posta onayı zorunlu.
- **`BlockCustomerAccess`** → Müşteri → personel alanı engeli.
- **`role:Superadmin`** → En üst yetki.
- **`role:Superadmin|Yonetim`** → C-Level kokpit.
- **`role:Superadmin|Hukuk Admini`** → Hukuk ayarları.
- **`throttle:10,1`** → Spam koruması.

---
---

# BÖLÜM O: KRİTİK TEKNİK NOTLAR (AI & DEVELOPER İÇİN)

1. **Iaa ↔ MusteriSikayeti:** `iaa_id` üzerinden göbekten bağlı. Şikayet → Projeye dönüşebilir.
2. **Takim ↔ User:** `belongsToMany` ile çoktan çoğa. Bir personel birden fazla takımda olabilir.
3. **Bolum ↔ Her Şey:** Neredeyse tüm operasyonel modeller `bolum_id`'ye bağlı. Yetkilendirme bu ID üzerinden döner.
4. **Hibrit Mimari:** Laravel 10+, Livewire 3, çift DB entegrasyonu.
5. **Dinamik Workflow:** Adımlar sabittir ama içerikleri (Widget'lar) dinamik olarak yönetilir.
6. **Role-Based Logic:** Yetki statik Role değil, Proje-User-Bölüm-Direktör ilişkisinin sonucudur.
7. **Audit Trail:** Hemen her modelin `*Log` tablosu vardır.
8. **Hata Arama İpucu:** Önce Controller'daki `compact()` verisine, sonra Model ilişkisine, en son Blade'deki `$variable->field` kullanımına bak.
