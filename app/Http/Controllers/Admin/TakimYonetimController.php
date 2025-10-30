<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Iaa;    // <-- Bu satırın doğru olduğundan emin olun
use App\Models\Takim;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule; // Bu satırı ekleyin


class TakimYonetimController extends Controller
{
    /**
     * ==========================================================
     * TAKIM LİSTELEME SAYFASI
     * ==========================================================
     * Sistemdeki tüm takımları, lider bilgileri ve üye sayıları
     * ile birlikte alıp view'e gönderir.
     */
    public function index()
    {
        // with('lider') -> Her takımın liderinin bilgilerini tek seferde çeker (N+1 problemini önler)
        // withCount('uyeler') -> Her takım için üye sayısını 'uyeler_count' adında bir alanda hesaplar
        $takimlar = Takim::with('lider')->withCount('uyeler')->latest()->paginate(10);

        return view('admin.takim-yonetim.index', compact('takimlar'));
    }

    /**
     * ==========================================================
     * YENİ TAKIM OLUŞTURMA FORMUNU GÖSTERİR
     * ==========================================================
     * Lider olarak atanabilecek tüm kullanıcıları listeler ve
     * takım oluşturma formunu gösterir.
     */
    public function create()
    {
        // Lider seçimi için tüm kullanıcıları alıyoruz.
        $kullanicilar = User::orderBy('name')->get();
        return view('admin.takim-yonetim.create', compact('kullanicilar'));
    }

    /**
     * ==========================================================
     * YENİ TAKIMI VERİTABANINA KAYDEDER
     * ==========================================================
     * Formdan gelen veriyi doğrular, yeni takımı oluşturur ve
     * seçilen lideri takıma ilk üye olarak ekler.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255|unique:takimlar,ad',
            'lider_user_id' => 'required|exists:users,id',
            'amac' => 'nullable|string',
            'vizyon' => 'nullable|string',
            'misyon' => 'nullable|string',
            'kurallar' => 'nullable|string',
        ]);

        // Transaction ile güvenli kayıt
        DB::transaction(function () use ($validated) {
            // 1. Yeni takımı oluştur
            $takim = Takim::create($validated);

            // 2. Atanan lideri, aynı zamanda takıma ilk üye olarak ekle
            $takim->uyeler()->attach($validated['lider_user_id'], [
                'katilma_sekli' => 'Lider olarak atandı',
                'onay_durumu' => 'onaylandi', // Liderin onaya ihtiyacı yok
                'onaylayan_user_id' => auth()->id(),
                'onay_tarihi' => now()
            ]);
        });

        return redirect()->route('admin.takim-yonetim.index')->with('success', 'Takım başarıyla oluşturuldu ve lider atandı.');
    }

    /**
     * ==========================================================
     * BELİRLİ BİR TAKIMIN DETAYLARINI GÖSTERİR
     * ==========================================================
     * Üye listesi, potansiyel yeni üyeler ve havuza proje atama formu
     * için gerekli tüm verileri hazırlar ve view'e gönderir.
     */
    public function show(Takim $takim)
    {
        // Takımın ilişkili verilerini (üyeler, lider, atanmış projeler) önceden yüklüyoruz.
        $takim->load('uyeler', 'lider', 'atananProjeler');
        
        // Takıma eklenebilecek potansiyel üyeleri buluyoruz (mevcut üyeler hariç).
        $mevcutUyeIdleri = $takim->uyeler->pluck('id');
        $potansiyelUyeler = User::whereNotIn('id', $mevcutUyeIdleri)->orderBy('name')->get();

        // Takıma atanabilecek, durumu "Havuzda" olan İAA'ları alıyoruz.
        $havuzdakiOneriler = Iaa::where('durum', 'Havuzda')->orderBy('baslik')->get();

        return view('admin.takim-yonetim.show', compact('takim', 'potansiyelUyeler', 'havuzdakiOneriler'));
    }

    /**
     * ==========================================================
     * YENİ: BİR TAKIMA YENİ ÜYE EKLER
     * ==========================================================
     */
    public function uyeEkle(Request $request, Takim $takim)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Kullanıcının zaten üye olup olmadığını kontrol et
        if ($takim->uyeler()->where('user_id', $validated['user_id'])->exists()) {
            return back()->with('error', 'Bu kullanıcı zaten takımın bir üyesi.');
        }

        // Yeni üyeyi takıma ekle
        $takim->uyeler()->attach($validated['user_id'], [
            'katilma_sekli' => 'Yönetici tarafından eklendi',
            'onay_durumu' => 'onaylandi',
            'onaylayan_user_id' => auth()->id(),
            'onay_tarihi' => now()
        ]);

