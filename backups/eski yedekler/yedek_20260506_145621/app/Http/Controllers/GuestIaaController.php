<?php

namespace App\Http\Controllers;

use App\Models\Bolum;
use App\Models\Iaa;
use App\Models\IaaResim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\YeniIaaOnerisi;
use Illuminate\Support\Facades\Notification;


class GuestIaaController extends Controller
{
    /**
     * Misafirler için İAA önerme formunu gösterir.
     */
    public function create()
    {
        // Artık bölümleri göndermiyoruz.
        $paraBirimleri = ['TL', 'USD', 'EUR'];
        return view('guest.iaa.create', compact('paraBirimleri'));
    }

    /**
     * Misafir tarafından gönderilen İAA önerisini veritabanına kaydeder.
     */
    public function store(Request $request)
    {
        // Doğrulama kurallarını güncelliyoruz.
        $validatedData = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'baslik' => 'required|string|max:255',
            'ilgili_alan' => 'required|string|max:255',
            'mevcut_durum' => 'required|string|min:20',
            'oneri' => 'nullable|string|min:20',
            // EKSİK OLAN DOĞRULAMA KURALLARI EKLENDİ
            'oneren_kazanc_miktar' => 'nullable|numeric|min:0',
            'oneren_kazanc_birim' => 'nullable|string|in:TL,USD,EUR',
            'oneren_butce_miktar' => 'nullable|numeric|min:0',
            'oneren_butce_birim' => 'nullable|string|in:TL,USD,EUR',
            'resimler' => 'nullable|array|max:5',
            'resimler.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $validatedData) {
                // Veritabanına kaydı güncelliyoruz.
                $iaa = Iaa::create([
                    'gonderen_user_id' => null,
                    'guest_name' => $validatedData['guest_name'],
                    'guest_email' => $validatedData['guest_email'] ?? null,
                    'baslik' => $validatedData['baslik'],
                    'bolum_id' => null,
                    'ilgili_alan' => $validatedData['ilgili_alan'],
                    'mevcut_durum' => $validatedData['mevcut_durum'],
                    'oneri' => $validatedData['oneri'] ?? null,
                    // EKSİK OLAN ALANLAR EKLENDİ
                    'oneren_kazanc_miktar' => $validatedData['oneren_kazanc_miktar'] ?? null,
                    'oneren_kazanc_birim' => $validatedData['oneren_kazanc_birim'] ?? null,
                    'oneren_butce_miktar' => $validatedData['oneren_butce_miktar'] ?? null,
                    'oneren_butce_birim' => $validatedData['oneren_butce_birim'] ?? null,
                    'durum' => 'Onay Bekliyor',
                ]);

                // Eğer resimler varsa, onları kaydet
                if ($request->hasFile('resimler')) {
                    foreach ($request->file('resimler') as $resim) {
                        $filename = 'guest_iaa_' . now()->format('Ymd_Hisu') . '.' . $resim->getClientOriginalExtension();
                        $path = $resim->storeAs('iaa_resimleri', $filename, 'public');
                        
                        IaaResim::create([
                            'iaa_id' => $iaa->id,
                            'dosya_yolu' => $path,
                        ]);
                    }
                }

                // === BİLDİRİM: Superadmin'lere Yeni Öneri Bildirimi Gönder ===
                $superadmins = User::role('Superadmin')->get();
                if ($superadmins->isNotEmpty()) {
                    Notification::send($superadmins, new YeniIaaOnerisi($iaa, $validatedData['guest_name']));
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Öneriniz gönderilirken bir hata oluştu. Lütfen tekrar deneyin.');
        }

        return redirect()->route('guest.iaa.create')->with('success', 'Öneriniz başarıyla alınmıştır! Değerlendirme için teşekkür ederiz.');
    }
}