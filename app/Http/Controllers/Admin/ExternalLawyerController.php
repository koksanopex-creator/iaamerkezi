<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ExternalLawyerController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('dis-avukatlar.gor') && !auth()->user()->hasRole(['Superadmin', 'Hukuk Admini'])) {
            abort(403, 'Dış avukatları görüntüleme yetkiniz yok.');
        }

        $search = $request->input('search');

        // Sadece Dış Avukat rolüne sahip kullanıcıları getir (Soft delete otomatik dahil değildir, sadece aktifleri getirir)
        $query = User::role('Dış Avukat');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('telefon', 'LIKE', "%{$search}%");
            });
        }

        $lawyers = $query->with(['addedBy', 'updatedBy'])->orderBy('name')->get();
        $totalLawyers = User::role('Dış Avukat')->count();

        return view('admin.dis_avukatlar.index', compact('lawyers', 'totalLawyers', 'search'));
    }

    public function create()
    {
        if (!auth()->user()->can('dis-avukatlar.duzenle') && !auth()->user()->hasRole(['Superadmin', 'Hukuk Admini'])) {
            abort(403, 'Dış avukat ekleme yetkiniz yok.');
        }

        return view('admin.dis_avukatlar.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('dis-avukatlar.duzenle') && !auth()->user()->hasRole(['Superadmin', 'Hukuk Admini'])) {
            abort(403, 'Dış avukat ekleme yetkiniz yok.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'telefon' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'telefon' => $validated['telefon'],
            'created_by_id' => Auth::id(), // Loglama: Ekleyen kişi
        ]);

        // Kritik Adım: Rol Atama
        $user->assignRole('Dış Avukat');

        Log::info("Yeni Dış Avukat Eklendi: " . $user->name . " - Ekleyen: " . auth()->user()->name);

        return redirect()->route('admin.dis_avukatlar.index')
            ->with('success', 'Dış Avukat sisteme eklendi ve rolü tanımlandı.');
    }

    public function edit($id)
    {
        $lawyer = User::role('Dış Avukat')->findOrFail($id);

        // Yetki Kontrolü: Superadmin/Hukuk Admin her şeyi düzenler, diğerleri sadece kendi eklediğini (Düzenleme yetkisi kısıtlı değilse)
        // Ancak genellikle oluşturma yetkisi olan düzenleme de yapabilir.
        if (!auth()->user()->hasRole(['Superadmin', 'Hukuk Admini']) && $lawyer->created_by_id !== Auth::id()) {
            abort(403, 'Bu kaydı düzenleme yetkiniz yok.');
        }

        return view('admin.dis_avukatlar.edit', compact('lawyer'));
    }

    public function update(Request $request, $id)
    {
        $lawyer = User::role('Dış Avukat')->findOrFail($id);

        if (!auth()->user()->hasRole(['Superadmin', 'Hukuk Admini']) && $lawyer->created_by_id !== Auth::id()) {
            abort(403, 'Bu kaydı düzenleme yetkiniz yok.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'telefon' => 'nullable|string',
        ]);

        $lawyer->name = $validated['name'];
        $lawyer->email = $validated['email'];
        if (!empty($validated['password'])) {
            $lawyer->password = Hash::make($validated['password']);
        }
        $lawyer->telefon = $validated['telefon'];
        $lawyer->updated_by_id = Auth::id(); // Güncelleyen kişi logu
        $lawyer->save();

        return redirect()->route('admin.dis_avukatlar.index')
            ->with('success', 'Dış Avukat bilgileri güncellendi.');
    }

    public function destroy($id)
    {
        $lawyer = User::role('Dış Avukat')->findOrFail($id);

        // Kural: Superadmin/Hukuk Admin her şeyi silebilir. Diğerleri sadece kendi eklediğini.
        if (!auth()->user()->hasRole(['Superadmin', 'Hukuk Admini']) && $lawyer->created_by_id !== Auth::id()) {
            return back()->with('error', 'Sadece kendi eklediğiniz dış avukatları silebilirsiniz.');
        }

        $lawyer->delete(); // Soft delete (User modelinde SoftDeletes var)

        Log::info("Dış Avukat Silindi (Soft Delete): " . $lawyer->name . " - Silen: " . auth()->user()->name);

        return redirect()->route('admin.dis_avukatlar.index')
            ->with('success', 'Dış Avukat kaydı başarılı bir şekilde silindi.');
    }
}