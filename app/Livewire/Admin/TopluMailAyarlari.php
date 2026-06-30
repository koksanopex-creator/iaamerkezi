<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Setting;
use Livewire\Component;
use App\Models\Customer;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TopluMailAyarlari extends Component
{
    public $roles = [];
    public $users = [];
    public $customers = [];

    // Form states
    public $rolePermissions = []; // role_id => boolean
    public $userPermissions = []; // user_id => boolean
    
    // Restrictions
    public $roleRestrictions = []; // role_id => [customer_id, ...]
    public $userRestrictions = []; // user_id => [customer_id, ...]

    public function mount()
    {
        if (!auth()->user()->hasRole('Superadmin')) {
            abort(403);
        }

        $this->roles = Role::whereNotIn('name', ['Superadmin', 'Müşteri', 'Müşteri Temsilcisi', 'Dış Avukat'])->get();
        // Load internal users
        $this->users = User::where('is_personnel', true)
                           ->whereDoesntHave('roles', fn($q) => $q->where('name', 'Superadmin'))
                           ->orderBy('name')->get();
        
        $this->customers = Customer::orderBy('name')->get();

        $this->loadSettings();
    }

    public function loadSettings()
    {
        $permission = Permission::firstOrCreate(['name' => 'toplu_mail_gonder']);
        
        // Load role permissions
        foreach ($this->roles as $role) {
            $this->rolePermissions[$role->id] = $role->hasPermissionTo('toplu_mail_gonder');
        }

        // Load user permissions (direct)
        foreach ($this->users as $user) {
            $this->userPermissions[$user->id] = $user->hasDirectPermission('toplu_mail_gonder');
        }

        // Load restrictions
        $restrictions = Setting::where('key', 'bulk_mail_restrictions')->first();
        if ($restrictions && $restrictions->value) {
            $data = json_decode($restrictions->value, true) ?: [];
            $this->roleRestrictions = $data['roles'] ?? [];
            $this->userRestrictions = $data['users'] ?? [];
        }
    }

    public function saveSettings()
    {
        if (!auth()->user()->hasRole('Superadmin')) {
            abort(403);
        }

        // Update Role Permissions
        foreach ($this->rolePermissions as $roleId => $value) {
            $role = Role::find($roleId);
            if ($role) {
                if ($value) {
                    $role->givePermissionTo('toplu_mail_gonder');
                } else {
                    $role->revokePermissionTo('toplu_mail_gonder');
                }
            }
        }

        // Update User Permissions
        foreach ($this->userPermissions as $userId => $value) {
            $user = User::find($userId);
            if ($user) {
                if ($value) {
                    $user->givePermissionTo('toplu_mail_gonder');
                } else {
                    $user->revokePermissionTo('toplu_mail_gonder');
                }
            }
        }

        // Save Restrictions
        $data = [
            'roles' => $this->roleRestrictions,
            'users' => $this->userRestrictions,
        ];

        Setting::updateOrCreate(
            ['key' => 'bulk_mail_restrictions'],
            ['value' => json_encode($data)]
        );

        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Başarılı',
            'text' => 'Ayarlar başarıyla kaydedildi.',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.toplu-mail-ayarlari')
            ->layout('layouts.app', ['header' => 'Toplu Mail Yetki Ayarları']);
    }
}
