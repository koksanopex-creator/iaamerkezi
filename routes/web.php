<?php

// Gerekli tüm Controller'ları en üste ekliyoruz
use App\Http\Controllers\Admin\BolumController;
use App\Http\Controllers\Admin\IaaYonetimController;
use App\Http\Controllers\Admin\RaporController;
use App\Http\Controllers\Admin\SistemAyarController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TakimYonetimController;
use App\Http\Controllers\Admin\IaaWorkflowController;
use App\Http\Controllers\IaaController;
use App\Http\Controllers\ProfileController;
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

// === GÜVENLİK DÜZELTMESİ İÇİN EKLENDİ ===
use App\Http\Controllers\Admin\SikayetController;
use App\Http\Controllers\Admin\SikayetKategoriController;
use App\Http\Controllers\Admin\CozumTakimiController;
// === GÜVENLİK DÜZELTMESİ SONU ===


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return app(App\Http\Controllers\WelcomeController::class)->index();
})->name('home');


// Misafir (Giriş Yapmayan) Kullanıcı Rotaları
Route::get('/oneri-yap', [GuestIaaController::class, 'create'])->name('guest.iaa.create');
Route::post('/oneri-yap', [GuestIaaController::class, 'store'])
    ->middleware('throttle:10,1') // <-- BU SATIRI EKLEYİN
    ->name('guest.iaa.store');

// =============================================
// == PUBLIC MÜŞTERİ ŞİKAYET ROTALARI
// =============================================
Route::get('/sikayet', [PublicSikayetController::class, 'create'])->name('public.sikayet.create');
Route::post('/sikayet', [PublicSikayetController::class, 'store'])->name('public.sikayet.store');
Route::get('/sikayetler/{token}', [PublicSikayetController::class, 'show'])->name('public.sikayet.show'); 
Route::post('/sikayetler/{token}/login', [PublicSikayetController::class, 'guestLogin'])->name('public.sikayet.guestLogin'); 
Route::get('/sikayetler/{token}/edit', [PublicSikayetController::class, 'edit'])->name('public.sikayet.edit');
Route::put('/sikayetler/{token}', [PublicSikayetController::class, 'update'])->name('public.sikayet.update'); 
Route::post('/sikayetler/{token}/feedback', [PublicSikayetController::class, 'storeFeedback'])->name('public.sikayet.storeFeedback'); 

// =============================================

// =============================================
// === YENİ EKLEME (MİSAFİR PROJE ERİŞİMİ) ===
// Bu rotayı aşağıdaki 'auth' grubundan buraya taşıdık.
// Güvenlik, Controller'ın içinde (Adım 2B'de) sağlanacak.
Route::get('/proje-calisma-alani/{iaa}', [ProjectWorkspaceController::class, 'show'])->name('proje.workspace.show');
// === EKLEME SONU ===

// Alt Kategorileri getiren API rotası (Herkese açık)
Route::get('/api/get-alt-kategoriler/{kategori_id}', [SikayetKategoriController::class, 'getAltKategorilerApi'])->name('api.getAltKategoriler');

