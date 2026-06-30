<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HukukYetkiController extends Controller
{
    /**
     * Hukuk Yetki Matrisi Sayfası
     */
    public function index()
    {
        $user = Auth::user();

        // Sadece Superadmin ve Hukuk Admini erişebilir
        if (!$user->hasRole(['Superadmin', 'Hukuk Admini']))
        {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        // DİNAMİK BÖLÜM TESPİTİ: 
        // Hukuk Admini rolüne sahip VE aynı zamanda Bölüm Lideri olan kullanıcıların bölümlerini bul.
        $targetBolumIds = User::role('Hukuk Admini')
            ->whereHas('roles', function ($q)
            {
                $q->where('name', 'Bölüm Lideri');
            })
            ->pluck('bolum_id')
            ->unique()
            ->filter();

        $query = User::personel()
            ->with(['roles', 'permissions', 'bolum'])
            ->whereIn('bolum_id', $targetBolumIds)
            ->whereDoesntHave('roles', function ($q)
            {
                $q->whereIn('name', ['Hukuk Admini', 'Superadmin']);
            });

        $personel = $query->orderBy('name')->get();

        // Matriste yönetilecek izinler (config'den al)
        $managedPermissions = config('hukuk_permissions');

        return view('admin.disiplin.hukuk_matrisi', compact('personel', 'managedPermissions'));
    }

    /**
     * Rol ve Yetki Güncelleme
     */
    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();

        // Güvenlik: Kendi bölümü mü? (Superadmin hariç)
        if (!$currentUser->hasRole('Superadmin') && $user->bolum_id !== $currentUser->bolum_id)
        {
            return response()->json(['success' => false, 'message' => 'Bu kullanıcıyı yönetme yetkiniz yok.'], 403);
        }

        $type = $request->input('type'); // 'role' veya 'permission'
        $value = $request->input('value');
        $status = $request->input('status'); // bool

        $actionWord = "";
        $targetName = "";

        if ($type === 'role')
        {
            $targetName = "Hukuk Yöneticisi Rolü";
            if ($status)
            {
                $user->assignRole('Hukuk Yöneticisi');
                $actionWord = "atandı";
            }
            else
            {
                $user->removeRole('Hukuk Yöneticisi');
                $actionWord = "kaldırıldı";
            }
        }
        elseif ($type === 'permission')
        {
            // İzin etiketini bulalım
            $allPerms = [];
            foreach (config('hukuk_permissions', []) as $group => $perms)
            {
                foreach ($perms as $slug => $label)
                {
                    $allPerms[$slug] = $label;
                }
            }
            $targetName = $allPerms[$value] ?? $value;

            if ($status)
            {
                $user->givePermissionTo($value);
                $actionWord = "verildi";
            }
            else
            {
                $user->revokePermissionTo($value);
                $actionWord = "alındı";
            }
        }

        // 1. LOG KAYDI
        \App\Models\HukukYetkiLog::create([
            'admin_id' => $currentUser->id,
            'user_id' => $user->id,
            'type' => $type,
            'target_name' => $targetName,
            'action' => $actionWord,
            'details' => "{$currentUser->name} tarafından {$user->name} kullanıcısına {$targetName} yetkisi {$actionWord}.",
            'ip_address' => $request->ip()
        ]);

        // 2. BİLDİRİM: KULLANICIYA
        $user->notify(new \App\Notifications\HukukYetkiGuncellendiNotification(
            $currentUser->name,
            $user->name,
            $type,
            $targetName,
            $actionWord,
            false
        ));

        // 3. BİLDİRİM: DİĞER ADMINLERE (Bölümdeki diğer Hukuk Adminleri)
        $adminsToNotify = User::role('Hukuk Admini')
            ->where('id', '!=', $currentUser->id) // Kendine atma
            ->where('bolum_id', $currentUser->bolum_id)
            ->get();

        // Superadminler her zaman her şeyi duymalı mı? Evet, denetim için.
        $superadmins = User::role('Superadmin')
            ->where('id', '!=', $currentUser->id)
            ->get();

        $allAdmins = $adminsToNotify->merge($superadmins);

        foreach ($allAdmins as $admin)
        {
            $admin->notify(new \App\Notifications\HukukYetkiGuncellendiNotification(
                $currentUser->name,
                $user->name,
                $type,
                $targetName,
                $actionWord,
                true // isForAdmin = true
            ));
        }

        return response()->json([
            'status' => 'success',
            'message' => "{$user->name} için {$targetName} yetkisi başarıyla {$actionWord}."
        ]);
    }
}
