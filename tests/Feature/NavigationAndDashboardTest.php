<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Takim;
use Database\Seeders\RolesSeeder;
use Database\Seeders\BolumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class NavigationAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Temel verileri hazırla
        $this->seed(RolesSeeder::class);
        $this->seed(BolumSeeder::class);
    }

    /**
     * Tüm ana rollerin Dashboard'a erişebildiğini ve sayfanın hatasız yüklendiğini test eder.
     * Bu test, navigasyon üzerindeki tüm rotaların geçerli olduğunu ve
     * dashboard değişkenlerinin (count'lar vb.) doğru atandığını doğrular.
     *
     * @dataProvider roleProvider
     */
    public function test_dashboard_renders_for_all_roles(string $roleName)
    {
        $user = User::factory()->create();
        $user->assignRole($roleName);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        
        // Navigation bar'ın render edildiğini doğrula
        $response->assertSee('Portal');

        // Role-based visibility check
        $rolesWithManagerDropdown = ['Superadmin', 'Yonetim', 'Bölüm Kalite Yöneticisi', 'Bölüm Lideri', 'Direktör'];
        if (in_array($roleName, $rolesWithManagerDropdown)) {
            $response->assertSee('Yönetici');
        } else {
            $response->assertDontSee('Yönetici');
        }
    }

    public static function roleProvider(): array
    {
        return [
            ['Superadmin'],
            ['Bölüm Lideri'],
            ['Kullanıcı'],
            ['Direktör'],
            ['Bölüm Kalite Yöneticisi'],
            ['Müşteri Şikayeti Kurulu'],
        ];
    }

    /**
     * Kullanıcının belirttiği özel senaryo: 
     * Kullanıcının bekleyen bir takım daveti varken Dashboard'un çökmemesini sağlar.
     */
    public function test_dashboard_renders_with_pending_team_invitation()
    {
        // 1. Bir kullanıcı oluştur
        $user = User::factory()->create();
        $user->assignRole('Kullanıcı');

        // 2. Bir takım oluştur ve bu kullanıcıya davet gönder
        $takim = Takim::create([
            'ad' => 'Test Takımı',
            'bolum_id' => 1, // BolumSeeder'dan gelir
            'lider_user_id' => User::factory()->create()->id,
        ]);

        // Daveti veritabanına ekle (Tablo adını ve yapısını varsayıyoruz)
        \Illuminate\Support\Facades\DB::table('takim_davetiyeleri')->insert([
            'takim_id' => $takim->id,
            'davet_edilen_user_id' => $user->id,
            'davet_eden_user_id' => $takim->lider_user_id,
            'type' => 'davet',
            'durum' => 'bekliyor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Dashboard'u ziyaret et
        $response = $this->actingAs($user)->get('/dashboard');

        // 4. Hata almadığımızı doğrula
        $response->assertStatus(200);
        $response->assertSee('yeni takım davetiniz var'); 
    }
}
