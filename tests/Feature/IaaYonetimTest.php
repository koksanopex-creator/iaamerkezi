<?php

namespace Tests\Feature;

use App\Models\Bolum;
use App\Models\Iaa;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IaaYonetimTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $bolum;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);

        // Create a Superadmin user
        $this->admin = User::factory()->create(['name' => 'Admin User', 'is_personnel' => true]);
        $this->admin->assignRole('Superadmin');

        // Create a Bölüm
        $this->bolum = Bolum::create([
            'ad' => 'Test Bölümü',
        ]);
    }

    /** @test */
    public function it_approves_an_iaa_with_custom_scoring_horizon_and_calculates_correct_score()
    {
        // 1. Create an IAA waiting for approval
        $iaa = Iaa::create([
            'baslik' => 'Test İAA Önerisi',
            'mevcut_durum' => 'Eski Durum',
            'oneri => İyileştirme Önerisi',
            'bolum_id' => $this->bolum->id,
            'durum' => 'Onay Bekliyor',
        ]);

        // 2. Approve it with custom values and yil_baz = 10
        $response = $this->actingAs($this->admin)->patch(route('admin.iaa-yonetim.onayla', $iaa), [
            'risk' => 4,
            'kazanc_miktar' => 50000,
            'kazanc_birim' => 'TL',
            'butce_miktar' => 200000,
            'butce_birim' => 'TL',
            'yil_baz' => 10,
        ]);

        $response->assertRedirect(route('admin.iaa-yonetim.index'));

        // 3. Verify it is saved in database correctly
        $freshIaa = $iaa->fresh();
        $this->assertEquals('Havuzda', $freshIaa->durum);
        $this->assertEquals(4, $freshIaa->risk);
        $this->assertEquals(50000, $freshIaa->kazanc_miktar);
        $this->assertEquals(200000, $freshIaa->butce_miktar);
        $this->assertEquals(10, $freshIaa->yil_baz);
        
        // Puan = round((risk * kazanc_miktar * yil_baz) / butce_miktar)
        // Puan = round((4 * 50000 * 10) / 200000) = round(2000000 / 200000) = 10
        $this->assertEquals(10, $freshIaa->puan);
    }

    /** @test */
    public function it_updates_the_score_of_an_approved_iaa_with_new_custom_scoring_horizon()
    {
        // 1. Create a Havuzda status IAA
        $iaa = Iaa::create([
            'baslik' => 'Approved İAA',
            'mevcut_durum' => 'Mevcut Durum',
            'bolum_id' => $this->bolum->id,
            'durum' => 'Havuzda',
            'risk' => 3,
            'kazanc_miktar' => 10000,
            'butce_miktar' => 20000,
            'yil_baz' => 5,
            'puan' => 15,
        ]);

        // 2. Update its score with yil_baz = 2
        $response = $this->actingAs($this->admin)->patch(route('admin.iaa-yonetim.updateScore', $iaa), [
            'risk' => 5,
            'kazanc_miktar' => 30000,
            'kazanc_birim' => 'TL',
            'butce_miktar' => 50000,
            'butce_birim' => 'TL',
            'yil_baz' => 2,
        ]);

        $response->assertRedirect();

        // 3. Verify the updated values
        $freshIaa = $iaa->fresh();
        $this->assertEquals(5, $freshIaa->risk);
        $this->assertEquals(30000, $freshIaa->kazanc_miktar);
        $this->assertEquals(50000, $freshIaa->butce_miktar);
        $this->assertEquals(2, $freshIaa->yil_baz);
        
        // Puan = round((5 * 30000 * 2) / 50000) = round(300000 / 50000) = 6
        $this->assertEquals(6, $freshIaa->puan);
    }

    /** @test */
    public function it_uses_default_value_of_five_years_for_scoring_horizon_if_not_provided()
    {
        $iaa = Iaa::create([
            'baslik' => 'Test Default Horizon',
            'mevcut_durum' => 'Mevcut Durum',
            'bolum_id' => $this->bolum->id,
            'durum' => 'Onay Bekliyor',
        ]);

        // Request without yil_baz
        $response = $this->actingAs($this->admin)->patch(route('admin.iaa-yonetim.onayla', $iaa), [
            'risk' => 3,
            'kazanc_miktar' => 10000,
            'kazanc_birim' => 'TL',
            'butce_miktar' => 20000,
            'butce_birim' => 'TL',
        ]);

        $freshIaa = $iaa->fresh();
        $this->assertEquals(5, $freshIaa->yil_baz);
        
        // Puan = round((3 * 10000 * 5) / 20000) = round(150000 / 20000) = 8
        $this->assertEquals(8, $freshIaa->puan);
    }
}