        return back()->with('success', 'Üye başarıyla takıma eklendi.');
    }
    
    /**
     * ==========================================================
     * YENİ: BİR ÜYEYİ TAKIMDAN ÇIKARIR
     * ==========================================================
     */
    public function uyeCikar(Takim $takim, User $user)
    {
        // Lideri takımdan atmayı engelle
        if ($takim->lider_user_id === $user->id) {
            return back()->with('error', 'Takım liderini takımdan çıkaramazsınız. Lideri değiştirmek için takımı düzenleyin.');
        }

        $takim->uyeler()->detach($user->id);
        
        return back()->with('success', 'Üye başarıyla takımdan çıkarıldı.');
    }
    
    /**
     * ==========================================================
     * YENİ: BİR TAKIMA HAVUZDAN PROJE ATAR
     * ==========================================================
     */
    public function projeAta(Request $request, Takim $takim)
    {
        $validated = $request->validate([
            'iaa_id' => 'required|exists:iaas,id',
        ]);

        $iaa = Iaa::find($validated['iaa_id']);

        // Projenin hala havuzda olduğundan emin ol
        if ($iaa->durum !== 'Havuzda') {
            return back()->with('error', 'Bu proje artık havuzda değil, başka bir takıma atanmış olabilir.');
        }

        $iaa->durum = 'Atandı';
        $iaa->atanan_takim_id = $takim->id;
        $iaa->save();

        return back()->with('success', 'Proje başarıyla takıma atandı.');
    }

     /**
     * ==========================================================
     * TAKIM DÜZENLEME FORMUNU GÖSTERİR
     * ==========================================================
     */
    public function edit(Takim $takim)
    {
        // Lideri değiştirebilmek için tüm kullanıcıları alıyoruz.
        $kullanicilar = User::orderBy('name')->get();
        return view('admin.takim-yonetim.edit', compact('takim', 'kullanicilar'));
    }

    /**
     * ==========================================================
     * TAKIM BİLGİLERİNİ GÜNCELLER
     * ==========================================================
     */
    public function update(Request $request, Takim $takim)
    {
        $validated = $request->validate([
            // Takım adı benzersiz olmalı, ama mevcut takımın adını görmezden gel.
            'ad' => ['required', 'string', 'max:255', Rule::unique('takimlar')->ignore($takim->id)],
            'lider_user_id' => 'required|exists:users,id',
            'amac' => 'nullable|string',
            'vizyon' => 'nullable|string',
            'misyon' => 'nullable|string',
            'kurallar' => 'nullable|string',
        ]);

        $eskiLiderId = $takim->lider_user_id;
        $yeniLiderId = $validated['lider_user_id'];

        // 1. Takımın ana bilgilerini güncelle
        $takim->update($validated);
        
        // 2. Eğer lider değiştirildiyse, üyelik durumunu güncelle
        if ($eskiLiderId != $yeniLiderId) {
            // Yeni liderin zaten üye olduğundan emin ol
            $takim->uyeler()->syncWithoutDetaching([
                $yeniLiderId => [
                    'katilma_sekli' => 'Lider olarak atandı',
                    'onay_durumu' => 'onaylandi',
                    'onaylayan_user_id' => auth()->id(),
                    'onay_tarihi' => now()
                ]
            ]);
        }
        
        return redirect()->route('admin.takim-yonetim.index')->with('success', 'Takım başarıyla güncellendi.');
    }

    /**
     * Belirtilen takımı ve tüm ilişkili verilerini sorgusuzca siler.
     */
    public function destroy(Takim $takim)
    {
        try {
            // Transaction başlat: Herhangi bir adımda hata olursa tüm işlemler geri alınır.
            DB::transaction(function () use ($takim) {
                
                // 1. Atanmış Projeleri Havuza Geri Döndür
                // Bu takıma atanmış ve henüz tamamlanmamış tüm projelerin durumunu 'Havuzda' yapar
                // ve atanan_takim_id'sini null'a çeker.
                $takim->atananProjeler()->update([
                    'durum' => 'Havuzda',
                    'atanan_takim_id' => null
                ]);

                // 2. Takımın Tüm Proje Taleplerini Sil
                // Bu takımın herhangi bir projeye yaptığı tüm talepleri 'iaa_talepleri' tablosundan temizler.
                DB::table('iaa_talepleri')->where('takim_id', $takim->id)->delete();

                // 3. Takımın Tüm Davetiyelerini ve Katılma İsteklerini Sil
                // Bu takımla ilgili tüm açık davet ve istekleri temizler.
                $takim->davetiyeler()->delete();

                // 4. Tüm Üyelikleri Kaldır
                // Takımdaki tüm kullanıcıların üyeliklerini 'takim_user' pivot tablosundan kaldırır.
                $takim->uyeler()->detach();

                // 5. Takımı Kalıcı Olarak Sil
                // Tüm ilişkiler temizlendikten sonra, takımın kendisini 'takimlar' tablosundan siler.
                $takim->delete();
            });

        } catch (\Exception $e) {
            // Beklenmedik bir veritabanı hatası olursa kullanıcıyı bilgilendir.
            return back()->with('error', 'Takım silinirken bir veritabanı hatası oluştu. Detay: ' . $e->getMessage());
        }
        
        // Başarılı olursa ana sayfaya yönlendir.
        return redirect()->route('admin.takim-yonetim.index')->with('success', 'Takım ve tüm bağlantıları başarıyla silindi.');
    }
}