// Giriş yapmış tüm kullanıcılar için ortak alan
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['verified'])->name('dashboard');
    Route::get('/puan-durumu', [DashboardController::class, 'puanDurumu'])->name('puan-durumu');
    Route::get('/kullanici-puanlari/{user}', [DashboardController::class, 'kullaniciPuanlari'])->name('kullanici.puanlari');

    // Profil Yönetimi
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Kullanıcı Rehberi (Herkes Erişebilir)
    Route::get('/kullanici-listesi', [App\Http\Controllers\UserDirectoryController::class, 'index'])->name('user-directory.index');

    // Herkesin görebileceği Genel Profil Sayfası
    Route::get('/kullanici-profil/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/kullanici-profil/{user}/yorum', [ProfileController::class, 'storeComment'])->name('profile.comment.store');
    Route::delete('/kullanici-profil/yorum/{comment}', [ProfileController::class, 'destroyComment'])->name('profile.comment.destroy');
    
    // =================================================================
    // === YENİ EKLENEN KISIM: YÖNETİM KOKPİTİ ===
    // =================================================================
    // Sadece 'Superadmin' VEYA 'Yonetim' rolüne sahip olanlar görebilir.
    // URL: http://localhost:8000/yonetim
    Route::group(['middleware' => ['role:Superadmin|Yonetim']], function () {
        Route::get('/yonetim', [App\Http\Controllers\Admin\ExecutiveReportController::class, 'index'])
             ->name('yonetim.index');


             
    });
    // =================================================================
    
    // --- İAA MODÜLÜ ROTALARI ---
    Route::get('/havuz', [IaaController::class, 'havuz'])->name('iaa.havuz');
    Route::post('/iaa/{iaa}/takimla-talep-et', [IaaController::class, 'takimlaTalepEt'])->name('iaa.takimlaTalepEt');
    //Route::get('/takim-projeleri', [IaaController::class, 'takimProjeleri'])->name('iaa.takimProjeleri'); // Buradan taşındı
    Route::resource('iaa', IaaController::class);

    // --- TAKIM MODÜLÜ ROTALARI ---
    Route::post('takimlar/{takim}/davet-gonder', [TakimController::class, 'davetGonder'])->name('takimlar.davetGonder');
    Route::delete('takimlar/{takim}/uyeler/{user}', [TakimController::class, 'uyeCikar'])->name('takimlar.uyeCikar');
    Route::get('davetlerim', [TakimController::class, 'davetlerim'])->name('takimlar.davetlerim');
    Route::post('davetlerim/{davetiye}/kabul-et', [TakimController::class, 'davetiKabulEt'])->name('takimlar.davetiKabulEt');
    Route::post('davetlerim/{davetiye}/reddet', [TakimController::class, 'davetiReddet'])->name('takimlar.davetiReddet');
    Route::delete('davetlerim/{davetiye}/iptal-et', [TakimController::class, 'davetiIptalEt'])->name('takimlar.davetiIptalEt');
    Route::get('katilma-isteklerim', [TakimController::class, 'isteklerim'])->name('takimlar.isteklerim');
    Route::delete('katilma-isteklerim/{davetiye}', [TakimController::class, 'istegiGeriCek'])->name('takimlar.istegiGeriCek');
    Route::post('takimlar/{takim}/katilma-istegi', [TakimController::class, 'katilmaIstegiGonder'])->name('takimlar.katilmaIstegi');
    Route::post('takim-istekleri/{davetiye}/kabul-et', [TakimController::class, 'istekKabulEt'])->name('takimlar.istekKabulEt');
    Route::post('takim-istekleri/{davetiye}/reddet', [TakimController::class, 'istegiReddet'])->name('takimlar.istegiReddet');
    Route::resource('takimlar', TakimController::class)->parameters(['takimlar' => 'takim']);

    // Proje Çalışma Alanı Rotaları
    
    // === DEĞİŞİKLİK ===
    // GET rotası yukarı (public alana) taşındı.
    // Route::get('/proje-calisma-alani/{iaa}', [ProjectWorkspaceController::class, 'show'])->name('proje.workspace.show');
    // === DEĞİŞİKLİK SONU ===
    
    // Bu POST rotaları GİRİŞ YAPMAYI GEREKTİRİR (Erhan Cesur gibi), bu yüzden 'auth' içinde kalmalı
    Route::post('/proje-calisma-alani/{assignment_id}/adim/{step_id}', [ProjectWorkspaceController::class, 'storeStep'])->name('proje.workspace.storeStep');
    Route::post('/proje-calisma-alani/adim/{progress_update}/yeniden-ac', [ProjectWorkspaceController::class, 'reopenStep'])->name('proje.workspace.reopenStep');
    // VAZGEÇME ROTASI
    Route::post('/proje-calisma-alani/adim/{id}/vazgec', [App\Http\Controllers\ProjectWorkspaceController::class, 'cancelReopenStep'])
    ->name('proje.workspace.cancelReopenStep');

    // === YENİ: Takım Projelerim buraya taşındı ===
    Route::get('/takim-projeleri', [IaaController::class, 'takimProjeleri'])->name('iaa.takimProjeleri');
    
    // Adıma Sorumlu Atama Rotası
    Route::post('/proje-calisma-alani/{iaa}/adim/{step}/ata', [App\Http\Controllers\ProjectWorkspaceController::class, 'assignUserToStep'])
    ->name('proje.workspace.assignUserToStep');

    Route::get('/sikayet-gorevlerim', \App\Livewire\SikayetGorevlerim::class) 
      ->middleware('auth') 
      ->name('sikayet-gorevlerim.index');

    // Proje Davet Yanıt Rotaları
    Route::post('/proje-davet/{iaa}/yanit', [App\Http\Controllers\IaaController::class, 'davetYanitla'])->name('iaa.davetYanitla');

    // === BİLDİRİM SİSTEMİ API ROTALARI (BURAYA EKLEYİN) ===
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::post('/notifications/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

});

