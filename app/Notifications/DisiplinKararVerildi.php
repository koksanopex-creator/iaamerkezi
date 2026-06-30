<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisiplinKararVerildi extends Notification
{
    use Queueable;

    public $case;
    public $forPersonel; // true ise personele özel mesaj, false ise yönetici/lider mesajı

    public function __construct($case, bool $forPersonel = false)
    {
        $this->case = $case;
        $this->forPersonel = $forPersonel;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isCeza = $this->case->final_karar !== 'Savunma Kabul Edildi (Ceza Yok)';
        $kararMetni = $isCeza
            ? "**{$this->case->final_karar}** cezasına çarptırıldı."
            : "hakkındaki disiplin dosyası incelendi ve **ceza verilmedi** (savunma kabul edildi).";

        if ($this->forPersonel) {
            $subject = 'Disiplin Kararı Hakkında Bilgilendirme';
            $line1 = "Hakkınızdaki #{$this->case->id} numaralı disiplin dosyası Disiplin Kurulu tarafından incelendi.";
            $line2 = "Karar: " . ($isCeza ? "**{$this->case->final_karar}** cezası uygulanacak." : "**Savunmanız kabul edildi, ceza uygulanmayacak.**");
            $url = route('disiplin.show', $this->case->id);
            $action = 'Kararı Görüntüle';
        } else {
            // Organizasyonel Bağ Kontrolleri
            $isActualDirector = ($this->case->user->bolum && $this->case->user->bolum->director_id == $notifiable->id);
            $isActualLeader = ($notifiable->hasRole('Bölüm Lideri') && $notifiable->bolum_id == $this->case->user->bolum_id);

            if ($isActualDirector || $isActualLeader) {
                $subject = 'Personeliniz Hakkında Disiplin Kurulu Kararı';
                $line1 = "Bölümünüz personeli **{$this->case->user->name}** hakkındaki disiplin dosyası Disiplin Kurulu tarafından karara bağlandı.";
            } else {
                $subject = 'Disiplin Kurulu Karar Bildirimi';
                $line1 = "**{$this->case->user->name}** hakkındaki disiplin dosyası Disiplin Kurulu tarafından karara bağlandı.";
            }

            $line2 = "Karar: {$this->case->user->name}, {$kararMetni}";
            $url = route('admin.disiplin.show', $this->case->id);
            $action = 'Dosyayı İncele';
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($line1)
            ->line($line2)
            ->action($action, $url)
            ->line('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        $isCeza = $this->case->final_karar !== 'Savunma Kabul Edildi (Ceza Yok)';

        if ($this->forPersonel) {
            $message = $isCeza
                ? 'Disiplin kurulu kararı: ' . $this->case->final_karar . ' cezası uygulanacak. Detayları görüntüleyin.'
                : 'Disiplin kurulu kararı: Savunmanız kabul edildi, ceza verilmedi.';
            $url = route('disiplin.show', $this->case->id);
        } else {
            // Organizasyonel Bağ Kontrolleri
            $isActualDirector = ($this->case->user->bolum && $this->case->user->bolum->director_id == $notifiable->id);
            $isActualLeader = ($notifiable->hasRole('Bölüm Lideri') && $notifiable->bolum_id == $this->case->user->bolum_id);

            $kararText = $isCeza
                ? $this->case->final_karar . ' cezasına çarptırıldı'
                : 'hakkında ceza verilmedi (savunma kabul edildi)';

            if ($isActualDirector || $isActualLeader) {
                $message = 'Bölümünüz personeli ' . $this->case->user->name . ' ' . $kararText . '.';
            } else {
                $message = $this->case->user->name . ' ' . $kararText . '.';
            }
            
            $url = route('admin.disiplin.show', $this->case->id);
        }

        return [
            'message' => $message,
            'url' => $url,
            'icon' => $isCeza ? 'exclamation-circle' : 'check-circle',
            'color' => $isCeza ? 'red' : 'green',
            'case_id' => $this->case->id
        ];
    }
}
