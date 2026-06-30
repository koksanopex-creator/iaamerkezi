<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Takim;
use App\Models\User;
use App\Models\MusteriSikayeti; // Şikayet modelini kontrol için ekledik
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Transaction için eklendi
use Spatie\Permission\Models\Role; // Rolü kullanmak için import et
use Illuminate\Validation\Rule; // Unique kuralı için ekledik

class CozumTakimiController extends Controller
{
    /**
     * Sadece 'sikayet' türündeki takımları listeler.
     */
    public function index()
    {
        $cozumTakimlari = Takim::where('tur', 'sikayet')
            ->with('lider') // Lider bilgisini de al
            ->latest()
            ->get();

        return view('admin.cozum_takimlari.index', compact('cozumTakimlari'));
    }

    /**
     * Yeni çözüm takımı oluşturma formunu gösterir.
     * Sadece 'Müşteri Şikayeti Çözüm Lideri' rolündeki kullanıcıları listeler.
     */
    public function create()
    {
        $liderler = User::role('Müşteri Şikayeti Çözüm Lideri')
            ->where('onaylandi_mi', true) // Onaylı kullanıcıları al
            ->orderBy('name')
            ->get();

        return view('admin.cozum_takimlari.create', compact('liderler'));
    }

    /**
     * Yeni 'sikayet' takımı oluşturur ve liderini atar.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Takım adının, 'sikayet' türündeki diğer takımlar içinde benzersiz olmasını sağla
            'ad' => [
                'required',
                'string',
                'max:255',
                Rule::unique('takimlar', 'ad')->where(function ($query) {
                    return $query->where('tur', 'sikayet');
                })
            ],
            'lider_user_id' => [
                'required',
                'exists:users,id',
                // Seçilen kullanıcının lider rolüne sahip olduğunu doğrula
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if (!$user || !$user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
                        $fail('Seçilen kullanıcı geçerli bir çözüm lideri değil.');
                    }
                },
            ],
            // Gizli alandan tür bilgisini alıyoruz, create view'de eklenmeli
            'tur' => 'required|in:sikayet'
        ]);

        $takim = Takim::create([
            'ad' => $request->ad,
            'lider_user_id' => $request->lider_user_id,
            'tur' => $request->tur, // 'sikayet' olarak ayarlanacak
        ]);

        // Lideri takıma "Kurucu Lider" olarak ekle
        $takim->uyeler()->syncWithoutDetaching([$request->lider_user_id => ['katilma_sekli' => 'Kurucu Lider']]);

        return redirect()->route('admin.cozum-takimlari.index')->with('success', 'Çözüm takımı başarıyla oluşturuldu.');
    }

    /**
     * Belirtilen çözüm takımının detaylarını gösterir.
     */
    public function show(Request $request, Takim $cozumTakimi)
    {
        // Tür kontrolü
        if ($cozumTakimi->tur !== 'sikayet') {
            abort(404, 'İlgili çözüm takımı bulunamadı.');
        }

        // İlişkileri yükle
        $cozumTakimi->load(['lider', 'uyeler']);

        // --- FİLTRELEME PARAMETRELERİ ---
        $statusFilter = $request->input('durum'); // 'aktif', 'cozulmus', 'hepsi' veya spesifik bir durum
        $customerFilter = $request->input('musteri_id');
        $dateStart = $request->input('baslangic_tarihi');
        $dateEnd = $request->input('bitis_tarihi');

        // Temel Sorgu
        $query = MusteriSikayeti::where('atanan_cozum_takimi_id', $cozumTakimi->id)
            ->with(['customer', 'yetkili_user', 'sikayetKategori']);

        // 1. Durum Filtresi
        if ($statusFilter) {
            if ($statusFilter === 'cozulmus') {
                $query->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı', 'Talep Olarak Kapatıldı']);
            } elseif ($statusFilter === 'devam_eden') {
                $query->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı', 'Talep Olarak Kapatıldı']);
            } elseif ($statusFilter !== 'hepsi') {
                $query->where('musteri_durum', $statusFilter);
            }
        }

        // 2. Müşteri Filtresi
        if ($customerFilter) {
            $query->where('customer_id', $customerFilter);
        }

        // 3. Tarih Filtresi
        if ($dateStart) {
            $query->whereDate('created_at', '>=', $dateStart);
        }
        if ($dateEnd) {
            $query->whereDate('created_at', '<=', $dateEnd);
        }

        $sikayetler = $query->latest()->paginate(20)->withQueryString();

        // --- İSTATİSTİKLER (Filtreden Bağımsız) ---
        // Kartlara tıklanınca filtreleme yapması için sabit kalmalı
        $toplamSikayet = MusteriSikayeti::where('atanan_cozum_takimi_id', $cozumTakimi->id)->count();
        $cozulmusSikayet = MusteriSikayeti::where('atanan_cozum_takimi_id', $cozumTakimi->id)
            ->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı', 'Talep Olarak Kapatıldı'])
            ->count();
        $devamEdenSikayet = $toplamSikayet - $cozulmusSikayet;

        // Filtre Seçenekleri İçin Müşteri Listesi (Sadece bu takımın şikayetlerinde geçen müşteriler)
        $musteriler = \App\Models\Customer::whereHas('sikayetler', function ($q) use ($cozumTakimi) {
            $q->where('atanan_cozum_takimi_id', $cozumTakimi->id);
        })->orderBy('name')->get();

        return view('admin.cozum_takimlari.show', compact(
            'cozumTakimi',
            'sikayetler',
            'toplamSikayet',
            'cozulmusSikayet',
            'devamEdenSikayet',
            'musteriler'
        ));
    }

