<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SikayetKategori;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class BolumKaliteYoneticisiController extends Controller
{
    /**
     * Atama sayfasını gösterir.
     */
    public function index()
    {
        // Sadece "Bölüm Kalite Yöneticisi" rolüne sahip kullanıcıları getir
        $yoneticiler = User::role('Bölüm Kalite Yöneticisi')->with('yonettigiSikayetKategorileri')->orderBy('name')->get();
        
        // Tüm kategorileri getir
        $kategoriler = SikayetKategori::orderBy('ad')->get();

        return view('admin.bolum_kalite_yoneticileri.index', compact('yoneticiler', 'kategoriler'));
    }

    /**
     * Bir kullanıcıya sorumlu olduğu kategorileri atar/günceller.
     */
    public function update(Request $request, User $user)
    {
        // Güvenlik: Kullanıcının gerçekten bu rolde olduğundan emin olalım (Opsiyonel ama iyi olur)
        if (!$user->hasRole('Bölüm Kalite Yöneticisi')) {
            return back()->with('error', 'Bu kullanıcı "Bölüm Kalite Yöneticisi" rolüne sahip değil.');
        }

        $request->validate([
            'kategoriler' => 'array', // Hiç seçilmezse boş dizi gelir
            'kategoriler.*' => 'exists:sikayet_kategorileri,id',
            'can_intervene_quality' => 'boolean',
        ]);

        // 1. Müdahale Yetkisi Kontrolü ve Bildirimi
        $oldIntervenePower = $user->can_intervene_quality;
        $newIntervenePower = $request->boolean('can_intervene_quality');

        if ($oldIntervenePower !== $newIntervenePower) {
            $user->can_intervene_quality = $newIntervenePower;
            $user->save();
            
            // Sadece yetki verildiğinde veya alındığında bildirim gönder
            $user->notify(new \App\Notifications\QualityInterventionAuthorityNotification($newIntervenePower));
        }

        // 2. Mevcut kategorileri al (Bildirim için kıyaslama yapmak üzere)
        $oldCategoryIds = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
        $newCategoryIds = $request->input('kategoriler', []);

        // 3. sync metodu, seçilenleri ekler, seçilmeyenleri siler.
        $user->yonettigiSikayetKategorileri()->sync($newCategoryIds);

        // 4. Bildirimleri Gönder (Yeni atananlar ve silinenler için)
        $assignedIds = array_diff($newCategoryIds, $oldCategoryIds);
        $unassignedIds = array_diff($oldCategoryIds, $newCategoryIds);

        foreach ($assignedIds as $cid) {
            $cat = SikayetKategori::find($cid);
            if ($cat) {
                $user->notify(new \App\Notifications\KaliteYoneticisiAtamasi($cat, 'assigned'));
            }
        }

        foreach ($unassignedIds as $cid) {
            $cat = SikayetKategori::find($cid);
            if ($cat) {
                $user->notify(new \App\Notifications\KaliteYoneticisiAtamasi($cat, 'unassigned'));
            }
        }

        return back()->with('success', $user->name . ' için yetkiler ve kategori sorumlulukları güncellendi.');
    }
}