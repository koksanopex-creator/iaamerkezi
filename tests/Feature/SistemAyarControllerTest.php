<?php

namespace Tests\Feature;

use App\Models\Bolum;
use App\Models\Iaa;
use App\Models\IaaLog;
use App\Models\MusteriSikayeti;
use App\Models\MusteriSikayetiLog;
use App\Models\Setting;
use App\Models\SikayetKategori;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DirektorBekleyenProjelerNotification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SistemAyarControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $director;
    protected $bolum;
    protected $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles using the standard seeder
        $this->seed(RolesSeeder::class);

        // Create Superadmin user
        $this->admin = User::factory()->create(['name' => 'Admin User', 'is_personnel' => true]);
        $this->admin->assignRole('Superadmin');

        // Create Director user
        $this->director = User::factory()->create(['name' => 'Director User', 'is_personnel' => true]);
        $this->director->assignRole('Direktör');

        // Create Bolum & SikayetKategori
        $this->bolum = Bolum::create([
            'ad' => 'Test Bölümü',
            'director_id' => $this->director->id,
        ]);

        $this->kategori = SikayetKategori::create([
            'ad' => 'Test Kategori',
            'bolum_id' => $this->bolum->id,
        ]);
    }

    /** @test */
    public function it_transitions_active_customer_complaint_projects_to_director_approval_when_setting_is_enabled()
    {
        Notification::fake();

        // Set setting to disabled initially
        Setting::updateOrCreate(['key' => 'sikayet_direktor_onayi_aktif'], ['value' => '0']);

        // Create a customer complaint project in 'Yönetici Onayı Bekliyor' status
        $project = Iaa::create([
            'baslik' => 'Test Şikayet Projesi',
            'mevcut_durum' => 'Mevcut durum açıklaması',
            'bolum_id' => $this->bolum->id,
            'durum' => 'Yönetici Onayı Bekliyor',
        ]);

        // Create the linked customer complaint
        $complaint = MusteriSikayeti::create([
            'iaa_id' => $project->id,
            'sikayet_kategorisi_id' => $this->kategori->id,
            'musteri_durum' => 'Yönetici Onayı Bekliyor',
            'musteri_adi' => 'Test Müşteri',
            'musteri_sikayet_konusu' => 'Test Şikayet Konusu',
            'musteri_sikayet_detayi' => 'Test Detayı',
            'musteri_sikayet_tarihi' => now(),
        ]);

        // Verify initial status
        $this->assertEquals('Yönetici Onayı Bekliyor', $project->fresh()->durum);
        $this->assertEquals('Yönetici Onayı Bekliyor', $complaint->fresh()->musteri_durum);

        // Toggle the setting on (checkbox selected)
        $response = $this->actingAs($this->admin)
            ->from(route('admin.sistem-ayarlari.index'))
            ->post(route('admin.sistem-ayarlari.update'), [
                'sikayet_direktor_onayi_aktif' => '1',
            ]);

        $response->assertRedirect(route('admin.sistem-ayarlari.index'));

        // Assert statuses are updated
        $this->assertEquals('Direktör Onayı Bekliyor', $project->fresh()->durum);
        $this->assertEquals('Direktör Onayı Bekliyor', $complaint->fresh()->musteri_durum);

        // Assert logs were created
        $this->assertTrue(IaaLog::where('iaa_id', $project->id)
            ->where('eylem', 'Durum Güncellendi')
            ->where('aciklama', 'like', '%Direktör Onay Süreci aktif edildi%')
            ->exists());

        $this->assertTrue(MusteriSikayetiLog::where('musteri_sikayeti_id', $complaint->id)
            ->where('eylem', 'Durum Güncellendi')
            ->where('aciklama', 'like', '%Direktör Onay Süreci aktif edildi%')
            ->exists());

        // Assert notification was sent to director
        Notification::assertSentTo(
            $this->director,
            DirektorBekleyenProjelerNotification::class,
            function ($notification) use ($project) {
                return str_contains($notification->toDatabase($this->director)['message'] ?? '', $project->baslik);
            }
        );
    }

    /** @test */
    public function it_transitions_active_customer_complaint_projects_to_manager_approval_when_setting_is_disabled()
    {
        Notification::fake();

        // Set setting to enabled initially
        Setting::updateOrCreate(['key' => 'sikayet_direktor_onayi_aktif'], ['value' => '1']);

        // Create a customer complaint project in 'Direktör Onayı Bekliyor' status
        $project = Iaa::create([
            'baslik' => 'Test Şikayet Projesi 2',
            'mevcut_durum' => 'Mevcut durum açıklaması',
            'bolum_id' => $this->bolum->id,
            'durum' => 'Direktör Onayı Bekliyor',
        ]);

        // Create the linked customer complaint
        $complaint = MusteriSikayeti::create([
            'iaa_id' => $project->id,
            'sikayet_kategorisi_id' => $this->kategori->id,
            'musteri_durum' => 'Direktör Onayı Bekliyor',
            'musteri_adi' => 'Test Müşteri',
            'musteri_sikayet_konusu' => 'Test Şikayet Konusu 2',
            'musteri_sikayet_detayi' => 'Test Detayı 2',
            'musteri_sikayet_tarihi' => now(),
        ]);

        // Verify initial status
        $this->assertEquals('Direktör Onayı Bekliyor', $project->fresh()->durum);
        $this->assertEquals('Direktör Onayı Bekliyor', $complaint->fresh()->musteri_durum);

        // Toggle the setting off (checkbox not in request)
        $response = $this->actingAs($this->admin)
            ->from(route('admin.sistem-ayarlari.index'))
            ->post(route('admin.sistem-ayarlari.update'), []);

        $response->assertRedirect(route('admin.sistem-ayarlari.index'));

        // Assert statuses are updated
        $this->assertEquals('Yönetici Onayı Bekliyor', $project->fresh()->durum);
        $this->assertEquals('Yönetici Onayı Bekliyor', $complaint->fresh()->musteri_durum);

        // Assert logs were created
        $this->assertTrue(IaaLog::where('iaa_id', $project->id)
            ->where('eylem', 'Durum Güncellendi')
            ->where('aciklama', 'like', '%Direktör Onay Süreci kapatıldı%')
            ->exists());

        $this->assertTrue(MusteriSikayetiLog::where('musteri_sikayeti_id', $complaint->id)
            ->where('eylem', 'Durum Güncellendi')
            ->where('aciklama', 'like', '%Direktör Onay Süreci kapatıldı%')
            ->exists());
    }
}