    /**
     * Belirtilen çözüm takımını düzenleme formunu gösterir.
     * === DÜZELTİLDİ: Parametre adı '$cozumTakimi' (tekil) olarak değiştirildi ===
     */
    public function edit(Takim $cozumTakimi)
    {
        // === KONTROL GERİ EKLENDİ ===
        // Sadece 'sikayet' türündeki takımların düzenlenmesine izin ver
        if ($cozumTakimi->tur !== 'sikayet') { // <-- DEĞİŞTİ
            abort(404, 'İlgili çözüm takımı bulunamadı.');
        }
        // ============================

        $liderler = User::role('Müşteri Şikayeti Çözüm Lideri')
            ->where('onaylandi_mi', true) // Onaylı kullanıcıları al
            ->orderBy('name')
            ->get();

        // View'e 'takim' anahtarıyla gönderiyoruz
        return view('admin.cozum_takimlari.edit', [
            'takim' => $cozumTakimi, // <-- DEĞİŞTİ
            'liderler' => $liderler
        ]);
    }

    /**
     * Belirtilen çözüm takımını günceller.
     * === DÜZELTİLDİ: Parametre adı '$cozumTakimi' (tekil) olarak değiştirildi ===
     */
    public function update(Request $request, Takim $cozumTakimi)
    {
        // === KONTROL BURADA DA OLMALI ===
        if ($cozumTakimi->tur !== 'sikayet') { // <-- DEĞİŞTİ
            abort(404);
        }
        // ==============================

        $request->validate([
            // Takım adının, GÜNCELLENEN TAKIM HARİÇ, 'sikayet' türündeki diğer takımlar içinde benzersiz olmasını sağla
            'ad' => [
                'required',
                'string',
                'max:255',
                Rule::unique('takimlar', 'ad')->where(function ($query) {
                    return $query->where('tur', 'sikayet');
                })->ignore($cozumTakimi->id) // <-- DEĞİŞTİ: Kendisi hariç kontrol et
            ],
            'lider_user_id' => [
                'required',
                'exists:users,id',
                // Seçilen kullanıcının lider rolüne sahip olduğunu doğrula
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if (!$user || !$user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
                        $fail('Seçilen kullanıcı geçerli bir çözüm lideri değil.');
                    }
                },
            ],
            // Gizli alandan tür bilgisini alıyoruz, edit view'de eklenmeli
            'tur' => 'required|in:sikayet'
        ]);

        $eskiLiderId = $cozumTakimi->lider_user_id; // <-- DEĞİŞTİ
        $yeniLiderId = $request->lider_user_id;

        $cozumTakimi->update([ // <-- DEĞİŞTİ
            'ad' => $request->ad,
            'lider_user_id' => $yeniLiderId,
            // 'tur' alanı zaten hidden input ile geliyor ve değişmiyor.
        ]);

