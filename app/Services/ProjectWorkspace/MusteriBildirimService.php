<?php

namespace App\Services\ProjectWorkspace;

use App\Models\Iaa;
use App\Models\MusteriSikayetiLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\SikayetTakipBilgilendirmeMail;

class MusteriBildirimService
{
    public function notifyCustomer($id)
    {
        // Yetki Kontrolü
        if (!Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi'])) {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        $iaa = Iaa::findOrFail($id);
        $sikayet = $iaa->musteriSikayeti;

        if (!$sikayet) {
            throw new \Exception('Bu projeye bağlı bir müşteri şikayeti bulunamadı.');
        }

        if (empty($sikayet->musteri_iletisim)) {
            throw new \Exception('Müşteri e-posta adresi kayıtlı değil.');
        }

        // Token ve Şifre
        if (!$sikayet->takip_token) {
            $sikayet->takip_token = Str::random(12);
        }

        $plainPassword = Str::random(8);
        $sikayet->guest_password_hash = Hash::make($plainPassword);

        $sikayet->musteri_bildirim_yapan_id = Auth::id();
        $sikayet->musteri_bildirim_tarihi = now();
        $sikayet->save();

        // Log
        MusteriSikayetiLog::create([
            'musteri_sikayeti_id' => $sikayet->id,
            'user_id' => Auth::id(),
            'eylem' => 'Müşteri Bilgilendirildi',
            'aciklama' => Auth::user()->name . " tarafından müşteriye takip linki gönderildi."
        ]);

        // Mail
        try {
            Mail::to($sikayet->musteri_iletisim)->send(new SikayetTakipBilgilendirmeMail($sikayet, $plainPassword, false));
        } catch (\Exception $e) {
            return [
                'success' => true,
                'message' => 'Müşteri bilgileri oluşturuldu ancak mail gönderilemedi. Şifre: ' . $plainPassword,
                'password' => $plainPassword
            ];
        }

        return [
            'success' => true,
            'message' => 'Müşteriye bildirim gönderildi.',
            'password' => $plainPassword
        ];
    }

    public function resetCustomerPassword($id)
    {
        if (!Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi'])) {
            abort(403);
        }

        $iaa = Iaa::findOrFail($id);
        $sikayet = $iaa->musteriSikayeti;

        if (!$sikayet)
            throw new \Exception('Şikayet bulunamadı.');

        $newPlainPassword = Str::random(8);
        $sikayet->guest_password_hash = Hash::make($newPlainPassword);
        $sikayet->save();

        MusteriSikayetiLog::create([
            'musteri_sikayeti_id' => $sikayet->id,
            'user_id' => Auth::id(),
            'eylem' => 'Müşteri Şifresi Sıfırlandı',
            'aciklama' => Auth::user()->name . " tarafından müşteri şifresi yenilendi."
        ]);

        try {
            Mail::to($sikayet->musteri_iletisim)->send(new SikayetTakipBilgilendirmeMail($sikayet, $newPlainPassword, true));
        } catch (\Exception $e) {
        }

        return [
            'success' => true,
            'message' => 'Şifre sıfırlandı.',
            'password' => $newPlainPassword
        ];
    }
}
