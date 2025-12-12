<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DisiplinSorumlusuController extends Controller
{
    /**
     * Tutanak Sorumlusu Atama Ekranı
     */
    public function index()
    {
        $user = Auth::user();

        // Sadece Bölüm Lideri girebilir
        if (!$user->hasRole('Bölüm Lideri')) {
            abort(403, 'Sadece Bölüm Liderleri yetki dağıtabilir.');
        }

        // Liderin bölümündeki personelleri getir (Kendisi hariç)
        $personeller = User::where('bolum_id', $user->bolum_id)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        return view('admin.disiplin.sorumlular.index', compact('personeller'));
    }

    /**
     * Yetki Durumunu Değiştir (Ver/Al)
     */
    public function update(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();

        // Güvenlik: Sadece kendi bölümündeki personele işlem yapabilir
        if ($targetUser->bolum_id != $currentUser->bolum_id) {
            abort(403, 'Sadece kendi bölümünüzdeki personele işlem yapabilirsiniz.');
        }

        // Durumu tersine çevir (Varsa al, yoksa ver)
        $targetUser->can_issue_disciplinary = !$targetUser->can_issue_disciplinary;
        $targetUser->save();

        // === BİLDİRİM GÖNDER (YENİ EKLENDİ) ===
        if ($targetUser->can_issue_disciplinary) {
            $targetUser->notify(new \App\Notifications\SorumluAtandiBildirimi(Auth::user()->name));
        }
        // ======================================

        $durum = $targetUser->can_issue_disciplinary ? 'VERİLDİ' : 'GERİ ALINDI';
        return back()->with('success', "{$targetUser->name} kullanıcısının tutanak sorumluluğu {$durum}.");
    }
}