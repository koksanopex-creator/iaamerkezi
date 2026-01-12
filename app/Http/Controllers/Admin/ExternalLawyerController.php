<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ExternalLawyerController extends Controller
{
    public function index()
    {
        // Sadece Dış Avukat rolüne sahip kullanıcıları getir
        $lawyers = User::role('Dış Avukat')->get();
        return view('admin.dis_avukatlar.index', compact('lawyers'));
    }

    public function create()
    {
        return view('admin.dis_avukatlar.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8', // Veya otomatik oluşturulup mail atılabilir
            'telefon' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            // 'phone' => $validated['telefon'] // User tablosunda telefon varsa
        ]);

        // Kritik Adım: Rol Atama
        $user->assignRole('Dış Avukat');

        Log::info("Yeni Dış Avukat Eklendi: " . $user->name . " - Ekleyen: " . auth()->user()->name);

        return redirect()->route('admin.dis_avukatlar.index')
            ->with('success', 'Dış Avukat sisteme eklendi ve rolü tanımlandı.');
    }
}