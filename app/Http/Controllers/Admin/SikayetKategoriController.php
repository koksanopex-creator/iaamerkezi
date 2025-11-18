<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SikayetKategori;
use App\Models\Takim; // Takımları çekmek için ekliyoruz
use Illuminate\Http\Request;
use App\Models\SikayetAltKategori;

class SikayetKategoriController extends Controller
{
    public function index()
    {
        $kategoriler = SikayetKategori::with('varsayilanTakim')->latest()->get();
        return view('admin.sikayet_kategorileri.index', compact('kategoriler'));
    }

    public function create()
    {
        // Sadece şikayet takımlarını al
        $takimlar = Takim::where('tur', 'sikayet')->get();
        return view('admin.sikayet_kategorileri.create', compact('takimlar'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ad' => 'required|string|max:255|unique:sikayet_kategorileri,ad',
            'varsayilan_takim_id' => 'nullable|exists:takimlar,id',
        ]);

        // Kategoriyi oluşturup bir değişkene atıyoruz
        $kategori = SikayetKategori::create($request->all());

        // İndex yerine direkt oluşturulan kategorinin EDIT sayfasına yönlendiriyoruz
        // Böylece hemen alt kategori eklemeye başlayabilirsiniz.
        return redirect()
            ->route('admin.sikayet-kategorileri.edit', $kategori->id)
            ->with('success', 'Kategori oluşturuldu. Şimdi alt kategorileri ekleyebilirsiniz.');
    }

    public function destroy(SikayetKategori $sikayetKategori)
    {
        $sikayetKategori->delete();
        return redirect()->route('admin.sikayet-kategorileri.index')->with('success', 'Kategori başarıyla silindi.');
    }

    public function edit(SikayetKategori $sikayetKategori)
    {
        $takimlar = Takim::where('tur', 'sikayet')->get(); // Sadece çözüm takımlarını al
        return view('admin.sikayet_kategorileri.edit', compact('sikayetKategori', 'takimlar'));
    }

    public function update(Request $request, SikayetKategori $sikayetKategori)
    {
        $request->validate([
            // Kategori adını güncellerken, KENDİSİ HARİÇ bu ismin unique olmasını kontrol et
            'ad' => 'required|string|max:255|unique:sikayet_kategorileri,ad,' . $sikayetKategori->id,
            'varsayilan_takim_id' => 'nullable|exists:takimlar,id',
        ]);

        $sikayetKategori->update($request->all());

        return redirect()->route('admin.sikayet-kategorileri.index')->with('success', 'Kategori başarıyla güncellendi.');
    }

    // Class'ın içine, en alta ekle:
    public function getAltKategorilerApi($kategori_id)
    {
        $anaKategori = SikayetKategori::find($kategori_id);
        
        if (!$anaKategori) {
            return response()->json(['alt_kategoriler' => [], 'diger_goster' => false]);
        }

        $altKategoriler = SikayetAltKategori::where('sikayet_kategori_id', $kategori_id)
                                            ->orderBy('ad')
                                            ->get(['id', 'ad']);
                                            
        return response()->json([
            'alt_kategoriler' => $altKategoriler,
            'diger_goster' => (bool) $anaKategori->diger_secenegi_goster,
            'diger_baslik' => $anaKategori->diger_aciklama_basligi ?? 'Diğer Açıklama'
        ]);
    }

    /**
     * Yeni bir alt kategori kaydeder.
     */
    public function storeAltKategori(Request $request, SikayetKategori $sikayetKategori)
    {
        $request->validate([
            'ad' => 'required|string|max:255',
        ]);

        // Alt kategoriyi oluştur ve ana kategoriye bağla
        $sikayetKategori->altKategoriler()->create([
            'ad' => $request->ad,
        ]);

        return back()->with('success', 'Alt kategori başarıyla eklendi.');
    }

    /**
     * Mevcut bir alt kategoriyi siler.
     */
    public function destroyAltKategori(SikayetAltKategori $altKategori)
    {
        $altKategori->delete();
        return back()->with('success', 'Alt kategori başarıyla silindi.');
    }

    /**
     * Alt kategoriyi günceller.
     */
    public function updateAltKategori(Request $request, SikayetAltKategori $altKategori)
    {
        $request->validate([
            'ad' => 'required|string|max:255',
        ]);

        $altKategori->update([
            'ad' => $request->ad
        ]);

        return back()->with('success', 'Alt kategori güncellendi.');
    }
}