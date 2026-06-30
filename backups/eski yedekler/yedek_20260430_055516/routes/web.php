<?php

// Gerekli tüm Controller'ları en üste ekliyoruz
use App\Http\Controllers\Admin\BolumController;
use App\Http\Controllers\Admin\BolumKategorisiController; // Yeni
use App\Http\Controllers\Admin\IaaYonetimController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SistemAyarController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TakimYonetimController;
use App\Http\Controllers\Admin\IaaWorkflowController;
use App\Http\Controllers\IaaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KullaniciIstekController;
use App\Http\Controllers\Admin\IstekController;
use App\Http\Controllers\TakimController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestIaaController;
use App\Http\Controllers\ProjectWorkspaceController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\MusteriSikayetiDosyasi;
use App\Livewire\Admin\SikayetCozumGorevlerim;
use App\Http\Controllers\PublicSikayetController;
use App\Livewire\SikayetGorevlerim;
use App\Http\Middleware\BlockCustomerAccess;

// === GÜVENLİK DÜZELTMESİ İÇİN EKLENDİ ===
use App\Http\Controllers\Admin\SikayetController;
use App\Http\Controllers\Admin\SikayetKategoriController;
use App\Http\Controllers\Admin\CozumTakimiController;
// === GÜVENLİK DÜZELTMESİ SONU ===

use App\Http\Controllers\Admin\ArabuluculukController; // En üste eklemeyi unutma
use App\Http\Controllers\Admin\ArabulucuController; // arabulucular için
use App\Http\Controllers\Admin\ExternalLawyerController;

use App\Http\Controllers\Admin\ArabuluculukTanimController;
use App\Http\Controllers\Admin\DirectorAssignmentController;
use App\Http\Controllers\Admin\PuanRaporController;
use App\Http\Controllers\Admin\HealthCheckController;
use App\Http\Controllers\Admin\CustomerProfileController;
use App\Http\Controllers\Admin\MusteriLogController;
use App\Http\Controllers\VisitFileUploadController;
use App\Http\Controllers\Admin\MachineLogController;
use App\Http\Controllers\Admin\LoginLogController;
use App\Http\Controllers\UserDirectoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\MaviYakaController;
use App\Http\Controllers\Admin\BolumKaliteYoneticisiController;
use App\Livewire\ExecutiveReport;
use App\Livewire\Admin\ZiyaretListesi;
use App\Livewire\Admin\ZiyaretPlanlarim;
use App\Livewire\Admin\MusteriYonetimi;
use App\Livewire\Admin\TakvimMapping;
use App\Livewire\Admin\IadeTanimlariYonetimi;




/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- */

Route::get('/gemini-test', function () {
    $apiKey = config('services.gemini.api_key');
    $response = Illuminate\Support\Facades\Http::get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");
    return $response->json();
});

Route::get('/', function () {
    if (Auth::check())
    {
        return redirect()->route('dashboard');
    }
    return app(WelcomeController::class)->index();
})->name('home');


// Misafir (Giriş Yapmayan) Kullanıcı Rotaları
Route::get('/oneri-yap', [GuestIaaController::class , 'create'])->name('guest.iaa.create');
Route::post('/oneri-yap', [GuestIaaController::class , 'store'])
    ->middleware('throttle:10,1') // <-- BU SATIRI EKLEYİN
    ->name('guest.iaa.store');

// =============================================
// =============================================
// === MÜŞTERİ PORTALI (URL'de admin yazmaz) ===
// =============================================
// IIS Alt dizin yapısı (iaa/) nedeniyle prefix('') kullanıyoruz.
// Bu grubu public rotaların ÜSTÜNE alıyoruz ki /sikayetler/{sikayet} önce eşleşsin.
Route::middleware(['auth', BlockCustomerAccess::class])->group(function () {
    Route::get('/sikayetler/yeni', [SikayetController::class , 'create'])->name('iaa.sikayetler.create');
    Route::get('/sikayetler/{sikayet}', [SikayetController::class , 'show'])
        ->name('iaa.sikayetler.show')
        ->whereNumber('sikayet');
});

// =============================================
// == PUBLIC MÜŞTERİ ŞİKAYET ROTALARI
// =============================================
Route::get('/sikayet', [PublicSikayetController::class , 'create'])->name('public.sikayet.create');
Route::post('/sikayet', [PublicSikayetController::class , 'store'])->middleware('throttle:10,1')->name('public.sikayet.store');
Route::get('/sikayetler/{token}', [PublicSikayetController::class , 'show'])->name('public.sikayet.show');
Route::post('/sikayetler/{token}/login', [PublicSikayetController::class , 'guestLogin'])->name('public.sikayet.guestLogin');
Route::get('/sikayetler/{token}/edit', [PublicSikayetController::class , 'edit'])->name('public.sikayet.edit');
Route::put('/sikayetler/{token}', [PublicSikayetController::class , 'update'])->name('public.sikayet.update');
Route::post('/sikayetler/{token}/feedback', [PublicSikayetController::class , 'storeFeedback'])->name('public.sikayet.storeFeedback');

// Geçici debug rotası (Eğer prefix bazlı 404 varsa bunu deneyeceğiz)
Route::get('/iaa-debug/{sikayet}', [SikayetController::class , 'show'])->middleware(['auth']);

// =============================================

// =============================================
// === YENİ EKLEME (MİSAFİR PROJE ERİŞİMİ) ===
// Bu rotayı aşağıdaki 'auth' grubundan buraya taşıdık.
// Güvenlik, Controller'ın içinde (Adım 2B'de) sağlanacak.
Route::get('/proje-calisma-alani/{iaa}', [ProjectWorkspaceController::class , 'show'])->name('proje.workspace.show');
// === EKLEME SONU ===

// Alt Kategorileri getiren API rotası (Herkese açık)
Route::get('/api/get-alt-kategoriler/{kategori_id}', [SikayetKategoriController::class , 'getAltKategorilerApi'])->name('api.getAltKategoriler');