        // Eğer lider değiştiyse, takım üyeliğini güncelle
        if ($eskiLiderId != $yeniLiderId) {
            // Eski lideri çıkar (eğer sadece lider olarak eklenmişse)
            $eskiLiderUyelik = $cozumTakimi->uyeler()->where('user_id', $eskiLiderId)->first(); // <-- DEĞİŞTİ
            if ($eskiLiderUyelik && $eskiLiderUyelik->pivot->katilma_sekli === 'Kurucu Lider') {
                // Sadece pivot kaydını silmek yerine detach kullanmak daha güvenli olabilir
                $cozumTakimi->uyeler()->detach($eskiLiderId); // <-- DEĞİŞTİ
            }

            // Yeni lideri ekle veya pivot bilgisini 'Kurucu Lider' olarak güncelle
            $cozumTakimi->uyeler()->syncWithoutDetaching([$yeniLiderId => ['katilma_sekli' => 'Kurucu Lider']]); // <-- DEĞİŞTİ

            // === PROJE SQUAD SENKRONİZASYONU ===
            // Lider değiştiğinde, tamamlanmamış projelerdeki iaa_user pivot tablosunu güncelle.
            // Bu sayede yeni lider projelerde görünür ve puan alabilir.
            $tamamlanmamisDurumlar = ['Tamamlandı', 'Reddedildi', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi'];
            $tamamlanmamisProjeler = \App\Models\Iaa::where('atanan_takim_id', $cozumTakimi->id)
                ->whereNotIn('durum', $tamamlanmamisDurumlar)
                ->get();

            foreach ($tamamlanmamisProjeler as $proje) {
                // Eski lideri Squad'dan çıkar (sadece 'Lider' rolüyle eklenmişse)
                $proje->projeEkibi()->wherePivot('rol', 'Lider')->where('user_id', $eskiLiderId)->detach();

                // Yeni lideri Squad'a ekle
                $proje->projeEkibi()->syncWithoutDetaching([
                    $yeniLiderId => [
                        'rol' => 'Lider',
                        'kazanilan_puan' => $proje->puan ?? 0,
                        'durum' => 'onaylandi'
                    ]
                ]);
            }

            \Illuminate\Support\Facades\Log::info("Çözüm Takımı lider değişikliği: Takım #{$cozumTakimi->id}, Eski Lider: #{$eskiLiderId} → Yeni Lider: #{$yeniLiderId}. " . $tamamlanmamisProjeler->count() . " projenin Squad'ı güncellendi.");
            // === PROJE SQUAD SENKRONİZASYONU SONU ===
        }

        return redirect()->route('admin.cozum-takimlari.index')->with('success', 'Çözüm takımı başarıyla güncellendi.');
    }

    /**
     * Belirtilen çözüm takımını siler.
     * === DÜZELTİLDİ: Parametre adı '$cozumTakimi' (tekil) olarak değiştirildi ===
     */
    public function destroy(Takim $cozumTakimi)
    {
        // Yetki kontrolü eklenebilir: $this->authorize('delete', $cozumTakimi);

        // Takımın 'sikayet' türünde olduğundan emin ol
        if ($cozumTakimi->tur !== 'sikayet') { // <-- DEĞİŞTİ
            abort(404);
        }

        // KONTROL: Bu takıma atanmış şikayet var mı?
        if (MusteriSikayeti::where('atanan_cozum_takimi_id', $cozumTakimi->id)->exists()) { // <-- DEĞİŞTİ
            return back()->with('error', '"' . $cozumTakimi->ad . '" takımına atanmış şikayetler bulunduğu için silinemez. Lütfen önce şikayetlerin atamasını değiştirin.'); // <-- DEĞİŞTİ
        }

        try {
            DB::transaction(function () use ($cozumTakimi) { // <-- DEĞİŞTİ
                // Takımın üyelerini ayır (pivot tablodan sil)
                $cozumTakimi->uyeler()->detach(); // <-- DEĞİŞTİ

                // Takımı sil
                $cozumTakimi->delete(); // <-- DEĞİŞTİ
            });

            return redirect()->route('admin.cozum-takimlari.index')->with('success', 'Çözüm takımı başarıyla silindi.');

        } catch (\Exception $e) {
            // Loglama yapılabilir: Log::error('Takım silinirken hata: ' . $e->getMessage());
            return back()->with('error', 'Takım silinirken bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }
}