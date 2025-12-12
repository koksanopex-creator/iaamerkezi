<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DisciplinaryBehavior;
use App\Models\DisciplinaryCategory;
use App\Models\DisciplinaryImpact;
use App\Models\DisciplinaryScope;
use App\Models\DisciplinaryMultiplier;
use App\Models\DisciplinaryPenaltyScale;
use Illuminate\Support\Facades\Auth;

class DisciplinarySettingsController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasRole(['Superadmin', 'Hukuk Admini'])) abort(403);

        $categories = DisciplinaryCategory::orderBy('ad')->get();
        $impacts = DisciplinaryImpact::orderBy('puan')->get();
        $scopes = DisciplinaryScope::orderBy('puan')->get();
        $behaviors = DisciplinaryBehavior::with('category')->latest()->get();
        $multipliers = DisciplinaryMultiplier::orderBy('tekrar_sayisi')->get();
        $scales = DisciplinaryPenaltyScale::orderBy('min_puan')->get();

        return view('admin.disiplin.settings', compact('categories', 'impacts', 'scopes', 'behaviors', 'multipliers', 'scales'));
    }

    // --- 1. KATEGORİ ---
    public function storeCategory(Request $request) {
        DisciplinaryCategory::create($request->validate(['ad' => 'required|string']));
        return back()->with('success', 'Kategori eklendi.');
    }
    public function updateCategory(Request $request, DisciplinaryCategory $category) {
        $category->update($request->validate(['ad' => 'required|string']));
        return back()->with('success', 'Kategori güncellendi.');
    }
    public function deleteCategory(DisciplinaryCategory $category) {
        $category->delete();
        return back()->with('success', 'Silindi.');
    }

    // --- 2. ETKİ ---
    public function storeImpact(Request $request) {
        DisciplinaryImpact::create($request->validate(['tanim' => 'required', 'puan' => 'required|integer']));
        return back()->with('success', 'Etki eklendi.');
    }
    public function updateImpact(Request $request, DisciplinaryImpact $impact) {
        $impact->update($request->validate(['tanim' => 'required', 'puan' => 'required|integer']));
        return back()->with('success', 'Etki güncellendi.');
    }
    public function deleteImpact(DisciplinaryImpact $impact) {
        $impact->delete();
        return back()->with('success', 'Silindi.');
    }

    // --- 3. KAPSAM ---
    public function storeScope(Request $request) {
        DisciplinaryScope::create($request->validate(['tanim' => 'required', 'puan' => 'required|integer']));
        return back()->with('success', 'Kapsam eklendi.');
    }
    public function updateScope(Request $request, DisciplinaryScope $scope) {
        $scope->update($request->validate(['tanim' => 'required', 'puan' => 'required|integer']));
        return back()->with('success', 'Kapsam güncellendi.');
    }
    public function deleteScope(DisciplinaryScope $scope) {
        $scope->delete();
        return back()->with('success', 'Silindi.');
    }

    // --- 4. SUÇLAR ---
    public function storeBehavior(Request $request) {
        DisciplinaryBehavior::create($request->validate([
            'category_id' => 'required|exists:disciplinary_categories,id',
            'tanim' => 'required|string',
            'etki_puani' => 'nullable', // Artık matristen geliyor ama veritabanı hatası vermesin diye
            'kapsam_puani' => 'nullable',
            'yasal_dayanak' => 'nullable|string'
        ]));
        return back()->with('success', 'Suç eklendi.');
    }
    public function updateBehavior(Request $request, DisciplinaryBehavior $behavior) {
        $behavior->update($request->validate([
            'category_id' => 'required|exists:disciplinary_categories,id',
            'tanim' => 'required|string',
            'yasal_dayanak' => 'nullable|string'
        ]));
        return back()->with('success', 'Suç güncellendi.');
    }
    public function deleteBehavior(DisciplinaryBehavior $behavior) {
        $behavior->delete();
        return back()->with('success', 'Silindi.');
    }

    // --- 5. DİĞERLERİ (Katsayı/Skala) ---
    public function storeMultiplier(Request $request) {
        DisciplinaryMultiplier::updateOrCreate(['tekrar_sayisi' => $request->tekrar_sayisi], ['katsayi' => $request->katsayi]);
        return back()->with('success', 'Katsayı güncellendi.');
    }
    public function storeScale(Request $request) {
        DisciplinaryPenaltyScale::create($request->all());
        return back()->with('success', 'Skala eklendi.');
    }
    public function deleteScale(DisciplinaryPenaltyScale $scale) {
        $scale->delete();
        return back()->with('success', 'Silindi.');
    }
}