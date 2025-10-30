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
use Illuminate\Support\Facades\Auth; // <-- Bu satırı ekliyoruz
// === YENİ EKLENDİ: Dosya Silme için Model ===
use App\Models\MusteriSikayetiDosyasi;
use App\Livewire\Admin\SikayetCozumGorevlerim;
// ===========================================
use App\Http\Controllers\PublicSikayetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Eğer kullanıcı giriş yapmışsa, onu doğrudan dashboard'a yönlendir.
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // Giriş yapmamışsa, hoşgeldin sayfasını göster.
    return app(App\Http\Controllers\WelcomeController::class)->index();
})->name('home');


// Misafir (Giriş Yapmayan) Kullanıcı Rotaları
Route::get('/oneri-yap', [GuestIaaController::class, 'create'])->name('guest.iaa.create');
Route::post('/oneri-yap', [GuestIaaController::class, 'store'])->name('guest.iaa.store');

// =============================================
// == PUBLIC MÜŞTERİ ŞİKAYET ROTALARI (GÜNCELLENDİ) ==
// =============================================

// Şikayet bildirim formunu göster (GET)
Route::get('/sikayet', [PublicSikayetController::class, 'create'])->name('public.sikayet.create'); // Güncellendi

// Şikayet bildirim formunu kaydet (POST)
Route::post('/sikayet', [PublicSikayetController::class, 'store'])->name('public.sikayet.store'); // Güncellendi

// Müşteri takip sayfasını göster (GET) - Token ile
Route::get('/sikayetler/{token}', [PublicSikayetController::class, 'show'])->name('public.sikayet.show'); // Güncellendi

// Müşteri takip sayfası için giriş denemesi (POST) - Token ile
Route::post('/sikayetler/{token}/login', [PublicSikayetController::class, 'guestLogin'])->name('public.sikayet.guestLogin'); // Güncellendi

// === YENİ EKLE ===
// Müşteri şikayetini düzenleme formunu göster (GET)
Route::get('/sikayetler/{token}/edit', [PublicSikayetController::class, 'edit'])->name('public.sikayet.edit');
// === SON ===

// Müşteri şikayetini güncelleme (PUT) - Token ile (Kilitlenmemişse)
Route::put('/sikayetler/{token}', [PublicSikayetController::class, 'update'])->name('public.sikayet.update'); // Güncellendi

// Müşteri geri bildirimini kaydet (POST) - Token ile
Route::post('/sikayetler/{token}/feedback', [PublicSikayetController::class, 'storeFeedback'])->name('public.sikayet.storeFeedback'); // Güncellendi

// =============================================

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

    // --- İAA MODÜLÜ ROTALARI ---
    Route::get('/havuz', [IaaController::class, 'havuz'])->name('iaa.havuz');
    Route::post('/iaa/{iaa}/takimla-talep-et', [IaaController::class, 'takimlaTalepEt'])->name('iaa.takimlaTalepEt');
    Route::get('/takim-projeleri', [IaaController::class, 'takimProjeleri'])->name('iaa.takimProjeleri');
    // === YENİ ŞİKAYET RAPOR ROTASI ===
    Route::get('/musteri-sikayet-raporlari', [RaporController::class, 'sikayetRaporlari'])
    ->name('admin.sikayet-raporlari.index'); 
    // === ROTANIN SONU ===

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
    Route::get('/proje-calisma-alani/{iaa}', [ProjectWorkspaceController::class, 'show'])->name('proje.workspace.show');
    Route::post('/proje-calisma-alani/{assignment_id}/adim/{step_id}', [ProjectWorkspaceController::class, 'storeStep'])->name('proje.workspace.storeStep');
    Route::post('/proje-calisma-alani/adim/{progress_update}/yeniden-ac', [ProjectWorkspaceController::class, 'reopenStep'])->name('proje.workspace.reopenStep');
});

// =================================================================
// YÖNETİCİ (ADMIN) PANELİ ROTALARI
// =================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Bölüm Yönetimi
    Route::resource('bolumler', BolumController::class)->parameters(['bolumler' => 'bolum']);

    // Kullanıcı Yönetimi
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{user}/onayla', [UserController::class, 'onayla'])->name('users.onayla');

    // İAA Yönetimi
    Route::get('iaa-yonetim', [IaaYonetimController::class, 'index'])->name('iaa-yonetim.index');
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

    // ================================================================
    // ===== DÜZENLEME BURADA YAPILDI =================================
    // ================================================================
    Route::post('iaa-yonetim/{iaa}/update-status', [IaaYonetimController::class, 'updateStatus'])->name('iaa-yonetim.updateStatus');
    Route::post('iaa/{iaa}/approve-completed', [IaaYonetimController::class, 'approveCompleted'])->name('iaa.approveCompleted');
    Route::post('iaa/{iaa}/reject-completed', [IaaYonetimController::class, 'rejectCompleted'])->name('iaa.rejectCompleted');
    Route::post('iaa/{iaa}/request-revision', [IaaYonetimController::class, 'requestRevision'])->name('iaa.requestRevision');
    // ================================================================

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

    // RAPORLAMA MODÜLÜ
    Route::get('raporlar', [RaporController::class, 'index'])->name('raporlar.index');
    Route::get('raporlar/excel', [RaporController::class, 'exportExcel'])->name('raporlar.exportExcel');
    Route::get('raporlar/pdf', [RaporController::class, 'exportPdf'])->name('raporlar.exportPdf');

    // Müşteri Şikayetleri Yönetimi
    Route::resource('sikayetler', App\Http\Controllers\Admin\SikayetController::class)
        ->names('sikayetler')
        ->parameters(['sikayetler' => 'sikayet']);


    // Şikayet Kategorileri Yönetimi
    Route::resource('sikayet-kategorileri', \App\Http\Controllers\Admin\SikayetKategoriController::class)
        ->parameters(['sikayet-kategorileri' => 'sikayetKategori']) // Bu satırı ekleyin
        ->except(['show']);

    // ÇÖZÜM TAKIMLARI ROTASI (GÜNCELLENDİ: Parameter adı)
    Route::resource('cozum-takimlari', \App\Http\Controllers\Admin\CozumTakimiController::class)
        ->parameters(['cozum-takimlari' => 'cozumTakimi']); // Parametre adını Controller ile uyumlu hale getir

    // === YENİ EKLENDİ: Çözüm Görevlerim Sayfası ===
    Route::get('/sikayet-cozum-gorevlerim', SikayetCozumGorevlerim::class)
        ->name('sikayet-cozum-gorevlerim.index'); 

   


});

require __DIR__.'/auth.php';

