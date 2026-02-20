<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bolum;
use Illuminate\Http\Request;

class DirectorAssignmentController extends Controller
{
    /**
     * Atama sayfasını gösterir.
     */
    public function index()
    {
        // Sadece "Direktör" rolüne sahip kullanıcıları getir
        $direktorler = User::role('Direktör')->with('yonetilenBolumler')->orderBy('name')->get();

        // Tüm bölümleri getir
        $bolumler = Bolum::orderBy('ad')->get();

        return view('admin.direktor_atamalari.index', compact('direktorler', 'bolumler'));
    }

    /**
     * Bir kullanıcıya sorumlu olduğu bölümleri atar/günceller.
     */
    public function update(Request $request, User $user)
    {
        // Güvenlik: Kullanıcının gerçekten bu rolde olduğundan emin olalım
        if (!$user->hasRole('Direktör')) {
            return back()->with('error', 'Bu kullanıcı "Direktör" rolüne sahip değil.');
        }

        $request->validate([
            'bolumler' => 'array',
            'bolumler.*' => 'exists:bolumler,id',
        ]);

        $bolumIds = $request->input('bolumler', []);

        // 1. Mevcut atamaları temizle (Bu direktöre bağlı tüm bölümleri null yap)
        Bolum::where('director_id', $user->id)->update(['director_id' => null]);

        // 2. Yeni atamaları yap
        if (!empty($bolumIds)) {
            Bolum::whereIn('id', $bolumIds)->update(['director_id' => $user->id]);
        }

        return back()->with('success', $user->name . ' için bölüm sorumlulukları güncellendi.');
    }

    /**
     * Hızlı bir şekilde yeni bir Direktör oluşturur.
     */
    public function storeDirector(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'onaylandi_mi' => true,
            'is_personnel' => true,
        ]);

        // "Direktör" rolünü ata
        $user->assignRole('Direktör');

        return back()->with('success', 'Yeni direktör (' . $user->name . ') başarıyla oluşturuldu.');
    }
}
