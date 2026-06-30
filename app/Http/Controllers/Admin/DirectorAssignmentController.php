<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bolum;
use Illuminate\Support\Facades\Hash;

class DirectorAssignmentController extends Controller
{
    public function index()
    {
        // Direktör rollerini getir (Spatie role)
        $direktorler = User::role('Direktör')->get();
        // Aktif bölümleri getir
        $bolumler = Bolum::where('is_active', true)->get();

        return view('admin.direktor_atamalari.index', compact('direktorler', 'bolumler'));
    }

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
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'is_personnel' => true,
        ]);

        // Rol atama
        $user->assignRole('Direktör');

        return redirect()->route('admin.direktorler.index')->with('success', 'Direktör başarıyla oluşturuldu.');
    }

    public function update(Request $request, User $user)
    {
        // 1. Önce bu direktörün eski sorumluluklarını temizleyelim
        Bolum::where('director_id', $user->id)->update(['director_id' => null]);

        // 2. Seçilen bölümleri bu direktöre atayalım
        if ($request->has('bolumler') && is_array($request->bolumler)) {
            Bolum::whereIn('id', $request->bolumler)->update(['director_id' => $user->id]);
        }

        return redirect()->route('admin.direktorler.index')->with('success', 'Bölüm atamaları güncellendi.');
    }
}
