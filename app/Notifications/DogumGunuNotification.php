<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Setting;

class DogumGunuNotification extends Notification
{
    use Queueable;

    public $birthdayUser;
    public $type; // 'self', 'manager', 'colleague'

    /**
     * Create a new notification instance.
     */
    public function __construct(User $birthdayUser, string $type = 'self')
    {
        $this->birthdayUser = $birthdayUser;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->type === 'self') {
            $subjectSetting = Setting::where('key', 'birthday_email_subject')->first()?->value ?? 'İyi Ki Doğdun! 🎂';
            $bodySetting = Setting::where('key', 'birthday_email_body')->first()?->value ?? 'Sayın {personel_adi}, Doğum gününüzü kutlar, sağlıklı ve mutlu bir yıl dileriz.';
            
            $subject = str_replace('{personel_adi}', $notifiable->name, $subjectSetting);
            $body = str_replace('{personel_adi}', $notifiable->name, $bodySetting);

            return (new MailMessage)
                ->subject($subject)
                ->greeting('Mutlu Yıllar ' . $notifiable->name . '!')
                ->line($body)
                ->line('Şirketimizdeki başarılarınızın devamını diler, sizinle çalışmaktan mutluluk duyarız.')
                ->action('Paneli Görüntüle', url('/dashboard'))
                ->line('Yeni yaşınızın size huzur ve başarı getirmesini dileriz.');
        } elseif ($this->type === 'manager') {
            $subjectSetting = Setting::where('key', 'birthday_leader_email_subject')->first()?->value ?? 'Ekibinizde Bir Doğum Günü! 🎂';
            $bodySetting = Setting::where('key', 'birthday_leader_email_body')->first()?->value ?? 'Merhaba {yonetici_adi}, bugün ekibinizden {personel_adi} personelin doğum günü.';
            
            $subject = str_replace(['{yonetici_adi}', '{personel_adi}'], [$notifiable->name, $this->birthdayUser->name], $subjectSetting);
            $body = str_replace(['{yonetici_adi}', '{personel_adi}'], [$notifiable->name, $this->birthdayUser->name], $bodySetting);

            return (new MailMessage)
                ->subject($subject)
                ->greeting('Merhaba ' . $notifiable->name . ',')
                ->line($body)
                ->line('Kendisini tebrik etmek isterseniz profiline gidip bir mesaj bırakabilirsiniz.')
                ->action('Tebrik Et', route('profile.show', $this->birthdayUser->id) . '?tab=yorumlar&bday_msg=1')
                ->line('Birlikte daha nice başarılı yıllara!');
        } else {
            // Colleague
            $subjectSetting = Setting::where('key', 'birthday_colleague_email_subject')->first()?->value ?? 'Bir Ekip Arkadaşınızın Doğum Günü! 🎂';
            $bodySetting = Setting::where('key', 'birthday_colleague_email_body')->first()?->value ?? 'Merhaba, bugün bölüm arkadaşınız {personel_adi}\'nın doğum günü.';
            
            $subject = str_replace('{personel_adi}', $this->birthdayUser->name, $subjectSetting);
            $body = str_replace('{personel_adi}', $this->birthdayUser->name, $bodySetting);

            return (new MailMessage)
                ->subject($subject)
                ->greeting('Merhaba ' . $notifiable->name . ',')
                ->line($body)
                ->line('Arkadaşımızı tebrik etmek ve ona güzel bir sürpriz yapmak ister misiniz?')
                ->action('Tebrik Et', route('profile.show', $this->birthdayUser->id) . '?tab=yorumlar&bday_msg=1')
                ->line('Birlikte nice yaşlara!');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->type === 'self') {
            return [
                'type' => 'birthday_self',
                'title' => 'Mutlu Yıllar! 🎂',
                'message' => 'Doğum gününüz kutlu olsun! Nice mutlu senelere.',
                'user_name' => $this->birthdayUser->name,
                'user_id' => $this->birthdayUser->id,
                'link' => url('/dashboard'),
            ];
        } else {
            return [
                'type' => 'birthday_other',
                'title' => 'Bir Doğum Günü Var! 🎂',
                'message' => $this->birthdayUser->name . ' bugün bir yaş daha gençleşti! Tebrik etmeye ne dersin?',
                'user_name' => $this->birthdayUser->name,
                'user_id' => $this->birthdayUser->id,
                'link' => route('profile.show', $this->birthdayUser->id) . '?tab=yorumlar&bday_msg=1',
            ];
        }
    }
}
