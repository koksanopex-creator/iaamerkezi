<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DisciplinaryCase;

class DisiplinPersonelKurulaSevkBildirimi extends Notification
{
    use Queueable;

    public DisciplinaryCase $case;
    public bool $isPersonel;

    public function __construct(DisciplinaryCase $case, bool $isPersonel)
    {
        $this->case = $case;
        $this->isPersonel = $isPersonel;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $toplantiTarihi = $this->case->toplanti_tarihi ? $this->case->toplanti_tarihi->format('d.m.Y H:i') : 'Belirtilmedi';
        $url = route('admin.disiplin.show', $this->case->id);
        $name = $notifiable->name ?? 'Kullanıcı';

        if ($this->isPersonel) {
            $mesaj = "Hakkınızda açılan disiplin dosyası Disiplin Kurulu'na sevk edilmiştir.";
        } else {
            // Organizasyonel Bağ Kontrolleri
            $isActualDirector = ($this->case->user->bolum && $this->case->user->bolum->director_id == $notifiable->id);
            $isActualLeader = ($notifiable->hasRole('Bölüm Lideri') && $notifiable->bolum_id == $this->case->user->bolum_id);

            if ($isActualDirector || $isActualLeader) {
                $mesaj = "Bölümünüz personeli **{$this->case->user->name}** hakkında açılan disiplin dosyası Disiplin Kurulu'na sevk edilmiştir.";
            } else {
                $mesaj = "**{$this->case->user->name}** hakkında açılan disiplin dosyası Disiplin Kurulu'na sevk edilmiştir.";
            }
        }

        return (new MailMessage)
            ->subject('Disiplin Dosyası Kurula Sevk Edildi')
            ->greeting("Merhaba {$name},")
            ->line($mesaj)
            ->line("Kurul Toplantı Tarihi: {$toplantiTarihi}")
            ->action('Dosyayı Görüntüle', $url)
            ->line('Saygılarımızla, Köksan İAA Sistemi');
    }

    public function toArray(object $notifiable): array
    {
        $toplantiTarihi = $this->case->toplanti_tarihi ? $this->case->toplanti_tarihi->format('d.m.Y H:i') : 'Belirtilmedi';
        $url = route('admin.disiplin.show', $this->case->id);

        if ($this->isPersonel) {
            $mesaj = "Dosyanız disiplin kurulunda görüşülmek üzere {$toplantiTarihi} toplantısına sevk edildi.";
        } else {
            // Organizasyonel Bağ Kontrolleri
            $isActualDirector = ($this->case->user->bolum && $this->case->user->bolum->director_id == $notifiable->id);
            $isActualLeader = ($notifiable->hasRole('Bölüm Lideri') && $notifiable->bolum_id == $this->case->user->bolum_id);

            if ($isActualDirector || $isActualLeader) {
                $mesaj = "Bölümünüz personeli {$this->case->user->name}'in dosyası Disiplin Kurulu'na sevk edildi.";
            } else {
                $mesaj = "{$this->case->user->name}'in dosyası Disiplin Kurulu'na sevk edildi.";
            }
        }

        return [
            'type'       => 'disiplin_kurul_sevk',
            'category'   => 'disiplin',
            'label'      => 'KURULA SEVK',
            'message'    => $mesaj,
            'url'        => $url,
            'action_url' => $url,
            'icon'       => 'folder-open',
            'color'      => 'indigo',
        ];
    }
}

