<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesSeeder;
use Database\Seeders\BolumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleHealthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(RolesSeeder::class);
        $this->seed(BolumSeeder::class);
    }

    /**
     * Tüm ana modüllerin (Kullanıcılar, İAA, Şikayet, Disiplin) 
     * liste sayfalarının her rol için hatasız yüklendiğini kontrol eder.
     *
     * @dataProvider moduleRouteProvider
     */
    public function test_module_index_pages_render_without_errors(string $roleName, string $routeName)
    {
        $user = User::factory()->create();
        $user->assignRole($roleName);

        // Not: Bazı rollerin bazı sayfalara yetkisi olmayabilir (403). 
        // 500 hatası almadığımız sürece başarılı sayacağız (çökme yok).
        $response = $this->actingAs($user)->get(route($routeName));

        // 500 (Server Error) alınmadığını doğrula
        $this->assertNotEquals(500, $response->getStatusCode(), "Route '$routeName' crashed for role '$roleName'");
    }

    public static function moduleRouteProvider(): array
    {
        $routes = [
            'admin.users.index',
            'admin.bolumler.index',
            'admin.iaa-yonetim.index',
            'admin.sikayetler.index',
            'admin.disiplin.index',
            'puan-durumu',
            'admin.bolum-kategorileri.index',
        ];

        $roles = ['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör'];
        
        $cases = [];
        foreach ($roles as $role) {
            foreach ($routes as $route) {
                $cases[] = [$role, $route];
            }
        }

        return $cases;
    }

    /**
     * Kullanıcı listesinde filtreleme yapıldığında sayfanın çökmemesini test eder.
     */
    public function test_user_list_filters_work_without_errors()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Superadmin');

        // Onaylı ve personel bir kullanıcı oluştur
        User::factory()->create([
            'name' => 'Test User', 
            'onaylandi_mi' => true, 
            'is_personnel' => true,
            'is_mavi_yaka' => false
        ])->assignRole('Kullanıcı');

        // Çeşitli filtrelerle sayfayı çağır
        $response = $this->actingAs($admin)->get(route('admin.users.index', [
            'name_filter' => 'Test User',
            'role_filter' => 'Kullanıcı'
        ]));

        $response->assertStatus(200);
        $response->assertSee('Test User');
    }

    /**
     * İAA detay sayfasının (proje çalışma alanı) her rol için çökmediğini doğrular.
     * @dataProvider roleProviderForIaa
     */
    public function test_iaa_detail_page_renders_without_errors(string $roleName)
    {
        $user = User::factory()->create();
        $user->assignRole($roleName);

        // Bir İAA oluştur
        $iaa = \App\Models\Iaa::create([
            'baslik' => 'Test Proje ' . $roleName,
            'durum' => 'Havuzda',
            'mevcut_durum' => 'Test mevcut durum içeriği',
            'gonderen_user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get(route('iaa.show', $iaa->id));

        // 500 hata alınmadığını doğrula (403 veya 200 gelebilir)
        $this->assertNotEquals(500, $response->getStatusCode(), "IAA show page crashed for role $roleName");
    }

    public static function roleProviderForIaa(): array
    {
        return [['Superadmin'], ['Bölüm Lideri'], ['Direktör'], ['Kullanıcı']];
    }
}
