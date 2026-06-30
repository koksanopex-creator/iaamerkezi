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

        // Sadece Bölüm Lideri veya Yetkili Yardımcısı girebilir
        if (!$user->hasRole('Bölüm Lideri') && !$user->hasBolumAuthority('bolum.disiplin.sorumlu_yonet')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        // Liderin bölümündeki personelleri ve MAVİ YAKALILARI getir
        $query = User::where('bolum_id', $user->bolum_id)
            ->where('id', '!=', $user->id);

        // Eğer yardımcı ise, müdürü (Bölüm Lideri) de listeden çıkar
        if ($user->hasRole('Bölüm Lider Yardımcısı')) {
            $query->whereDoesntHave('roles', function($q) {
                $q->where('name', 'Bölüm Lideri');
            });
        }

        $personeller = $query->orderBy('is_mavi_yaka') // Önce beyaz yaka, sonra mavi yaka
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

        // 1. Genel Yetki Kontrolü
        if (!$currentUser->hasRole('Bölüm Lideri') && !$currentUser->hasBolumAuthority('bolum.disiplin.sorumlu_yonet')) {
            abort(403, 'Yetki verme/alma işlemini sadece Bölüm Liderleri veya Yetkili Yardımcılar yapabilir.');
        }

        // 2. Bölüm Kontrolü
        if ($targetUser->bolum_id != $currentUser->bolum_id) {
            abort(403, 'Sadece kendi bölümünüzdeki personele işlem yapabilirsiniz.');
        }

        // 3. Yardımcı Kısıtlamaları (Kendine ve Müdüre müdahale edemez)
        if ($currentUser->hasRole('Bölüm Lider Yardımcısı')) {
            if ($targetUser->id == $currentUser->id) {
                abort(403, 'Kendi yetki durumunuzu değiştiremezsiniz.');
            }
            if ($targetUser->hasRole('Bölüm Lideri')) {
                abort(403, 'Bölüm Liderinin (Müdürünüzün) yetki durumuna müdahale edemezsiniz.');
            }
        }

        // Durumu tersine çevir (Varsa al, yoksa ver)
        $targetUser->can_issue_disciplinary = !$targetUser->can_issue_disciplinary;
        $targetUser->save();

        // === BİLDİRİMLER (YENİ VE GELİŞMİŞ) ===
        
        // 1. Hedef Personele Bildirim (Zil + Mail)
        $targetUser->notify(new \App\Notifications\DisiplinSorumluYetkiDegisikligi(
            $currentUser, 
            $targetUser, 
            $targetUser->can_issue_disciplinary, 
            'target'
        ));

        // 2. Eğer işlemi YARDIMCI yaptıysa, BÖLÜM LİDERİNE (Müdüre) bildirim gönder
        if ($currentUser->hasRole('Bölüm Lider Yardımcısı')) {
            $manager = User::where('bolum_id', $currentUser->bolum_id)
                ->role('Bölüm Lideri')
                ->first();

            if ($manager) {
                $manager->notify(new \App\Notifications\DisiplinSorumluYetkiDegisikligi(
                    $currentUser, 
                    $targetUser, 
                    $targetUser->can_issue_disciplinary, 
                    'manager'
                ));
            }
        }
        // ======================================

        $durum = $targetUser->can_issue_disciplinary ? 'VERİLDİ' : 'GERİ ALINDI';
        return back()->with('success', "{$targetUser->name} kullanıcısının tutanak sorumluluğu {$durum}.");
    }
}