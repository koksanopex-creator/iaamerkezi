<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Mail\Events\MessageSent;
use App\Models\EmailLog;

class LogSentEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        try {
            $message = $event->message;
            $subject = $message->getSubject() ?? '(Konusuz)';
            
            $toAddresses = [];
            foreach ($message->getTo() as $address) {
                $toAddresses[] = $address->getAddress();
            }
            $toAddressesStr = implode(', ', $toAddresses);

            $icerik = $message->getHtmlBody();
            if (empty($icerik)) {
                $icerik = $message->getTextBody();
            }

            // Kategoriyi konuya göre tahmin etme
            $kategori = 'Sistem / Diğer';
            $konuKucuk = mb_strtolower($subject);
            
            if (str_contains($konuKucuk, 'rapor') || str_contains($konuKucuk, 'özet')) {
                $kategori = 'Raporlar';
            } elseif (str_contains($konuKucuk, 'proje') || str_contains($konuKucuk, 'iaa') || str_contains($konuKucuk, 'öneri')) {
                $kategori = 'Proje Bildirimleri';
            } elseif (str_contains($konuKucuk, 'şikayet') || str_contains($konuKucuk, 'müşteri')) {
                $kategori = 'Şikayet Bildirimleri';
            } elseif (str_contains($konuKucuk, 'disiplin')) {
                $kategori = 'Disiplin Bildirimleri';
            } elseif (str_contains($konuKucuk, 'şifre') || str_contains($konuKucuk, 'hesap') || str_contains($konuKucuk, 'davet') || str_contains($konuKucuk, 'giriş')) {
                $kategori = 'Kullanıcı/Sistem';
            }

            EmailLog::create([
                'kategori' => $kategori,
                'alici_email' => $toAddressesStr,
                'konu' => $subject,
                'icerik' => is_resource($icerik) ? stream_get_contents($icerik) : $icerik,
            ]);
        } catch (\Exception $e) {
            \Log::error('Mail loglanırken hata oluştu: ' . $e->getMessage());
        }
    }
}
