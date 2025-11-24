<?php

namespace App\Notifications;

use App\Models\Iaa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProjeDurumuDegisti extends Notification
{
    use Queueable;

    protected $iaa;
    protected $durumText; // Örn: "Erhan Cesur tarafından onayınıza sunuldu" veya "reddedildi"
    protected $neden; // Opsiyonel

    /**
     * Create a new notification instance.
     */
    public function __construct(Iaa $iaa, string $durumText, ?string $neden = null)
    {
        $this->iaa = $iaa;
        $this->durumText = $durumText;
        $this->neden = $neden;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        // Rota adını web.php dosyanızdan aldım
        $link = route('proje.workspace.show', $this->iaa->id);
        
        // === DÜZELTME BURADA ===
        // "yönetici tarafından" ibaresini sildik. 
        // Artık $this->durumText ne gelirse direkt onu yazacak.
        $message = "'{$this->iaa->baslik}' başlıklı projeniz {$this->durumText}.";
        
        // Eğer bir 'neden' (revizyon veya red notu) varsa, mesaja ekle
        if ($this->neden) {
            $message .= " (Neden: \"{$this->neden}\")";
        }

        return [
            'message' => $message,
            'link' => $link,
            'iaa_id' => $this->iaa->id
        ];
    }
}