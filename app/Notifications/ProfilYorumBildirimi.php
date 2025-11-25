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
        return ['database'];
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

            // PROFİL SAYFASINA TAM LİNK
            'action_url'  => route('profile.show', ['user' => $this->profileId]) . '?tab=yorumlar',

            // ID'yi hem snake_case hem camelCase olarak verelim (JS hangisini isterse bulsun)
            'profile_id'  => $this->profileId,
            'profileId'   => $this->profileId,

            'icon'        => 'chat-bubble-left-right',
            'color'       => 'indigo',
        ];
    }

}