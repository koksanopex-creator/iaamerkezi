<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bolum;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class MusteriSahaTemsilcisiController extends Controller
{
    /**
     * Atama sayfasını gösterir.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Sadece "Müşteri Saha Temsilcisi" rolüne sahip kullanıcıları getir
        $temsilciler = User::role('Müşteri Saha Temsilcisi')->with('musteriSahaTemsilcisiOlduguBolumler')->orderBy('name')->get();
        
        // Superadmin ise tüm bölümler, Bölüm Kalite Yöneticisi ise sadece yönettiği bölümler
        if ($user->hasRole('Superadmin') || $user->hasRole('Yonetim')) {
            $bolumler = Bolum::where('is_active', true)->orderBy('ad')->get();
        } else {
            $bolumler = $user->yonetilenBolumler()->where('is_active', true)->orderBy('ad')->get();
        }

        return view('admin.musteri_saha_temsilcileri.index', compact('temsilciler', 'bolumler'));
    }

    /**
     * Yeni bir kullanıcıyı Müşteri Saha Temsilcisi yapar.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        
        if (!$user->hasRole('Müşteri Saha Temsilcisi')) {
            $user->assignRole('Müşteri Saha Temsilcisi');
        }

        return back()->with('success', $user->name . ' Müşteri Saha Temsilcisi olarak eklendi.');
    }

    /**
     * Bir kullanıcıya sorumlu olduğu bölümleri atar/gceller.
     */
    public function update(Request $request, User $user)
    {
        // Güvenlik: Kullanıcının gerçekten bu rolde olduğundan emin olalım
        if (!$user->hasRole('Müşteri Saha Temsilcisi')) {
            return back()->with('error', 'Bu kullanıcı "Müşteri Saha Temsilcisi" rolüne sahip değil.');
        }

        $request->validate([
            'bolumler' => 'array',
            'bolumler.*' => 'exists:bolumler,id',
        ]);

        $authUser = auth()->user();
        $newBolumIds = $request->input('bolumler', []);

        // Eğer Superadmin değilse, sadece kendi yönettiği bölümler üzerinde işlem yapabilir
        if (!$authUser->hasRole('Superadmin') && !$authUser->hasRole('Yonetim')) {
            $yonetilenBolumIds = $authUser->yonetilenBolumler()->pluck('bolumler.id')->toArray();
            
            // Sadece auth user'ın yönettiği bölümlere olan atamalarını güncelle
            // Önce bu kullanıcının mevcut yetkili olduğu ama benim GÖREMEDİÄİM bölümleri al (onlara dokunmayacağız)
            $mevcutDigerBolumler = $user->musteriSahaTemsilcisiOlduguBolumler()
                                        ->whereNotIn('bolumler.id', $yonetilenBolumIds)
                                        ->pluck('bolumler.id')
                                        ->toArray();
            
            // Gelen datadaki geçerli bölümleri (benim yönettiklerim) filtrele
            $gecerliYeniBolumler = array_intersect($newBolumIds, $yonetilenBolumIds);
            
            // Dokunamadığım diğer bölümler + Atadığım yeni bölümler
            $finalBolumIds = array_merge($mevcutDigerBolumler, $gecerliYeniBolumler);
        } else {
            // Superadmin her şeye müdahale edebilir
            $finalBolumIds = $newBolumIds;
        }

        // sync metodu, seçilenleri ekler, seçilmeyenleri siler.
        $user->musteriSahaTemsilcisiOlduguBolumler()->sync($finalBolumIds);

        // Bildirim gönder
        $user->notify(new \App\Notifications\SahaTemsilcisiYetkiGuncellendiBildirimi($authUser->name));

        return back()->with('success', $user->name . ' için saha ziyaret yetkileri güncellendi.');
    }
}
