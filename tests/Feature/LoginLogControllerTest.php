<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Bolum;
use App\Models\Customer;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginLogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $bolum;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);

        // Create an admin user who has access to logs
        $this->admin = User::factory()->create(['name' => 'Admin User', 'is_personnel' => true]);
        $this->admin->assignRole('Superadmin');

        $this->bolum = Bolum::create([
            'ad' => 'Test Bölümü',
        ]);
    }

    /** @test */
    public function it_can_load_login_activities_page()
    {
        $response = $this->actingAs($this->admin)->get(route('logs.login.index'));
        $response->assertStatus(200);
        $response->assertSee('Kullanıcı Giriş &amp; Aktivite Analizi', false);
        $response->assertSee('Şirket Personelleri');
        $response->assertSee('Müşteriler');
    }

    /** @test */
    public function it_filters_personnel_by_default_and_customers_when_selected()
    {
        // 1. Create a personnel user and a customer user
        $personnel = User::factory()->create([
            'name' => 'Personnel Ahmet',
            'is_personnel' => true,
            'bolum_id' => $this->bolum->id,
        ]);

        $customer = User::factory()->create([
            'name' => 'Customer Mehmet',
            'is_personnel' => false,
        ]);

        // 2. Fetch the index page (default tab is personel)
        $response = $this->actingAs($this->admin)->get(route('logs.login.index'));
        $response->assertStatus(200);
        $response->assertSee('Personnel Ahmet');
        $response->assertDontSee('Customer Mehmet');

        // 3. Fetch index page with tab=musteri
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', ['tab' => 'musteri']));
        $response->assertStatus(200);
        $response->assertSee('Customer Mehmet');
        $response->assertDontSee('Personnel Ahmet');
    }

    /** @test */
    public function it_filters_by_bolum_in_personnel_tab()
    {
        $bolum2 = Bolum::create(['ad' => 'Başka Bölüm']);

        $user1 = User::factory()->create([
            'name' => 'Kullanıcı Bir',
            'is_personnel' => true,
            'bolum_id' => $this->bolum->id,
        ]);

        $user2 = User::factory()->create([
            'name' => 'Kullanıcı İki',
            'is_personnel' => true,
            'bolum_id' => $bolum2->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('logs.login.index', ['bolum_id' => $this->bolum->id]));
        $response->assertStatus(200);
        $response->assertSee('Kullanıcı Bir');
        $response->assertDontSee('Kullanıcı İki');
    }

    /** @test */
    public function it_filters_by_customer_in_customer_tab()
    {
        $firm1 = Customer::create(['name' => 'Firma A']);
        $firm2 = Customer::create(['name' => 'Firma B']);

        $cust1 = User::factory()->create([
            'name' => 'Müşteri Bir',
            'is_personnel' => false,
            'customer_id' => $firm1->id,
        ]);

        $cust2 = User::factory()->create([
            'name' => 'Müşteri İki',
            'is_personnel' => false,
        ]);
        // Many-to-many relationship using pivot table
        $cust2->customers()->attach($firm2->id);

        // Filter by Firma A
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', [
            'tab' => 'musteri',
            'customer_id' => $firm1->id,
        ]));
        $response->assertStatus(200);
        $response->assertSee('Müşteri Bir');
        $response->assertDontSee('Müşteri İki');

        // Filter by Firma B
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', [
            'tab' => 'musteri',
            'customer_id' => $firm2->id,
        ]));
        $response->assertStatus(200);
        $response->assertSee('Müşteri İki');
        $response->assertDontSee('Müşteri Bir');
    }

    /** @test */
    public function it_shows_customer_company_names_in_bolum_column()
    {
        $firm1 = Customer::create(['name' => 'Firma Alfa']);
        $firm2 = Customer::create(['name' => 'Firma Beta']);

        $cust = User::factory()->create([
            'name' => 'Müşteri Tek',
            'is_personnel' => false,
            'customer_id' => $firm1->id,
        ]);
        $cust->customers()->attach($firm2->id);

        $this->assertEquals('Firma Alfa, Firma Beta', $cust->fresh()->firma_adlari);

        $response = $this->actingAs($this->admin)->get(route('logs.login.index', ['tab' => 'musteri']));
        $response->assertStatus(200);
        $response->assertSee('Firma Alfa, Firma Beta');
    }

    /** @test */
    public function it_persists_filters_in_session_and_clears_correctly()
    {
        // Visit with search parameter
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', ['search' => 'ArananKullanici']));
        $response->assertStatus(200);

        // Session should have stored the filter
        $this->assertEquals('ArananKullanici', session('login_activities_filters.search'));

        // Visit without query parameter - should redirect to route with session filters
        $response = $this->actingAs($this->admin)->get(route('logs.login.index'));
        $response->assertRedirect(route('logs.login.index', ['search' => 'ArananKullanici']));

        // Visit with clear_filter=1 - should forget session filters and redirect
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', ['clear_filter' => 1]));
        $response->assertRedirect(route('logs.login.index'));
        
        $this->assertFalse(session()->has('login_activities_filters'));
    }

    /** @test */
    public function it_sorts_users_by_different_columns_correctly()
    {
        // 1. Create departments
        $bolumA = Bolum::create(['ad' => 'A Bolumu']);
        $bolumB = Bolum::create(['ad' => 'B Bolumu']);

        // 2. Create users with specific names and departments
        // Note: Admin User is created in setUp (Admin User, is_personnel=true)
        $userAhmet = User::factory()->create([
            'name' => 'Ahmet',
            'is_personnel' => true,
            'bolum_id' => $bolumB->id, // Ahmet in B Bolumu
        ]);

        $userZeki = User::factory()->create([
            'name' => 'Zeki',
            'is_personnel' => true,
            'bolum_id' => $bolumA->id, // Zeki in A Bolumu
        ]);

        // Clear filter session to start fresh
        session()->forget('login_activities_filters');

        // Test Sort by Name - ASC (Ahmet should come first, then Admin, then Zeki)
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', [
            'sort_by' => 'name',
            'sort_dir' => 'asc'
        ]));
        $response->assertStatus(200);
        
        $usersSorted = $response->viewData('users');
        $this->assertEquals('Admin User', $usersSorted[0]->name);
        $this->assertEquals('Ahmet', $usersSorted[1]->name);
        $this->assertEquals('Zeki', $usersSorted[2]->name);

        // Test Sort by Name - DESC (Zeki first, then Ahmet, then Admin)
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', [
            'sort_by' => 'name',
            'sort_dir' => 'desc'
        ]));
        $usersSortedDesc = $response->viewData('users');
        $this->assertEquals('Zeki', $usersSortedDesc[0]->name);
        $this->assertEquals('Ahmet', $usersSortedDesc[1]->name);
        $this->assertEquals('Admin User', $usersSortedDesc[2]->name);

        // Test Sort by Bolum/Firma - ASC (Zeki's department is A Bolumu, Ahmet's is B Bolumu, Admin has no department - so Admin might be first or last depending on SQL nulls sort order, but Zeki is definitely before Ahmet)
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', [
            'sort_by' => 'bolum_firma',
            'sort_dir' => 'asc'
        ]));
        $usersSortedDeptAsc = $response->viewData('users');
        
        // Let's filter out users that don't have a department for a clearer assertion
        $filteredNames = collect($usersSortedDeptAsc->items())
            ->filter(fn($u) => !is_null($u->bolum_id))
            ->pluck('name')
            ->toArray();
        $this->assertEquals(['Zeki', 'Ahmet'], $filteredNames);

        // Test Sort by Bolum/Firma - DESC (Ahmet first, then Zeki)
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', [
            'sort_by' => 'bolum_firma',
            'sort_dir' => 'desc'
        ]));
        $usersSortedDeptDesc = $response->viewData('users');
        
        $filteredNamesDesc = collect($usersSortedDeptDesc->items())
            ->filter(fn($u) => !is_null($u->bolum_id))
            ->pluck('name')
            ->toArray();
        $this->assertEquals(['Ahmet', 'Zeki'], $filteredNamesDesc);

        // 3. Create customers and customer users
        $firmA = Customer::create(['name' => 'Alfa Corp']);
        $firmB = Customer::create(['name' => 'Beta Corp']);

        $custAhmet = User::factory()->create([
            'name' => 'Cust Ahmet',
            'is_personnel' => false,
            'customer_id' => $firmB->id,
        ]);

        $custZeki = User::factory()->create([
            'name' => 'Cust Zeki',
            'is_personnel' => false,
            'customer_id' => $firmA->id,
        ]);

        // Test Sort by Customer - ASC
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', [
            'tab' => 'musteri',
            'sort_by' => 'bolum_firma',
            'sort_dir' => 'asc'
        ]));
        $usersSortedCustAsc = $response->viewData('users');
        $filteredCustNames = collect($usersSortedCustAsc->items())
            ->filter(fn($u) => !is_null($u->customer_id))
            ->pluck('name')
            ->toArray();
        $this->assertEquals(['Cust Zeki', 'Cust Ahmet'], $filteredCustNames);

        // Test Sort by Customer - DESC
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', [
            'tab' => 'musteri',
            'sort_by' => 'bolum_firma',
            'sort_dir' => 'desc'
        ]));
        $usersSortedCustDesc = $response->viewData('users');
        $filteredCustNamesDesc = collect($usersSortedCustDesc->items())
            ->filter(fn($u) => !is_null($u->customer_id))
            ->pluck('name')
            ->toArray();
        $this->assertEquals(['Cust Ahmet', 'Cust Zeki'], $filteredCustNamesDesc);
    }

    /** @test */
    public function it_filters_statistics_cards_based_on_applied_filters()
    {
        $bolum1 = Bolum::create(['ad' => 'Bolum Bir']);
        $bolum2 = Bolum::create(['ad' => 'Bolum Iki']);

        $user1 = User::factory()->create([
            'name' => 'Ahmet Personel',
            'is_personnel' => true,
            'bolum_id' => $bolum1->id,
        ]);

        $user2 = User::factory()->create([
            'name' => 'Mehmet Personel',
            'is_personnel' => true,
            'bolum_id' => $bolum2->id,
        ]);

        // Create login activities
        \App\Models\LoginActivity::create([
            'user_id' => $user1->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'created_at' => now(),
            'last_activity_at' => now(),
        ]);

        \App\Models\LoginActivity::create([
            'user_id' => $user2->id,
            'ip_address' => '127.0.0.2',
            'user_agent' => 'Test Agent',
            'created_at' => now(),
            'last_activity_at' => now(),
        ]);

        // Clear filter session to start fresh
        session()->forget('login_activities_filters');

        // Without filter - both counted (including admin if they had logins, but let's just assert minimum count of 2)
        $response = $this->actingAs($this->admin)->get(route('logs.login.index'));
        $response->assertStatus(200);
        $stats = $response->viewData('stats');
        $this->assertGreaterThanOrEqual(2, $stats['total_users']);

        // Filter by Bolum Bir - only user1 (Ahmet) counted in stats
        $response = $this->actingAs($this->admin)->get(route('logs.login.index', [
            'bolum_id' => $bolum1->id,
        ]));
        $response->assertStatus(200);
        $statsFiltered = $response->viewData('stats');
        $this->assertEquals(1, $statsFiltered['total_users']);
        $this->assertEquals('Ahmet Personel', $statsFiltered['most_online_user']->name);
    }
}