// Giriş yapmış tüm kullanıcılar için ortak alan
Route::middleware(['auth', BlockCustomerAccess::class])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class , 'index'])->middleware(['verified'])->name('dashboard');
    Route::get('/dashboard/switch/{view}', [DashboardController::class , 'switchDashboard'])->name('dashboard.switch');
    Route::post('/dashboard/dismiss-password-alert', [DashboardController::class , 'dismissPasswordAlert'])->name('dashboard.dismiss-password-alert');
    Route::post('/dashboard/save-tab-order', [DashboardController::class , 'saveTabOrder'])->name('dashboard.save-tab-order');
    Route::get('/puan-durumu', [DashboardController::class , 'puanDurumu'])->name('puan-durumu');
    Route::get('/tum-bolum-puanlari', [DashboardController::class , 'tumBolumler'])->name('tum-bolum-puanlari');
    Route::get('/tum-bolum-puanlari/export/excel', [DashboardController::class , 'exportBolumAnalizExcel'])->name('tum-bolum-puanlari.export.excel');
    Route::get('/tum-bolum-puanlari/export/pdf', [DashboardController::class , 'exportBolumAnalizPdf'])->name('tum-bolum-puanlari.export.pdf');
    Route::get('/bolum-puanlari/{bolum}', [DashboardController::class , 'bolumPuanlari'])->name('bolum-puanlari');
    Route::get('/bolum-puanlari/{bolum}/export/excel', [DashboardController::class , 'exportBolumDetayExcel'])->name('bolum-puanlari.export.excel');
    Route::get('/bolum-puanlari/{bolum}/export/pdf', [DashboardController::class , 'exportBolumDetayPdf'])->name('bolum-puanlari.export.pdf');
    Route::get('/puan-raporu', [PuanRaporController::class , 'index'])->name('puan.raporu');
    Route::get('/tum-personel', [DashboardController::class , 'tumPersonel'])->name('tum-personel');
    Route::get('/kullanici-puanlari/{user}', [DashboardController::class , 'kullaniciPuanlari'])->name('profile.puanlar');
    Route::get('/takim-puanlari/{takim}', [DashboardController::class , 'takimPuanlari'])->name('takim-puanlari');
    Route::get('/personel/dogum-gunleri', \App\Livewire\Personel\DogumGunleriListesi::class)->name('personel.dogum-gunleri');
    Route::get('/personel/yildonumleri', \App\Livewire\Personel\YildonumleriListesi::class)->name('personel.yildonumleri');

    // Profil Yönetimi
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
    Route::post('/profile/istek', [KullaniciIstekController::class, 'store'])->name('profile.istek.store');
    Route::delete('/profile/istek/{istek}/iptal', [KullaniciIstekController::class, 'cancel'])->name('profile.istek.cancel');
    Route::post('/profile/dismiss-warning', [ProfileController::class, 'dismissWarning'])->name('profile.dismiss.warning');

    // Kullanıcı Rehberi (Herkes Erişebilir)
    Route::get('/kullanici-listesi', [UserDirectoryController::class , 'index'])->name('user-directory.index');

    // Herkesin görebileceği Genel Profil Sayfası
    Route::get('/kullanici-profil/{user}', [ProfileController::class , 'show'])->name('profile.show');
    Route::post('/kullanici-profil/{user}/yorum', [ProfileController::class , 'storeComment'])->middleware('throttle:5,1')->name('profile.comment.store');
    Route::put('/kullanici-profil/yorum/{comment}', [ProfileController::class , 'updateComment'])->name('profile.comment.update');
    Route::delete('/kullanici-profil/yorum/{comment}', [ProfileController::class , 'destroyComment'])->name('profile.comment.destroy');

    // --- MÜŞTERİ PROFİLİ VE DETAYLARI ---
    Route::get('/musteri-profil/{customer}', [CustomerProfileController::class , 'show'])
        ->name('musteri.profil.show');

    // Mevcut profil rotasının altına ekle:
    Route::post('/musteri-profil/{customer}/yetkili-ekle', [CustomerProfileController::class , 'storeRepresentative'])
        ->name('musteri.yetkili.store');

    Route::delete('/musteri-profil/yetkili-sil/{user}', [CustomerProfileController::class , 'destroyRepresentative'])
        ->name('musteri.yetkili.destroy');

    // Tüm Müşteri Logları Sayfası
    Route::get('/tum-musteri-loglari', [MusteriLogController::class , 'index'])
        ->name('musteri-logs.index');

    // Müşteri Ziyaretleri Sayfası (Livewire)
    Route::get('/ziyaretler', ZiyaretListesi::class)
        ->name('admin.ziyaretler');

    // Müşteri Ziyaret Planları ve Onayları (Livewire)
    Route::get('/ziyaret-planlarim', ZiyaretPlanlarim::class)
        ->name('admin.ziyaret-planlarim');

    // [YENİ] Geleneksel Dosya Yükleme Rotaları (PropertyNotFoundException Çözümü İçin)
    Route::post('/ziyaret/dosya-yukle', [VisitFileUploadController::class , 'upload'])->name('ziyaret.dosya.upload');
    Route::delete('/ziyaret/dosya-sil', [VisitFileUploadController::class , 'delete'])->name('ziyaret.dosya.delete');


    // =================================================================
    // === YENİ EKLENEN KISIM: YÖNETİM KOKPİTİ ===
    // =================================================================
    // Sadece 'Superadmin' VEYA 'Yonetim' rolüne sahip olanlar görebilir.
    Route::group(['middleware' => ['role:Superadmin|Yonetim']], function () {
            Route::get('/yonetim', ExecutiveReport::class)
                ->name('yonetim.index');

            // [YENİ] Tüm Bekleyen İşler Sayfası
            Route::get('/tum-bekleyen-isler', [DashboardController::class , 'tumBekleyenIsler'])
                ->name('admin.tum-bekleyen-isler');
    });

    // Superadmin, Yönetim, Bölüm Lideri veya Direktör rolüne sahip olanlar görebilir.
    Route::group(['middleware' => ['role:Superadmin|Yonetim|Bölüm Lideri|Direktör']], function () {
            // Makine İşlem Geçmişi (Global)
            Route::get('/makine-loglari', [MachineLogController::class , 'index'])
                ->name('machine-logs.index');

            // [YENİ] Giriş Logları
            Route::get('/logs/login-activities', [LoginLogController::class , 'index'])->name('logs.login.index');
            Route::get('/logs/login-activities/{user}', [LoginLogController::class , 'show'])->name('logs.login.show');
    });
    // =================================================================
    
        // --- İAA MODÜLÜ (SORUN ÇÖZÜCÜ DEĞİŞİKLİK) ---
        // URL'leri değiştirdik ama 'name'leri koruduk. Böylece diğer kodların bozulmaz.
        // localhost:8000/iyilestirme/yeni adresine gidecek.
        Route::get('/iyilestirme/yeni', [IaaController::class , 'create'])->name('iaa.create');
        Route::post('/iyilestirme/kaydet', [IaaController::class , 'store'])->name('iaa.store');

        Route::get('/havuz', [IaaController::class , 'havuz'])->name('iaa.havuz');
        Route::post('/iaa-talep-et/{id}', [IaaController::class , 'takimlaTalepEt'])->name('iaa.takimlaTalepEt');
        Route::post('/iyilestirme/{iaa}/talebi-geri-cek', [IaaController::class , 'talebiGeriCek'])->name('iaa.talebiGeriCek');
        //Route::get('/takim-projeleri', [IaaController::class, 'takimProjeleri'])->name('iaa.takimProjeleri'); // Buradan taşındı
        // Resource (URL 'iyilestirme' oldu, ama route isimleri 'iaa.index' gibi kaldı)
        Route::resource('iyilestirme', IaaController::class)
            ->names('iaa')
            ->parameters(['iyilestirme' => 'iaa'])
            ->except(['create', 'store']);

        // --- TAKIM MODÜLÜ ROTALARI ---
        Route::post('takimlar/{takim}/davet-gonder', [TakimController::class , 'davetGonder'])->name('takimlar.davetGonder');
        Route::delete('takimlar/{takim}/uyeler/{user}', [TakimController::class , 'uyeCikar'])->name('takimlar.uyeCikar');
        Route::get('davetlerim', [TakimController::class , 'davetlerim'])->name('takimlar.davetlerim');
        Route::post('davetlerim/{davetiye}/kabul-et', [TakimController::class , 'davetiKabulEt'])->name('takimlar.davetiKabulEt');
        Route::post('davetlerim/{davetiye}/reddet', [TakimController::class , 'davetiReddet'])->name('takimlar.davetiReddet');
        Route::delete('davetlerim/{davetiye}/iptal-et', [TakimController::class , 'davetiIptalEt'])->name('takimlar.davetiIptalEt');
        Route::get('katilma-isteklerim', [TakimController::class , 'isteklerim'])->name('takimlar.isteklerim');
        Route::delete('katilma-isteklerim/{davetiye}', [TakimController::class , 'istegiGeriCek'])->name('takimlar.istegiGeriCek');
        Route::post('takimlar/{takim}/katilma-istegi', [TakimController::class , 'katilmaIstegiGonder'])->name('takimlar.katilmaIstegi');
        Route::post('takim-istekleri/{davetiye}/kabul-et', [TakimController::class , 'istekKabulEt'])->name('takimlar.istekKabulEt');
        Route::post('takim-istekleri/{davetiye}/reddet', [TakimController::class , 'istegiReddet'])->name('takimlar.istegiReddet');
        Route::resource('takimlar', TakimController::class)->parameters(['takimlar' => 'takim']);

        // Proje Çalışma Alanı Rotaları
    
        // === DEĞİŞİKLİK ===
        // GET rotası yukarı (public alana) taşındı.
        // Route::get('/proje-calisma-alani/{iaa}', [ProjectWorkspaceController::class, 'show'])->name('proje.workspace.show');
        // === DEĞİŞİKLİK SONU ===
    
        // Bu POST rotaları GİRİŞ YAPMAYI GEREKTİRİR (Erhan Cesur gibi), bu yüzden 'auth' içinde kalmalı
        Route::post('/proje-calisma-alani/{assignment_id}/adim/{step_id}', [ProjectWorkspaceController::class , 'storeStep'])->name('proje.workspace.storeStep');

        // YENİ: Şikayet Detayları Güncelleme (Lot, Makine vb.)
        Route::put('/proje-calisma-alani/{iaa}/sikayet-detaylari', [ProjectWorkspaceController::class , 'updateComplaintDetails'])->name('proje.update-complaint-details');
        Route::post('/proje-calisma-alani/adim/{progress_update}/yeniden-ac', [ProjectWorkspaceController::class , 'reopenStep'])->name('proje.workspace.reopenStep');
        // VAZGEÇME ROTASI
        Route::post('/proje-calisma-alani/adim/{id}/vazgec', [ProjectWorkspaceController::class , 'cancelReopenStep'])
            ->name('proje.workspace.cancelReopenStep');

        // === GİZLİLİK YÖNETİMİ (BURAYA EKLENDİ) ===
        Route::post('/proje/{iaa_id}/adim/{step_id}/toggle-visibility', [ProjectWorkspaceController::class , 'toggleStepVisibility'])
            ->name('proje.step.toggleVisibility');

        // === MÜŞTERİ BİLDİRİM ROTALARI (BURAYA EKLE) ===
        Route::post('/proje-calisma-alani/{id}/musteri-bildir', [ProjectWorkspaceController::class , 'notifyCustomer'])->name('proje.notify_customer');
        Route::post('/proje-calisma-alani/{id}/musteri-sifre-sifirla', [ProjectWorkspaceController::class , 'resetCustomerPassword'])->name('proje.reset_customer_password');

        // === PROJE ÇIKTI ALMA (PDF & EXCEL) ROTALARI ===
        Route::get('/proje-calisma-alani/{id}/export-pdf', [ProjectWorkspaceController::class , 'exportPdf'])->name('proje.export.pdf');
        Route::get('/proje-calisma-alani/{id}/export-excel', [ProjectWorkspaceController::class , 'exportExcel'])->name('proje.export.excel');
        // ===============================================
    
        // =============================================================
        // === YENİ EKLENECEK: TALEP YÖNETİMİ ROTALARI ===
        // =============================================================
        Route::post('/proje/{id}/talep-bildir', [ProjectWorkspaceController::class , 'markAsRequest'])->name('proje.markAsRequest');
        Route::post('/proje/{id}/talep-karar-kalite', [ProjectWorkspaceController::class , 'decideRequestByQuality'])->name('proje.decideRequestByQuality');
        Route::post('/proje/{id}/talep-karar-superadmin', [ProjectWorkspaceController::class , 'decideRequestBySuperadmin'])->name('proje.decideRequestBySuperadmin');

        // HATALI BİLDİRİM (FAULTY NOTIFICATION) ROTILARI
        Route::post('/proje/{id}/hatali-bildirim', [ProjectWorkspaceController::class , 'markAsFaulty'])->name('proje.markAsFaulty');
        Route::post('/proje/{id}/hatali-bildirim-karar-kalite', [ProjectWorkspaceController::class , 'decideFaultyByQuality'])->name('proje.decideFaultyByQuality');
        Route::post('/proje/{id}/hatali-bildirim-karar-direktor', [ProjectWorkspaceController::class , 'decideFaultyByDirector'])->name('proje.decideFaultyByDirector');
        Route::post('/proje/{id}/hatali-bildirim-karar-superadmin', [ProjectWorkspaceController::class , 'decideFaultyBySuperadmin'])->name('proje.decideFaultyBySuperadmin');
        Route::post('/proje/{id}/hatali-bildirim-geri-al', [ProjectWorkspaceController::class , 'recallFaulty'])->name('proje.recallFaulty');
        Route::post('/proje/{id}/hatali-bildirim-kalite-geri-al', [ProjectWorkspaceController::class , 'recallFaultyByQuality'])->name('proje.recallFaultyByQuality');
        Route::post('/proje/{id}/hatali-bildirim-direktor-geri-al', [ProjectWorkspaceController::class , 'recallFaultyByDirector'])->name('proje.recallFaultyByDirector');
        Route::post('/proje/{id}/hatali-bildirim-superadmin-geri-al', [ProjectWorkspaceController::class , 'recallFaultyBySuperadmin'])->name('proje.recallFaultyBySuperadmin');

        // EK SÜRE TALEBİ ROTALARI
        Route::post('/proje/{id}/ek-sure-talep', [ProjectWorkspaceController::class , 'requestExtension'])->name('proje.talep.extension.request');
        Route::post('/proje/{id}/ek-sure-karar-direktor', [ProjectWorkspaceController::class , 'decideExtensionByDirector'])->name('proje.talep.extension.director');
        Route::post('/proje/{id}/ek-sure-karar-superadmin', [ProjectWorkspaceController::class , 'decideExtensionBySuperadmin'])->name('proje.talep.extension.superadmin');
        // =============================================================
    
        // =============================================================
        // === PROJE TAMAMLAMA VE İADE İŞLEMLERİ (YENİ) ===
        // =============================================================
        // İade VARSA bu rotaya gider
        Route::post('proje-calisma-alani/{id}/geri-cek', [ProjectWorkspaceController::class , 'recallSubmission'])->name('proje.recallSubmission');
        Route::post('proje-calisma-alani/{id}/tamamla-iadeli', [ProjectWorkspaceController::class , 'completeWithReturn'])->name('proje.completeWithReturn');
        Route::post('proje-calisma-alani/{id}/tamamla-iadesiz', [ProjectWorkspaceController::class , 'completeWithoutReturn'])->name('proje.completeWithoutReturn');

        // İade Bilgisini Silme (Revizyon durumunda gerekebilir)
        Route::delete('/proje-calisma-alani/{id}/iade-sil', [ProjectWorkspaceController::class , 'deleteReturnInfo'])
            ->name('proje.deleteReturnInfo');

        // İade Notu/Belgesi Müşteri Görünürlüğü (Aç/Kapat)
        Route::post('/proje-calisma-alani/iade/{id}/toggle-visibility', [ProjectWorkspaceController::class , 'toggleCustomerVisibility'])
            ->name('proje.toggleCustomerVisibility');

        Route::post('/proje-calisma-alani/{id}/recall', [ProjectWorkspaceController::class , 'recallSubmission'])
            ->name('proje.recall');
        // =============================================================
    
        // === YENİ: Takım Projelerim buraya taşındı ===
        Route::get('/takim-projeleri', [IaaController::class , 'takimProjeleri'])->name('iaa.takimProjeleri');

        // Adıma Sorumlu Atama Rotası
        Route::post('/proje-calisma-alani/{iaa}/adim/{step}/ata', [ProjectWorkspaceController::class , 'assignUserToStep'])
            ->name('proje.workspace.assignUserToStep');

        Route::get('/sikayet-gorevlerim', SikayetGorevlerim::class)
            ->middleware('auth')
            ->name('sikayet-gorevlerim.index');

        // Raporlar (Orijinal ReportController'a geri dönüldü)
        Route::get('/admin/reports/daily-complaints', [ReportController::class , 'dailyComplaintReport'])->name('admin.reports.daily_complaints');

        // API Rotaları (Web içinde tutuyoruz çünkü Session Auth kullanıyoruz)
        Route::get('/musteriler', MusteriYonetimi::class)
            ->name('personel.musteriler.index');
        // Not: Aynı Livewire bileşenini kullanacağız ama Layout dinamik olacak.
    
        // Proje Davet Yanıt Rotaları
        Route::post('/proje-davet/{iaa}/yanit', [App\Http\Controllers\IaaController::class , 'davetYanitla'])->name('iaa.davetYanitla');

        // === BİLDİRİM SİSTEMİ API ROTALARI (BURAYA EKLEYİN) ===
        Route::get('/notifications', [App\Http\Controllers\NotificationController::class , 'index'])->name('notifications.index');
        Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class , 'unreadCount'])->name('notifications.unreadCount');
        Route::post('/notifications/mark-as-read', [App\Http\Controllers\NotificationController::class , 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/notifications/{id}/toggle', [App\Http\Controllers\NotificationController::class , 'toggleStatus'])->name('notifications.toggleStatus');
        Route::get('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class , 'readAndRedirect'])->name('notifications.readAndRedirect');

        Route::get('/admin/bildirim-takip', function () {
            return view('admin.notifications.audit');
        }
         )->name('admin.notifications.audit')->middleware(['auth', 'role:Superadmin|Yonetim|Bölüm Lideri|Bölüm Kalite Yöneticisi']);
    });

// =================================================================
// YÖNETİCİ (ADMIN) PANELİ ROTALARI
// =================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // =================================================================
    // === YENİ EKLENEN: MÜŞTERİ YÖNETİMİ (Superadmin + Yönetim + Kurul) ===
    // =================================================================
    Route::get('/musteriler', MusteriYonetimi::class)
        ->name('musteriler.index') // <--- CHANGED THIS LINE
        ->middleware(['role:Superadmin|Yonetim|Müşteri Şikayeti Kurulu|Bölüm Kalite Yöneticisi|Bölüm Lideri|Müşteri Şikayeti Çözüm Lideri|Direktör|Hukuk Admini|Hukuk Yöneticisi']);
    // =================================================================


    // =================================================================
    // === BÖLÜM VE MAKİNE YÖNETİMİ (Superadmin + Bölüm Lideri) ===
    // =================================================================
    Route::middleware(['role:Superadmin|Bölüm Lideri|Yonetim|Direktör'])->group(function () {
            // Bölüm Dashboard (Hem Superadmin Hem Lider Hem Yönetim Erişebilir)
            Route::get('bolumler/{bolum}/dashboard', [BolumController::class , 'dashboard'])->name('bolumler.dashboard');

            // Bölüm listesi, detay ve düzenleme (Yetki kontrolü Controller'da yapılacak)
            Route::resource('bolumler', BolumController::class)
                ->parameters(['bolumler' => 'bolum'])
                ->except(['destroy']); // Silme işlemi sadece Superadmin'de kalsın (veya controllerda kontrol et)
    
            // Makine Yönetimi Rotaları
            Route::post('bolumler/{bolum}/machines', [BolumController::class , 'storeMachine'])->name('bolumler.machines.store');
            Route::get('machines/{machine}', [BolumController::class , 'showMachine'])->name('machines.show');
            Route::put('machines/{machine}', [BolumController::class , 'updateMachine'])->name('machines.update');
            Route::delete('machines/{machine}', [BolumController::class , 'deleteMachine'])->name('machines.destroy');

            // Hammadde Yönetimi Rotaları
            Route::post('bolumler/{bolum}/hammaddeler', [BolumController::class , 'storeHammadde'])->name('bolumler.hammaddeler.store');
            Route::put('hammaddeler/{hammadde}', [BolumController::class , 'updateHammadde'])->name('hammaddeler.update');
            Route::delete('hammaddeler/{hammadde}', [BolumController::class , 'deleteHammadde'])->name('bolumler.hammaddeler.delete');

            // Versiyon Yönetimi Rotaları
            Route::post('bolumler/{bolum}/versiyonlar', [BolumController::class , 'storeVersiyon'])->name('bolumler.versiyonlar.store');
            Route::put('versiyonlar/{versiyon}', [BolumController::class , 'updateVersiyon'])->name('versiyonlar.update');
            Route::delete('versiyonlar/{versiyon}', [BolumController::class , 'deleteVersiyon'])->name('bolumler.versiyonlar.delete');

        }
                 );

        // --- MAVİ YAKA PERSONEL YÖNETİMİ ---
        Route::middleware(['role:Superadmin|Hukuk Admini|Bölüm Lideri'])->group(function () {
            // Toplu Aktarım Rotaları
            Route::get('mavi-yaka/import', [App\Http\Controllers\Admin\MaviYakaController::class , 'importView'])->name('mavi-yaka.import');
            Route::get('mavi-yaka/export', [App\Http\Controllers\Admin\MaviYakaController::class , 'export'])->name('mavi-yaka.export');
            Route::post('mavi-yaka/import-preview', [App\Http\Controllers\Admin\MaviYakaController::class , 'previewImport'])->name('mavi-yaka.import-preview');
            Route::post('mavi-yaka/import-execute', [App\Http\Controllers\Admin\MaviYakaController::class , 'executeImport'])->name('mavi-yaka.import-execute');
            Route::get('mavi-yaka/download-template', [App\Http\Controllers\Admin\MaviYakaController::class , 'downloadTemplate'])->name('mavi-yaka.download-template');

            // İşten Çıkış (Resign) Rotası
            Route::post('mavi-yaka/{maviYaka}/resign', [App\Http\Controllers\Admin\MaviYakaController::class , 'resign'])->name('mavi-yaka.resign');
            Route::post('mavi-yaka/{id}/restore', [App\Http\Controllers\Admin\MaviYakaController::class , 'restore'])->name('mavi-yaka.restore');
            Route::post('mavi-yaka/import-execute-chunk', [App\Http\Controllers\Admin\MaviYakaController::class , 'executeImportChunk'])->name('mavi-yaka.import-execute-chunk');
            Route::get('mavi-yaka/import-finish', [App\Http\Controllers\Admin\MaviYakaController::class , 'finishImport'])->name('mavi-yaka.import-finish');

            Route::resource('mavi-yaka', App\Http\Controllers\Admin\MaviYakaController::class)->parameters([
                'mavi-yaka' => 'maviYaka'
            ]);
        }
         );

        // Sadece Superadmin'in silebileceği Bölüm rotası (Resource harici tanımlama gerekebilir veya controller check)
        // Resource kullandığımız için destroy yukarıda hariç tutuldu, burada ekleyelim
        Route::delete('bolumler/{bolum}', [BolumController::class , 'destroy'])
            ->name('bolumler.destroy')
            ->middleware('role:Superadmin');

        // BÖLÜM KATEGORİLERİ (SADECE SUPERADMIN)
        Route::post('bolum-kategorileri/store-ajax', [BolumKategorisiController::class, 'storeAjax'])->name('bolum-kategorileri.store-ajax');
        Route::resource('bolum-kategorileri', BolumKategorisiController::class)
            ->middleware('role:Superadmin')
            ->parameters(['bolum-kategorileri' => 'bolumKategorisi']);


        // =================================================================
        // === GÜVENLİK DÜZELTMESİ: SADECE SUPERADMIN ERİŞEBİLİR ===
        // =================================================================
        // Bu grup, 'Superadmin' rolüne sahip olmayan herkesi engelleyecektir.
        Route::middleware(['role:Superadmin'])->group(function () {
            // SİSTEM SAĞLIK PANELİ
            Route::get('sistem-sagligi', [HealthCheckController::class, 'index'])->name('health.index');
            Route::get('sistem-sagligi/init', [HealthCheckController::class, 'init'])->name('health.init');
            Route::post('sistem-sagligi/tarama', [HealthCheckController::class, 'scan'])->name('health.scan');
            Route::get('sistem-sagligi/kalibrasyon-gunlugu', \App\Livewire\Admin\CalibrationLogs::class)->name('health.logs');

            // Kullanıcı Yönetimi

            // Kullanıcı Yönetimi
            Route::resource('users', UserController::class)->except(['show']);
            Route::patch('users/{user}/onayla', [UserController::class , 'onayla'])->name('users.onayla');
            Route::post('users/{user}/verify-email', [UserController::class , 'verifyEmail'])->name('users.verifyEmail'); // <--- EKLENDİ
            Route::patch('users/{user}/resign', [UserController::class , 'resign'])->name('users.resign'); // <--- EKLENDİ
            Route::patch('users/{id}/restore', [UserController::class , 'restore'])->name('users.restore'); // <--- EKLENDİ
    
            // KULLANICI İSTEKLERİ
            Route::get('istekler', [IstekController::class, 'index'])->name('istekler.index');
            Route::post('istekler/{istek}/approve', [IstekController::class, 'approve'])->name('istekler.approve');
            Route::post('istekler/{istek}/reject', [IstekController::class, 'reject'])->name('istekler.reject');
    
            // BÖLÜM KALİTE YÖNETİCİSİ ATAMA
            Route::get('kalite-yoneticileri', [App\Http\Controllers\Admin\BolumKaliteYoneticisiController::class , 'index'])
                ->name('kalite-yoneticileri.index');

            Route::post('kalite-yoneticileri/{user}', [App\Http\Controllers\Admin\BolumKaliteYoneticisiController::class , 'update'])
                ->name('kalite-yoneticileri.update');

            // DİREKTÖR ATAMALARI
            Route::get('direktorler', [DirectorAssignmentController::class , 'index'])
                ->name('direktorler.index');

            Route::post('direktorler', [DirectorAssignmentController::class , 'storeDirector'])
                ->name('direktorler.store');

            Route::post('direktorler/{user}', [DirectorAssignmentController::class , 'update'])
                ->name('direktorler.update');

            // İAA Yönetimi
    
            Route::patch('iaa-yonetim/{iaa}/onayla', [IaaYonetimController::class , 'onayla'])->name('iaa-yonetim.onayla');
            Route::patch('iaa-yonetim/{iaa}/reddet', [IaaYonetimController::class , 'reddet'])->name('iaa-yonetim.reddet');
            Route::patch('iaa-yonetim/{iaa}/geri-al', [IaaYonetimController::class , 'geriAl'])->name('iaa-yonetim.geriAl');
            Route::delete('iaa-yonetim/{iaa}', [IaaYonetimController::class , 'destroy'])->name('iaa-yonetim.destroy');
            Route::post('iaa-yonetim/bulk-delete', [IaaYonetimController::class , 'bulkDestroy'])->name('iaa-yonetim.bulkDestroy');
            Route::get('iaa-yonetim/{iaa}/talepler', [IaaYonetimController::class , 'talepleriGoster'])->name('iaa-yonetim.talepleriGoster');
            Route::get('iaa-yonetim/{iaa}/takim/{takim}/ata', [IaaYonetimController::class , 'atamaFormuGoster'])->name('iaa-yonetim.atamaFormu');
            Route::post('iaa-yonetim/{iaa}/takim/{takim}/ata', [IaaYonetimController::class , 'atamaYap'])->name('iaa-yonetim.atamaYap');
            Route::get('iaa-yonetim/arsiv', [IaaYonetimController::class , 'arsiv'])->name('iaa-yonetim.arsiv');
            Route::patch('iaa-yonetim/{iaa}/update-score', [IaaYonetimController::class , 'updateScore'])->name('iaa-yonetim.updateScore');
            Route::get('iaa-yonetim/{iaa}/reassign', [IaaYonetimController::class , 'reassignForm'])->name('iaa-yonetim.reassignForm');
            Route::patch('iaa-yonetim/{iaa}/reassign', [IaaYonetimController::class , 'reassignUpdate'])->name('iaa-yonetim.reassignUpdate');
            Route::post('iaa-yonetim/{iaa}/update-status', [IaaYonetimController::class , 'updateStatus'])->name('iaa-yonetim.updateStatus');
            Route::post('iaa/{iaa}/approve-completed', [IaaYonetimController::class , 'approveCompleted'])->name('iaa.approveCompleted');
            Route::post('iaa/{iaa}/reject-completed', [IaaYonetimController::class , 'rejectCompleted'])->name('iaa.rejectCompleted');
            Route::post('iaa/{iaa}/request-revision', [IaaYonetimController::class , 'requestRevision'])->name('iaa.requestRevision');

            // TAKIM YÖNETİMİ
            Route::post('takim-yonetim/{takim}/uye-ekle', [TakimYonetimController::class , 'uyeEkle'])->name('takim-yonetim.uyeEkle');
            Route::delete('takim-yonetim/{takim}/uye-cikar/{user}', [TakimYonetimController::class , 'uyeCikar'])->name('takim-yonetim.uyeCikar');
            Route::post('takim-yonetim/{takim}/proje-ata', [TakimYonetimController::class , 'projeAta'])->name('takim-yonetim.projeAta');
            Route::resource('takim-yonetim', TakimYonetimController::class)->parameters(['takim-yonetim' => 'takim']);

            // AKIŞ ŞABLONLARI YÖNETİMİ
            Route::resource('workflows', IaaWorkflowController::class);
            Route::get('workflows/{workflow}/steps', [IaaWorkflowController::class , 'editSteps'])->name('workflows.editSteps');
            Route::post('workflows/{workflow}/steps', [IaaWorkflowController::class , 'storeStep'])->name('workflows.storeStep');
            Route::put('workflows/steps/{step}', [IaaWorkflowController::class , 'updateStep'])->name('workflows.updateStep');
            Route::delete('workflows/steps/{step}', [IaaWorkflowController::class , 'destroyStep'])->name('workflows.destroyStep');

            // SİSTEM AYARLARI
            Route::get('sistem-ayarlari', [SistemAyarController::class , 'index'])->name('sistem-ayarlari.index');
            Route::post('sistem-ayarlari', [SistemAyarController::class , 'update'])->name('sistem-ayarlari.update');
            Route::get('takvim-eslestirme', TakvimMapping::class)->name('takvim-eslestirme.index');
            Route::get('profil-yorum-denetimi', App\Livewire\Admin\ProfilYorumDenetimi::class)->name('profil-yorum-denetimi.index');
            Route::get('puan-senkronize', [DashboardController::class , 'syncAllUserPoints'])->name('puan.sync');

            Route::get('/iade-ayarlari', IadeTanimlariYonetimi::class)
                ->middleware('role:Superadmin|Yonetim')
                ->name('iade-ayarlari.index');

            // Şikayet KATEGORİ ve ÇÖZÜM TAKIMI Yönetimi (Sadece Superadmin)
            Route::resource('sikayet-kategorileri', SikayetKategoriController::class)
                ->parameters(['sikayet-kategorileri' => 'sikayetKategori'])
                ->except(['show']);

            Route::resource('cozum-takimlari', CozumTakimiController::class)
                ->parameters(['cozum-takimlari' => 'cozumTakimi']);

            // === BURAYA EKLENDİ: ALT KATEGORİ YÖNETİMİ ===
            Route::post('sikayet-kategorileri/{sikayetKategori}/alt-kategori', [SikayetKategoriController::class , 'storeAltKategori'])
                ->name('sikayet-kategorileri.alt-kategori.store');

            Route::delete('sikayet-alt-kategori/{altKategori}', [SikayetKategoriController::class , 'destroyAltKategori'])
                ->name('sikayet-alt-kategori.destroy');

            Route::put('sikayet-alt-kategori/{altKategori}', [App\Http\Controllers\Admin\SikayetKategoriController::class , 'updateAltKategori'])
                ->name('sikayet-alt-kategori.update');
        // =================================================
    



        }
         ); // --- Superadmin grubunun sonu ---
    
        // =============================================================
        // GRUP: RAPORLAR (Superadmin ve Yönetim Görebilir)
        // =============================================================
        Route::middleware(['role:Superadmin|Yonetim'])->group(function () {
            Route::get('raporlar', [ReportController::class , 'index'])->name('raporlar.index');
            Route::get('raporlar/excel', [ReportController::class , 'exportExcel'])->name('raporlar.exportExcel');
            Route::get('raporlar/pdf', [ReportController::class , 'exportPdf'])->name('raporlar.exportPdf');



            // Makine İşlem Geçmişi (Global) - URL: /admin/makine-loglari
            Route::get('/makine-loglari', [App\Http\Controllers\Admin\MachineLogController::class , 'index'])
                ->name('machine-logs.index');
        }
         );

        Route::get('iaa-yonetim', [IaaYonetimController::class , 'index'])->name('iaa-yonetim.index');
        Route::post('iaa-yonetim/{iaa}/bolum-onayi', [IaaYonetimController::class , 'bolumOnayiVer'])
            ->name('iaa-yonetim.bolumOnayiVer');
        Route::post('iaa-yonetim/{iaa}/bolum-revizyon', [IaaYonetimController::class , 'bolumRevizyonIste'])->name('iaa-yonetim.bolumRevizyon');
        Route::post('iaa-yonetim/{iaa}/bolum-red', [IaaYonetimController::class , 'bolumReddet'])->name('iaa-yonetim.bolumReddet');
        Route::post('iaa-yonetim/{iaa}/bolum-onayi-geri-al', [IaaYonetimController::class , 'bolumOnayiGeriAl'])
            ->name('iaa-yonetim.bolumOnayiGeriAl');

        Route::delete('iaa-yonetim/{iaa}/takim/{takim}/talep-reddet', [IaaYonetimController::class , 'talepReddet'])
            ->name('iaa-yonetim.talepReddet');

        // DİREKTÖR ONAY ROTALARI
        Route::post('iaa-yonetim/{iaa}/direktor-onayi', [IaaYonetimController::class , 'direktorOnayiVer'])
            ->name('iaa-yonetim.direktorOnayiVer');
        Route::post('iaa-yonetim/{iaa}/direktor-revizyon', [IaaYonetimController::class , 'direktorRevizyonIste'])
            ->name('iaa-yonetim.direktorRevizyon');
        Route::post('iaa-yonetim/{iaa}/direktor-red', [IaaYonetimController::class , 'direktorReddet'])
            ->name('iaa-yonetim.direktorReddet');
        Route::patch('iaa-yonetim/{iaa}/direktor-onayi-geri-al', [IaaYonetimController::class , 'direktorOnayiGeriAl'])
            ->name('iaa-yonetim.direktorOnayiGeriAl');

        // =================================================================
        // === MÜŞTERİ ŞİKAYETLERİ MODÜLÜ (İlgili Roller Erişebilir) ===
        // =================================================================
        // Bu rotalar, 'Superadmin' OLMAYAN ama 'Müşteri Şikayeti Kurulu' gibi
        // rollere sahip kişilerin erişmesi gereken yerlerdir.
        // Bu rotaların kendi Controller'ları içinde (örn: SikayetController@index) 
        // $this->authorize(...) ile zaten korunduğunu varsayıyoruz.
    
        // Şikayet Raporları (Canlı) - KORUMALI
        Route::get('musteri-sikayet-raporlari', [ReportController::class , 'sikayetRaporlari'])
            ->name('sikayet-raporlari.index')
            ->middleware(['role:Superadmin|Yonetim|Müşteri Şikayeti Kurulu|Bölüm Kalite Yöneticisi|Direktör|Bölüm Lideri']);

        // İAA Raporları (Canlı) - YENİ
        Route::get('iaa-raporlari', [ReportController::class , 'iaaRaporlari'])
            ->name('iaa-raporlari.index')
            ->middleware(['role:Superadmin|Yonetim|Müşteri Şikayeti Kurulu|Bölüm Kalite Yöneticisi|Direktör|Bölüm Lideri']);

        // Tüm Şikayetler Listesi - KORUMALI
        Route::get('musteri-sikayet-raporlari/tum-liste', [ReportController::class , 'tumSikayetListesi'])
            ->name('sikayet-raporlari.tum-liste')
            ->middleware(['role:Superadmin|Yonetim|Müşteri Şikayeti Kurulu|Bölüm Kalite Yöneticisi|Direktör|Bölüm Lideri']);

        // Şikayet Rapor Tablosu (Canlı Livewire)
        Route::get('musteri-sikayet-rapor-sayfasi', [ReportController::class , 'sikayetRaporTablosu'])
            ->name('sikayet-raporlari.tablo')
            ->middleware(['role:Superadmin|Yonetim|Müşteri Şikayeti Kurulu|Bölüm Kalite Yöneticisi|Direktör|Bölüm Lideri']);

        // İadeler Raporu (Yeni)
        Route::get('musteri-sikayet-iade-raporlari', [ReportController::class , 'iadeRaporlari'])
            ->name('sikayet-iade-raporlari.index')
            ->middleware(['role:Superadmin|Yonetim|Müşteri Şikayeti Kurulu|Bölüm Kalite Yöneticisi|Direktör|Bölüm Lideri|Müşteri Şikayeti Çözüm Lideri']);

        // Kurul Girdileri
        Route::get('sikayetler/kurul-girdileri', [SikayetController::class , 'kurulGirdileri'])
            ->name('sikayetler.kurulGirdileri')
            ->middleware('role:Superadmin|Yonetim|Müşteri Şikayeti Kurulu|Bölüm Kalite Yöneticisi');

        // Müşteri Şikayetleri Yönetimi (CRUD)
        Route::resource('sikayetler', SikayetController::class)
            ->names('sikayetler')
            ->parameters(['sikayetler' => 'sikayet'])
            ->middleware('role:Superadmin|Yonetim|Müşteri Şikayeti Kurulu|Bölüm Kalite Yöneticisi|Bölüm Lideri|Müşteri Şikayeti Çözüm Lideri|Direktör');

        Route::post('sikayetler/{sikayet}/restore', [SikayetController::class , 'restore'])
            ->name('sikayetler.restore')
            ->withTrashed();

        // =================================================================
        // 1. GENEL DİSİPLİN ERİŞİMİ (Görüntüleme ve Oluşturma)
        // =================================================================
        // EKLENEN ROL: 'Disiplin Kurulu Üyesi' (Artık sayfayı görebilecekler)
        Route::middleware(['auth'])
            ->prefix('disiplin')
            ->name('disiplin.')
            ->group(function () {

            // --- YENİ: DİSİPLİN KURULU PORTALI ---
            Route::middleware(['role:Superadmin|Yonetim|Hukuk Admini|Hukuk Yöneticisi|Disiplin Kurulu Başkanı|Disiplin Kurulu Üyesi'])
                ->prefix('kurul')
                ->name('kurul.')
                ->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\DisiplinKuruluController::class , 'index'])->name('index');
                Route::post('/uye-ekle', [App\Http\Controllers\Admin\DisiplinKuruluController::class , 'storeMember'])->name('uye.ekle');
                Route::delete('/uye-cikar/{user}', [App\Http\Controllers\Admin\DisiplinKuruluController::class , 'removeMember'])->name('uye.cikar');
                Route::post('/toplanti', [App\Http\Controllers\Admin\DisiplinKuruluController::class , 'storeToplanti'])->name('toplanti.store');
                Route::get('/toplanti/{toplanti}', [App\Http\Controllers\Admin\DisiplinKuruluController::class , 'showToplanti'])->name('toplanti.show');
                Route::put('/toplanti/{toplanti}', [App\Http\Controllers\Admin\DisiplinKuruluController::class , 'updateToplanti'])->name('toplanti.update');
                Route::patch('/toplanti/{toplanti}/durum', [App\Http\Controllers\Admin\DisiplinKuruluController::class , 'updateToplantiDurum'])->name('toplanti.durum');
                Route::delete('/toplanti/{toplanti}', [App\Http\Controllers\Admin\DisiplinKuruluController::class , 'destroyToplanti'])->name('toplanti.destroy');
            });

            // --- YENİ: HUKUK YETKİ MATRİSİ ---
            Route::middleware(['role:Superadmin|Hukuk Admini'])
                ->prefix('hukuk-yetki-matrisi')
                ->name('hukuk-matrisi.')
                ->group(function () {
                    Route::get('/', [App\Http\Controllers\Admin\HukukYetkiController::class, 'index'])->name('index');
                    Route::post('/update/{user}', [App\Http\Controllers\Admin\HukukYetkiController::class, 'update'])->name('update');
                });

            // Liste
            Route::get('/', [App\Http\Controllers\Admin\DisciplinaryController::class , 'index'])->name('index');

            // --- YENİ: DİSİPLİN RAPORU ---
            Route::get('/raporlar', [App\Http\Controllers\Admin\DisiplinRaporController::class , 'index'])
                ->name('report')
                ->middleware(['role:Superadmin|Yonetim|Bölüm Lideri|Hukuk Admini|Hukuk Yöneticisi|Disiplin Kurulu Başkanı|Disiplin Kurulu Üyesi']);

            // Yeni Tutanak (Controller içinde yetki kontrolü var, herkes oluşturamaz)
            Route::get('/yeni', [App\Http\Controllers\Admin\DisciplinaryController::class , 'create'])->name('create');
            Route::post('/kaydet', [App\Http\Controllers\Admin\DisciplinaryController::class , 'store'])->name('store');

            // Tutanak Sorumlusu Atama Ekranı
            Route::get('/sorumlu-yonetimi', [App\Http\Controllers\Admin\DisiplinSorumlusuController::class , 'index'])->name('sorumlular.index');
            Route::post('/sorumlu-yonetimi/{user}', [App\Http\Controllers\Admin\DisiplinSorumlusuController::class , 'update'])->name('sorumlular.update');

            // --- YENİ EKLENEN ROTALAR (SİLME & YORUM) ---
    
            // Tutanak Silme (Controller içinde Matris Kontrolü var)
            Route::delete('/{case}', [App\Http\Controllers\Admin\DisciplinaryController::class , 'destroy'])->name('destroy');

            // Yorum Ekleme ve Silme
            Route::post('/{case}/yorum-yap', [App\Http\Controllers\Admin\DisciplinaryController::class , 'storeComment'])->name('comment.store');
            Route::put('/yorum-duzenle/{comment}', [App\Http\Controllers\Admin\DisciplinaryController::class , 'updateComment'])->name('comment.update');
            Route::delete('/yorum-sil/{comment}', [App\Http\Controllers\Admin\DisciplinaryController::class , 'destroyComment'])->name('comment.destroy');

            // ---------------------------------------------
    
            // Detay Görüntüleme
            Route::get('/{id}', [App\Http\Controllers\Admin\DisciplinaryController::class , 'show'])->name('show');
            Route::get('/{id}/yazdir', [App\Http\Controllers\Admin\DisciplinaryController::class , 'print'])->name('print');
            Route::get('/{id}/pdf', [App\Http\Controllers\Admin\DisciplinaryController::class , 'downloadPdf'])->name('download-pdf');

            // Düzenleme
            Route::get('/{case}/duzenle', [App\Http\Controllers\Admin\DisciplinaryController::class , 'edit'])->name('edit');
            Route::put('/{case}', [App\Http\Controllers\Admin\DisciplinaryController::class , 'update'])->name('update');

        }
             );

        // =================================================================
        // 2. KARAR MERCİİ (Hukuk ve Yönetim)
        // =================================================================
        // Kimler: SADECE Hukuk Yöneticisi, Hukuk Admini ve Superadmin.
        // DİKKAT: Bölüm Lideri ve Kurul Başkanı BURAYA GİREMEZ.
        Route::middleware(['role:Superadmin|Hukuk Yöneticisi|Hukuk Admini|Disiplin Kurulu Başkanı'])
            ->prefix('disiplin')
            ->name('disiplin.')
            ->group(function () {
            // Oylama Başlatma (SADECE BAŞKAN / SUPERADMIN)
            Route::post('/{case}/oylama-baslat', [App\Http\Controllers\Admin\DisciplinaryController::class , 'startVoting'])->name('voting.start');
        }
             );

        // =================================================================
        // 3. DİSİPLİN KURULU İŞLEMLERİ (Oy Kullanma)
        // =================================================================
        // Kimler: Kurul Üyeleri, Başkan ve Üst Yönetim
        Route::middleware(['role:Superadmin|Hukuk Yöneticisi|Hukuk Admini|Disiplin Kurulu Başkanı|Disiplin Kurulu Üyesi'])
            ->prefix('disiplin')
            ->name('disiplin.')
            ->group(function () {

            // Oy Kullanma
            Route::post('/{case}/oy-kullan', [App\Http\Controllers\Admin\DisciplinaryController::class , 'saveVote'])->name('vote.save');
            Route::delete('/{case}/oy-sil', [App\Http\Controllers\Admin\DisciplinaryController::class , 'deleteVote'])->name('vote.delete');

            // Kritik Karar Butonları
            Route::post('/{case}/cezayi-onayla', [App\Http\Controllers\Admin\DisciplinaryController::class , 'approvePenalty'])->name('penalty.approve');
            Route::post('/{case}/savunmayi-kabul-et', [App\Http\Controllers\Admin\DisciplinaryController::class , 'acceptDefense'])->name('defense.accept');
            Route::post('/{case}/kurula-sevk', [App\Http\Controllers\Admin\DisciplinaryController::class , 'sendToBoard'])->name('board.send');
            Route::post('/{case}/karari-geri-al', [App\Http\Controllers\Admin\DisciplinaryController::class , 'revokeDecision'])->name('decision.revoke');
        }
             );



        // =================================================================
        // DİSİPLİN AYARLARI (URL: /admin/disiplin-ayarlari)
        // =================================================================
        Route::middleware(['role:Superadmin|Hukuk Admini'])
            ->prefix('disiplin-ayarlari') // Başında 'admin/' YOK
            ->name('disiplin.settings.') // Başında 'admin.' YOK (Otomatik eklenir)
            ->group(function () {

            Route::get('/', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'index'])->name('index');

            // Kategoriler
            Route::post('/kategori', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'storeCategory'])->name('category.store');
            Route::put('/kategori/{category}', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'updateCategory'])->name('category.update'); // <-- BU EKSİKTİ
            Route::delete('/kategori/{category}', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'deleteCategory'])->name('category.delete');
            // Etki
            Route::post('/etki', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'storeImpact'])->name('impact.store');
            Route::put('/etki/{impact}', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'updateImpact'])->name('impact.update'); // <-- BU
            Route::delete('/etki/{impact}', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'deleteImpact'])->name('impact.delete');

            // Kapsam
            Route::post('/kapsam', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'storeScope'])->name('scope.store');
            Route::put('/kapsam/{scope}', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'updateScope'])->name('scope.update'); // <-- BU
            Route::delete('/kapsam/{scope}', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'deleteScope'])->name('scope.delete');

            // Suçlar
            Route::post('/davranis', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'storeBehavior'])->name('behavior.store');
            Route::put('/davranis/{behavior}', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'updateBehavior'])->name('behavior.update');
            Route::delete('/davranis/{behavior}', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'deleteBehavior'])->name('behavior.delete');

            // Hesaplama
            Route::post('/katsayi', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'storeMultiplier'])->name('multiplier.store');
            Route::post('/skala', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'storeScale'])->name('scale.store');
            Route::put('/skala/{scale}', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'updateScale'])->name('scale.update');
            Route::delete('/skala/{scale}', [App\Http\Controllers\Admin\DisciplinarySettingsController::class , 'deleteScale'])->name('scale.delete');
        }
             );


        // =================================================================
        // === ARABULUCULUK YÖNETİMİ (Tam ve Eksiksiz Rotalar) ===
        // =================================================================
        // 1. TANIMLAMALAR (Hukuk Menüsü - /admin/arabuluculuk/tanimlar/...)
        // Prefix zaten 'admin' olduğu için buraya 'arabuluculuk/tanimlar' yazıyoruz.
        Route::prefix('arabuluculuk/tanimlar')->name('arabuluculuk.tanim.')->group(function () {
            Route::get('/anlasma-maddeleri', [ArabuluculukTanimController::class , 'anlasmaMaddeleri'])->name('anlasmaMaddeleri');
            Route::post('/anlasma-maddeleri', [ArabuluculukTanimController::class , 'storeMadde'])->name('storeMadde');
            Route::put('/anlasma-maddeleri/{id}', [ArabuluculukTanimController::class , 'updateMadde'])->name('updateMadde');
            Route::delete('/anlasma-maddeleri/{id}', [ArabuluculukTanimController::class , 'destroyMadde'])->name('destroyMadde');
            Route::get('/anlasma-maddeleri/loglar', [ArabuluculukTanimController::class , 'showAllLogs'])->name('showAllLogs');
        }
         );


        Route::prefix('arabuluculuk')->name('arabuluculuk.')->group(function () {

            // 1. Temel CRUD İşlemleri (Liste, Yeni Ekleme, Kaydetme, Detay)
            Route::get('/', [ArabuluculukController::class , 'index'])->name('index');
            Route::get('/create', [ArabuluculukController::class , 'create'])->name('create');
            Route::post('/', [ArabuluculukController::class , 'store'])->name('store');
            Route::get('/{case}', [ArabuluculukController::class , 'show'])->name('show');

            // 2. Düzenleme İşlemleri
            Route::get('/{case}/edit', [ArabuluculukController::class , 'edit'])->name('edit');
            Route::put('/{case}', [ArabuluculukController::class , 'update'])->name('update');

            // 3. Dosya Yükleme (HATA VEREN KISIM BURASIYDI, DÜZELTİLDİ)
            Route::post('/{case}/upload-file', [ArabuluculukController::class , 'uploadFile'])->name('uploadFile');

            // Dosya Silme Rotası
            Route::delete('/file/{file}', [ArabuluculukController::class , 'deleteFile'])->name('deleteFile');

            Route::post('/{case}/revert', [ArabuluculukController::class , 'revertStatus'])->name('revertStatus');

            // 4. Durum ve Atama İşlemleri
            Route::patch('/{case}/status', [ArabuluculukController::class , 'changeStatus'])->name('updateStatus');
            Route::post('/{case}/decision', [ArabuluculukController::class , 'submitDecision'])->name('submitDecision');
            Route::patch('/{case}/assign-mediator', [ArabuluculukController::class , 'assignMediator'])->name('assignMediator');

            // 5. Kurul Değerlendirmesi (Yeni)
            Route::post('/{case}/add-comment', [ArabuluculukController::class , 'addComment'])->name('addComment');

            // 6. Ödeme İşlemleri (Yeni - Word Dosyasından Gelen)
            Route::post('/{case}/save-payment', [ArabuluculukController::class , 'savePayment'])->name('savePayment');
            Route::post('/{case}/approve-payment', [ArabuluculukController::class , 'approvePayment'])->name('approvePayment');

            // 7. Personel için Süreci Geri Alma (Ödeme -> Arabulucuya)
            Route::post('/{case}/revert-mediation', [ArabuluculukController::class , 'revertToMediation'])->name('revertToMediation');

            // 8. Finans için Ödemeyi Reddetme
            Route::post('/{case}/reject-payment', [ArabuluculukController::class , 'rejectPayment'])->name('rejectPayment');

            // 9. Son Onay ve Dosya Kapatma
            Route::post('/{case}/final-close', [ArabuluculukController::class , 'finalClose'])->name('finalClose');
        }
         );

        // prefix => 'admin' ZATEN ÜST GRUPTA VAR, BURADA TEKRAR YAZMAYIN
        Route::group(['middleware' => ['role:Superadmin|Hukuk Admini|Hukuk Yöneticisi|Arabuluculuk Personel']], function () {

            // --- LOG ROTASI (En Üste) ---
            Route::get('arabulucular/sistem-loglari', [ArabulucuController::class , 'logs'])
                ->name('arabulucular.logs')
                ->middleware('role:Superadmin'); // Sadece Superadmin
    
            // Arabulucu Durum Değiştirme (Aktif/Pasif)
            Route::patch('arabulucular/{arabulucu}/toggle-status', [ArabulucuController::class , 'toggleStatus'])
                ->name('arabulucular.toggleStatus');
            // 1. ARABULUCU YÖNETİMİ (CRUD)
            // URL: /admin/arabulucular
            Route::resource('arabulucular', ArabulucuController::class);

            // 2. DIŞ AVUKAT YÖNETİMİ
            // URL: /admin/dis-avukatlar
            // Route isimlerinde 'admin.' zaten üst gruptan geliyor, tekrar yazmaya gerek yok ama
            // sizin kodunuzda name() içinde elle 'admin.' verdiğiniz için çakışma olabilir.
            // Laravel resource veya name prefix kullanıldığında otomatik ekler.
    
            // En temiz haliyle şu şekilde tanımlayalım:
            Route::get('dis-avukatlar', [ExternalLawyerController::class , 'index'])->name('dis_avukatlar.index');
            Route::get('dis-avukatlar/create', [ExternalLawyerController::class , 'create'])->name('dis_avukatlar.create');
            Route::post('dis-avukatlar', [ExternalLawyerController::class , 'store'])->name('dis_avukatlar.store');
            Route::get('dis-avukatlar/{id}/edit', [ExternalLawyerController::class , 'edit'])->name('dis_avukatlar.edit');
            Route::put('dis-avukatlar/{id}', [ExternalLawyerController::class , 'update'])->name('dis_avukatlar.update');
            Route::delete('dis-avukatlar/{id}', [ExternalLawyerController::class , 'destroy'])->name('dis_avukatlar.destroy');
        }
         );
    }); // --- Admin prefix'inin sonu ---

