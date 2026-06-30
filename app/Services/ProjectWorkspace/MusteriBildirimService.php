<?php

namespace App\Services\ProjectWorkspace;

use App\Models\Iaa;
use App\Models\SikayetGuestPassword;
use App\Models\MusteriSikayetiLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\SikayetTakipBilgilendirmeMail;

class MusteriBildirimService
{
    /**
     * Seçilen alıcılara bildirim gönderir.
     * Her alıcıya aynı takip URL'i, farklı şifre.
     *
     * @param int $id IAA proje ID
     * @param array $recipients [['email' => '...', 'name' => '...', 'type' => 'firma_iletisim|yetkili|musteri_iletisim'], ...]
     */
    public function notifyCustomer($id, array $recipients)
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

        // Seçilmemiş alıcıları filtrele (checkbox kapalı olanlar email göndermiyor)
        $recipients = array_filter($recipients, function ($r) {
            return !empty($r['email']) && !empty($r['name']) && !empty($r['type']);
        });
        $recipients = array_values($recipients);

        if (empty($recipients)) {
            throw new \Exception('En az bir alıcı seçmelisiniz.');
        }

        // Token yoksa oluştur
        if (!$sikayet->takip_token) {
            $sikayet->takip_token = Str::random(12);
        }

        // Bildirim bilgilerini güncelle
        $sikayet->musteri_bildirim_yapan_id = Auth::id();
        $sikayet->musteri_bildirim_tarihi = now();
        $sikayet->save();

        $passwords = [];
        $failedEmails = [];

        foreach ($recipients as $recipient) {
            $plainPassword = Str::random(8);

            // Yeni tabloya kaydet
            $guestPassword = SikayetGuestPassword::create([
                'musteri_sikayeti_id' => $sikayet->id,
                'email' => $recipient['email'],
                'recipient_name' => $recipient['name'],
                'recipient_type' => $recipient['type'],
                'password_hash' => Hash::make($plainPassword),
                'sent_by' => Auth::id(),
                'sent_at' => now(),
            ]);

            // Geriye uyumluluk: İlk alıcının şifresini eski sütuna da yaz
            if (empty($passwords)) {
                $sikayet->guest_password_hash = Hash::make($plainPassword);
                $sikayet->save();
            }

            // E-posta gönder
            try {
                $rawEmail = $recipient['email'];
                $emailToUse = $rawEmail;
                if (preg_match('/[<\\(]?([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})[>\\)]?/', $rawEmail, $matches)) {
                    $emailToUse = $matches[1];
                }

                Mail::to($emailToUse)->queue(
                    new SikayetTakipBilgilendirmeMail($sikayet, $plainPassword, false)
                );
            } catch (\Exception $e) {
                $failedEmails[] = $recipient['email'];
                \Log::error('Müşteri bilgilendirme maili gönderilemedi: ' . $e->getMessage());
            }

            $passwords[] = [
                'email' => $recipient['email'],
                'name' => $recipient['name'],
                'password' => $plainPassword,
            ];
        }

        // Log
        $aliciListesi = collect($recipients)->pluck('name')->implode(', ');
        MusteriSikayetiLog::create([
            'musteri_sikayeti_id' => $sikayet->id,
            'user_id' => Auth::id(),
            'eylem' => 'Müşteri Bilgilendirildi',
            'aciklama' => Auth::user()->name . " tarafından şu alıcılara takip bilgileri gönderildi: " . $aliciListesi
        ]);

        $message = 'Müşteriye bildirim gönderildi.';
        if (!empty($failedEmails)) {
            $message .= ' Ancak şu adreslere mail gönderilemedi: ' . implode(', ', $failedEmails);
        }

        return [
            'success' => true,
            'message' => $message,
            'passwords' => $passwords,
        ];
    }

    /**
     * Belirli bir alıcının şifresini sıfırlar.
     *
     * @param int $id IAA proje ID
     * @param int|null $guestPasswordId Belirli alıcı kaydı (null ise tüm alıcılar)
     */
    public function resetCustomerPassword($id, $guestPasswordId = null)
    {
        if (!Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi'])) {
            abort(403);
        }

        $iaa = Iaa::findOrFail($id);
        $sikayet = $iaa->musteriSikayeti;

        if (!$sikayet) {
            throw new \Exception('Şikayet bulunamadı.');
        }

        if ($guestPasswordId) {
            // Belirli bir alıcının şifresini sıfırla
            $guestPassword = SikayetGuestPassword::where('id', $guestPasswordId)
                ->where('musteri_sikayeti_id', $sikayet->id)
                ->firstOrFail();

            $newPlainPassword = Str::random(8);
            $guestPassword->password_hash = Hash::make($newPlainPassword);
            $guestPassword->sent_at = now();
            $guestPassword->sent_by = Auth::id();
            $guestPassword->save();

            MusteriSikayetiLog::create([
                'musteri_sikayeti_id' => $sikayet->id,
                'user_id' => Auth::id(),
                'eylem' => 'Müşteri Şifresi Sıfırlandı',
                'aciklama' => Auth::user()->name . " tarafından " . $guestPassword->recipient_name . " (" . $guestPassword->email . ") şifresi yenilendi."
            ]);
            $rawEmail = $guestPassword->email;
            $emailToUse = $rawEmail;

            // Eğer "İsim (email)" veya "İsim <email>" formatındaysa sadece emaili al
            if (preg_match('/[<\\(]?([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})[>\\)]?/', $rawEmail, $matches)) {
                $emailToUse = $matches[1];
            }

            try {
                Mail::to($emailToUse)->queue(
                    new SikayetTakipBilgilendirmeMail($sikayet, $newPlainPassword, true)
                );
            } catch (\Exception $e) {
                \Log::error('Şifre sıfırlama maili gönderilemedi: ' . $e->getMessage());
            }

            return [
                'success' => true,
                'message' => $guestPassword->recipient_name . ' için şifre sıfırlandı.',
                'password' => $newPlainPassword,
            ];
        } else {
            // Eski davranış: genel şifre sıfırlama (geriye uyumluluk)
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
                Mail::to($sikayet->musteri_iletisim)->queue(
                    new SikayetTakipBilgilendirmeMail($sikayet, $newPlainPassword, true)
                );
            } catch (\Exception $e) {
                \Log::error('Şifre sıfırlama maili gönderilemedi: ' . $e->getMessage());
            }

            return [
                'success' => true,
                'message' => 'Şifre sıfırlandı.',
                'password' => $newPlainPassword,
            ];
        }
    }
}