// =================================================================
// YÖNETİCİ (ADMIN) PANELİ ROTALARI
// =================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // =================================================================
    // === GÜVENLİK DÜZELTMESİ: SADECE SUPERADMIN ERİŞEBİLİR ===
    // =================================================================
    // Bu grup, 'Superadmin' rolüne sahip olmayan herkesi engelleyecektir.
    Route::middleware(['role:Superadmin'])->group(function () {
    
        // Bölüm Yönetimi
        Route::resource('bolumler', BolumController::class)->parameters(['bolumler' => 'bolum']);

        // Kullanıcı Yönetimi
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('users/{user}/onayla', [UserController::class, 'onayla'])->name('users.onayla');
        
        // BÖLÜM KALİTE YÖNETİCİSİ ATAMA
        Route::get('kalite-yoneticileri', [App\Http\Controllers\Admin\BolumKaliteYoneticisiController::class, 'index'])
            ->name('kalite-yoneticileri.index');
            
        Route::post('kalite-yoneticileri/{user}', [App\Http\Controllers\Admin\BolumKaliteYoneticisiController::class, 'update'])
            ->name('kalite-yoneticileri.update');

        // İAA Yönetimi

        Route::patch('iaa-yonetim/{iaa}/onayla', [IaaYonetimController::class, 'onayla'])->name('iaa-yonetim.onayla');
        Route::patch('iaa-yonetim/{iaa}/reddet', [IaaYonetimController::class, 'reddet'])->name('iaa-yonetim.reddet');
        Route::patch('iaa-yonetim/{iaa}/geri-al', [IaaYonetimController::class, 'geriAl'])->name('iaa-yonetim.geriAl');
        Route::delete('iaa-yonetim/{iaa}', [IaaYonetimController::class, 'destroy'])->name('iaa-yonetim.destroy');
        Route::post('iaa-yonetim/bulk-delete', [IaaYonetimController::class, 'bulkDestroy'])->name('iaa-yonetim.bulkDestroy');
        Route::get('iaa-yonetim/{iaa}/talepler', [IaaYonetimController::class, 'talepleriGoster'])->name('iaa-yonetim.talepleriGoster');
        Route::get('iaa-yonetim/{iaa}/takim/{takim}/ata', [IaaYonetimController::class, 'atamaFormuGoster'])->name('iaa-yonetim.atamaFormu');
        Route::post('iaa-yonetim/{iaa}/takim/{takim}/ata', [IaaYonetimController::class, 'atamaYap'])->name('iaa-yonetim.atamaYap');
        Route::get('iaa-yonetim/arsiv', [IaaYonetimController::class, 'arsiv'])->name('iaa-yonetim.arsiv');
        Route::patch('iaa-yonetim/{iaa}/update-score', [IaaYonetimController::class, 'updateScore'])->name('iaa-yonetim.updateScore');
        Route::get('iaa-yonetim/{iaa}/reassign', [IaaYonetimController::class, 'reassignForm'])->name('iaa-yonetim.reassignForm');
        Route::patch('iaa-yonetim/{iaa}/reassign', [IaaYonetimController::class, 'reassignUpdate'])->name('iaa-yonetim.reassignUpdate');
        Route::post('iaa-yonetim/{iaa}/update-status', [IaaYonetimController::class, 'updateStatus'])->name('iaa-yonetim.updateStatus');
        Route::post('iaa/{iaa}/approve-completed', [IaaYonetimController::class, 'approveCompleted'])->name('iaa.approveCompleted');
        Route::post('iaa/{iaa}/reject-completed', [IaaYonetimController::class, 'rejectCompleted'])->name('iaa.rejectCompleted');
        Route::post('iaa/{iaa}/request-revision', [IaaYonetimController::class, 'requestRevision'])->name('iaa.requestRevision');
        
        // TAKIM YÖNETİMİ
        Route::post('takim-yonetim/{takim}/uye-ekle', [TakimYonetimController::class, 'uyeEkle'])->name('takim-yonetim.uyeEkle');
        Route::delete('takim-yonetim/{takim}/uye-cikar/{user}', [TakimYonetimController::class, 'uyeCikar'])->name('takim-yonetim.uyeCikar');
        Route::post('takim-yonetim/{takim}/proje-ata', [TakimYonetimController::class, 'projeAta'])->name('takim-yonetim.projeAta');
        Route::resource('takim-yonetim', TakimYonetimController::class)->parameters(['takim-yonetim' => 'takim']);

        // AKIŞ ŞABLONLARI YÖNETİMİ
        Route::resource('workflows', IaaWorkflowController::class);
        Route::get('workflows/{workflow}/steps', [IaaWorkflowController::class, 'editSteps'])->name('workflows.editSteps');
        Route::post('workflows/{workflow}/steps', [IaaWorkflowController::class, 'storeStep'])->name('workflows.storeStep');
        Route::put('workflows/steps/{step}', [IaaWorkflowController::class, 'updateStep'])->name('workflows.updateStep');
        Route::delete('workflows/steps/{step}', [IaaWorkflowController::class, 'destroyStep'])->name('workflows.destroyStep');

        // SİSTEM AYARLARI
        Route::get('sistem-ayarlari', [SistemAyarController::class, 'index'])->name('sistem-ayarlari.index');
        Route::post('sistem-ayarlari', [SistemAyarController::class, 'update'])->name('sistem-ayarlari.update');

        

        // Şikayet KATEGORİ ve ÇÖZÜM TAKIMI Yönetimi (Sadece Superadmin)
        Route::resource('sikayet-kategorileri', SikayetKategoriController::class)
            ->parameters(['sikayet-kategorileri' => 'sikayetKategori']) 
            ->except(['show']);

        // === BURAYA EKLENDİ: ALT KATEGORİ YÖNETİMİ ===
        Route::post('sikayet-kategorileri/{sikayetKategori}/alt-kategori', [SikayetKategoriController::class, 'storeAltKategori'])
            ->name('sikayet-kategorileri.alt-kategori.store');
        
        Route::delete('sikayet-alt-kategori/{altKategori}', [SikayetKategoriController::class, 'destroyAltKategori'])
            ->name('sikayet-alt-kategori.destroy');

        Route::put('sikayet-alt-kategori/{altKategori}', [App\Http\Controllers\Admin\SikayetKategoriController::class, 'updateAltKategori'])
        ->name('sikayet-alt-kategori.update');
        // =================================================

        Route::resource('cozum-takimlari', CozumTakimiController::class)
            ->parameters(['cozum-takimlari' => 'cozumTakimi']);

     
    
    }); // --- Superadmin grubunun sonu ---

    // =============================================================
    // GRUP: RAPORLAR (Superadmin ve Yönetim Görebilir)
    // =============================================================
    Route::middleware(['role:Superadmin|Yonetim'])->group(function () {
        Route::get('raporlar', [RaporController::class, 'index'])->name('raporlar.index');
        Route::get('raporlar/excel', [RaporController::class, 'exportExcel'])->name('raporlar.exportExcel');
        Route::get('raporlar/pdf', [RaporController::class, 'exportPdf'])->name('raporlar.exportPdf');
    });

    Route::get('iaa-yonetim', [IaaYonetimController::class, 'index'])->name('iaa-yonetim.index');
    Route::post('iaa-yonetim/{iaa}/bolum-onayi', [IaaYonetimController::class, 'bolumOnayiVer'])
            ->name('iaa-yonetim.bolumOnayiVer');
    Route::post('iaa-yonetim/{iaa}/bolum-revizyon', [IaaYonetimController::class, 'bolumRevizyonIste'])->name('iaa-yonetim.bolumRevizyon');
    Route::post('iaa-yonetim/{iaa}/bolum-red', [IaaYonetimController::class, 'bolumReddet'])->name('iaa-yonetim.bolumReddet');
    Route::post('iaa-yonetim/{iaa}/bolum-onayi-geri-al', [IaaYonetimController::class, 'bolumOnayiGeriAl'])
    ->name('iaa-yonetim.bolumOnayiGeriAl');    

    // =================================================================
    // === MÜŞTERİ ŞİKAYETLERİ MODÜLÜ (İlgili Roller Erişebilir) ===
    // =================================================================
    // Bu rotalar, 'Superadmin' OLMAYAN ama 'Müşteri Şikayeti Kurulu' gibi
    // rollere sahip kişilerin erişmesi gereken yerlerdir.
    // Bu rotaların kendi Controller'ları içinde (örn: SikayetController@index) 
    // $this->authorize(...) ile zaten korunduğunu varsayıyoruz.
    
    // Şikayet Raporları (Canlı)
    Route::get('musteri-sikayet-raporlari', [RaporController::class, 'sikayetRaporlari'])
        ->name('sikayet-raporlari.index');
    // Tüm Şikayetler Listesi
    Route::get('musteri-sikayet-raporlari/tum-liste', [RaporController::class, 'tumSikayetListesi'])
        ->name('sikayet-raporlari.tum-liste');

    // Kurul Girdileri
    Route::get('sikayetler/kurul-girdileri', [SikayetController::class, 'kurulGirdileri'])
        ->name('sikayetler.kurulGirdileri');
    
    // Müşteri Şikayetleri Yönetimi (CRUD)
    Route::resource('sikayetler', SikayetController::class)
        ->names('sikayetler')
        ->parameters(['sikayetler' => 'sikayet']);

}); // --- Admin prefix'inin sonu ---



require __DIR__.'/auth.php';