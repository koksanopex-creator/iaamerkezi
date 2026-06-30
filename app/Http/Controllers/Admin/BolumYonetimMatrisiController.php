<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bolum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BolumYonetimMatrisiController extends Controller
{
    /**
     * Bölüm Yönetim Matrisi Sayfası
     */
    public function index()
    {
        $user = Auth::user();

        // Kimler erişebilir? Superadmin VEYA Bölüm Lideri
        if (!$user->hasAnyRole(['Superadmin', 'Bölüm Lideri', 'Yonetim'])) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        $tumBolumler = collect();
        $bolumId = $user->bolum_id;

        // Superadmin veya Yönetim ise tüm bölümleri seçebilir
        if ($user->hasAnyRole(['Superadmin', 'Yonetim'])) {
            $tumBolumler = Bolum::active()->orderBy('ad')->get();
            $bolumId = request('bolum_id', $bolumId);
            
            // Eğer hala bolum_id yoksa (Superadmin bölüme bağlı değilse) ilk bölümü seç
            if (!$bolumId && $tumBolumler->isNotEmpty()) {
                $bolumId = $tumBolumler->first()->id;
            }
        }

        if (!$bolumId) {
            abort(404, 'Yönetilecek bir bölüm bulunamadı.');
        }

        $bolum = Bolum::findOrFail($bolumId);

        // Bölümdeki personelleri getir
        $personel = User::personel()
            ->where('bolum_id', $bolumId)
            ->where('id', '!=', $user->id) // Kendini yönetmesin
            ->orderBy('is_mavi_yaka', 'asc') // Beyaz yaka önce
            ->orderBy('name', 'asc')
            ->get();

        // Matriste yönetilecek izinler (config'den al)
        $managedPermissions = config('bolum_permissions');

        return view('admin.bolum.yonetim_matrisi', compact('personel', 'managedPermissions', 'bolum', 'tumBolumler'));
    }

    /**
     * Yardımcı Atama ve Yetki Güncelleme
     */
    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();

        // Güvenlik: Kendi bölümü mü? (Superadmin hariç)
        if (!$currentUser->hasRole('Superadmin') && $user->bolum_id !== $currentUser->bolum_id) {
            return response()->json(['success' => false, 'message' => 'Bu kullanıcıyı yönetme yetkiniz yok.'], 403);
        }

        $type = $request->input('type'); // 'role' veya 'permission'
        $value = $request->input('value');
        $status = $request->input('status'); // bool

        $actionWord = "";
        $targetName = "";

        if ($type === 'role') {
            $targetName = "Bölüm Lider Yardımcısı Rolü";
            if ($status) {
                $user->assignRole('Bölüm Lider Yardımcısı');
                $actionWord = "atandı";
            } else {
                $user->removeRole('Bölüm Lider Yardımcısı');
                $actionWord = "kaldırıldı";
            }
        } elseif ($type === 'permission') {
            // İzin etiketini bulalım
            $allPerms = [];
            foreach (config('bolum_permissions', []) as $group => $perms) {
                foreach ($perms as $slug => $label) {
                    $allPerms[$slug] = $label;
                }
            }
            $targetName = $allPerms[$value] ?? $value;

            if ($status) {
                $user->givePermissionTo($value);
                $actionWord = "verildi";
            } else {
                $user->revokePermissionTo($value);
                $actionWord = "alındı";
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "{$user->name} için {$targetName} yetkisi başarıyla {$actionWord}."
        ]);
    }
}
