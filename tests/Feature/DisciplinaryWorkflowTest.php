<?php

namespace Tests\Feature;

use App\Models\DisciplinaryBehavior;
use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryCategory;
use App\Models\DisciplinaryImpact;
use App\Models\DisciplinaryMultiplier;
use App\Models\DisciplinaryPenaltyScale;
use App\Models\DisciplinaryScope;
use App\Models\DisciplinaryVote;
use App\Models\User;
use App\Services\Dashboard\KullaniciPuanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DisciplinaryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $councilMember;
    protected $targetUser;
    protected $category;
    protected $behavior;
    protected $impact;
    protected $scope;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Superadmin']);
        Role::create(['name' => 'Disiplin Kurulu Üyesi']);
        Role::create(['name' => 'Disiplin Kurulu Başkanı']);
        Role::create(['name' => 'Hukuk Admini']);
        Role::create(['name' => 'Yönetici']);
        Role::create(['name' => 'İnsan Kaynakları']);
        Role::create(['name' => 'Hukuk Yöneticisi']);

        // Create users
        $this->admin = User::factory()->create(['name' => 'Admin User', 'is_personnel' => true]);
        $this->admin->assignRole('Superadmin');

        $this->councilMember = User::factory()->create(['name' => 'Council Member', 'is_personnel' => true]);
        $this->councilMember->assignRole('Disiplin Kurulu Üyesi');

        $this->targetUser = User::factory()->create([
            'name' => 'Target Personnel',
            'toplam_puan' => 100,
            'is_personnel' => true
        ]);

        // Setup Disciplinary Meta Data
        $this->category = DisciplinaryCategory::create(['ad' => 'Test Category']);
        $this->behavior = DisciplinaryBehavior::create([
            'category_id' => $this->category->id,
            'tanim' => 'Test Behavior',
            'aktif_mi' => true
        ]);
        $this->impact = DisciplinaryImpact::create(['tanim' => 'Low', 'puan' => 2]);
        $this->scope = DisciplinaryScope::create(['tanim' => 'Personal', 'puan' => 2]);

        DisciplinaryMultiplier::create(['tekrar_sayisi' => 1, 'katsayi' => 1.0]);
        DisciplinaryPenaltyScale::create([
            'min_puan' => 1,
            'max_puan' => 10,
            'ceza_adi' => 'Uyarı'
        ]);

        // Setup base points for calculation (KullaniciPuanService logic)
        \App\Models\Setting::create(['key' => 'iaa_oneri_puani', 'value' => '100']);
        \App\Models\Iaa::create([
            'baslik' => 'Test Iaa',
            'oneri' => 'Test Oneri',
            'mevcut_durum' => 'Test Mevcut Durum',
            'gonderen_user_id' => $this->targetUser->id,
            'durum' => 'Tamamlandı'
        ]);

        // Verify initial state
        $this->assertEquals(100, app(\App\Services\Dashboard\KullaniciPuanService::class)->calculateTotalScore($this->targetUser));
        $this->targetUser->update(['toplam_puan' => 100]);
    }

    /** @test */
    public function it_can_complete_a_full_disciplinary_workflow_with_revert()
    {
        Notification::fake();

        // 1. Create Disciplinary Case
        $this->actingAs($this->admin);
        
        $case = DisciplinaryCase::create([
            'user_id' => $this->targetUser->id,
            'reporter_id' => $this->admin->id,
            'behavior_id' => $this->behavior->id,
            'impact_id' => $this->impact->id,
            'scope_id' => $this->scope->id,
            'olay_tarihi' => now(),
            'olay_aciklamasi' => 'This is a test case description for E2E.',
            'hesaplanan_puan' => 4,
            'sistem_oneri_ceza' => 'Uyarı',
            'durum' => 'Kurulda'
        ]);

        $this->assertNotNull($case);
        $this->assertEquals(4, $case->hesaplanan_puan);
        $this->assertEquals('Kurulda', $case->durum);

        // 2. Start Voting
        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Admin\Disiplin\DisiplinOylamaPaneli::class, ['case' => $case])
            ->call('startCaseVoting', 'Let us vote');

        $case->refresh();
        $this->assertTrue((bool)$case->oylama_aktif);

        // 3. Cast Vote
        \Livewire\Livewire::actingAs($this->councilMember)
            ->test(\App\Livewire\Admin\Disiplin\DisiplinOylamaPaneli::class, ['case' => $case])
            ->set('tempOyYonu', 'Ceza Verilsin')
            ->set('tempYorum', 'Guilty')
            ->call('castCaseVote');

        $this->assertDatabaseHas('disciplinary_votes', [
            'case_id' => $case->id,
            'user_id' => $this->councilMember->id,
            'oy_yonu' => 'Ceza Verilsin'
        ]);

        // 4. Resolve Case (Approve Penalty)
        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Admin\Disiplin\DisiplinOylamaPaneli::class, ['case' => $case])
            ->set('decisionNote', 'Penalty approved')
            ->call('resolveCase', 'approve')
            ->assertRedirect(route('admin.disiplin.show', ['id' => $case->id, 'tab' => 'kurul']));

        $case->refresh();
        $this->assertEquals('Karar Verildi', $case->durum);
        $this->assertEquals('Ceza Onaylandı', $case->final_karar);

        // Points are automatically deducted by resolveCase
        $this->assertEquals(96, $this->targetUser->fresh()->toplam_puan);

        // 5. Revert Decision
        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Admin\Disiplin\DisiplinOylamaPaneli::class, ['case' => $case])
            ->call('revertDecision')
            ->assertRedirect(route('admin.disiplin.show', ['id' => $case->id, 'tab' => 'kurul']));

        $case->refresh();
        $this->assertEquals('Kurulda', $case->durum);
        $this->assertNull($case->final_karar);
        $this->assertTrue((bool)$case->oylama_aktif);
        
        // 6. Verify Point Refund
        // Manual sync to match real service behavior
        app(\App\Services\Dashboard\KullaniciPuanService::class)->syncUserCache($this->targetUser);
        $this->assertEquals(100, $this->targetUser->fresh()->toplam_puan);

        // 7. Verify Notifications
        Notification::assertSentTo($this->targetUser, \App\Notifications\DisiplinKararGeriAlindi::class);
    }
}