// =================================================================
// 3. DİSİPLİN ORTAK ALAN (Tüm Personel Erişebilir)
// =================================================================
// Buraya "auth" middleware koyuyoruz, yani giriş yapmış herkes erişebilir.
// Ancak Controller içinde "Kendi dosyam mı?" kontrolü yapacağız.
Route::middleware(['auth'])
    ->prefix('disiplin')
    ->name('disiplin.')
    ->group(function () {

        // Detay Görüntüleme (Personel kendi dosyasını görebilmeli)
        // NOT: Yukarıdaki yönetici grubundan 'show' rotasını buraya taşıdık veya kopyaladık
        Route::get('/{case}', [App\Http\Controllers\Admin\DisciplinaryController::class , 'show'])->name('show');

        // Savunma Verme (YENİ ROTA BURADA OLMALI)
        Route::post('/{case}/savunma-ver', [App\Http\Controllers\Admin\DisciplinaryController::class , 'saveDefense'])->name('defense.store');
    });




// === GÖZLEMCİ (SHADOWING) ROTALARI ===
Route::middleware(['auth'])->group(function () {
    Route::post('/observer/start/{target}', [App\Http\Controllers\ObserverController::class , 'startShadowing'])->name('observer.start');
    Route::match (['get', 'post'], '/observer/stop', [App\Http\Controllers\ObserverController::class , 'stopShadowing'])->name('observer.stop');
    Route::post('/observer/add', [App\Http\Controllers\ObserverController::class , 'addObserver'])->name('observer.add');
    Route::delete('/observer/remove/{observer}', [App\Http\Controllers\ObserverController::class , 'removeObserver'])->name('observer.remove');
});

require __DIR__ . '/auth.php';
