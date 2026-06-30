<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\ProfileComment;

class ProfilYorumBildirimi extends Notification
{
    use Queueable;

    public $comment;
    public $type;
    public $profileId; // Profil sahibinin ID'si

    public function __construct(ProfileComment $comment, string $type, int $profileId)
    {
        $this->comment = $comment;
        $this->type = $type;
        $this->profileId = $profileId;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $yazanIsim = $this->comment->yazan ? $this->comment->yazan->name : 'Bir kullanıcı';
        
        $subject = $this->type === 'reply' ? 'Bir yorumunuza cevap geldi' : 'Profilinize yeni bir yorum yapıldı';
        $greeting = "Sayın {$notifiable->name},";
        
        $line = $this->type === 'reply' 
            ? "{$yazanIsim} bir yorumunuza cevap verdi." 
            : "{$yazanIsim} profilinize yeni bir yorum yaptı.";

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line)
            ->line('"' . \Illuminate\Support\Str::limit($this->comment->yorum, 100) . '"')
            ->action('Yorumu Görüntüle', route('profile.show', ['user' => $this->profileId]) . '?tab=yorumlar&focused_comment=' . $this->comment->id)
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray(object $notifiable): array
    {
        $yazanIsim = $this->comment->yazan ? $this->comment->yazan->name : 'Bir kullanıcı';

        if ($this->type === 'reply') {
            $mesaj = "{$yazanIsim} bir yorumunuza cevap verdi.";
        } else {
            $mesaj = "{$yazanIsim} profilinize yorum yaptı.";
        }

        return [
            'message'     => $mesaj,

            // PROFİL SAYFASINA TAM LİNK (Yorum ID ekledik)
            'action_url'  => route('profile.show', ['user' => $this->profileId]) . '?tab=yorumlar&focused_comment=' . $this->comment->id,

            // ID'yi hem snake_case hem camelCase olarak verelim (JS hangisini isterse bulsun)
            'profile_id'  => $this->profileId,
            'profileId'   => $this->profileId,

            'icon'        => 'chat-bubble-left-right',
            'color'       => 'indigo',
        ];
    }

}