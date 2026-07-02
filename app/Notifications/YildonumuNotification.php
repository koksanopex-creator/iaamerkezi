<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class YildonumuNotification extends Notification
{
    use Queueable;

    public $anniversaryUser;
    public $type; // 'self', 'manager', 'colleague'
    public $years;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $anniversaryUser, string $type = 'self', int $years = 1)
    {
        $this->anniversaryUser = $anniversaryUser;
        $this->type = $type;
        $this->years = $years;
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
            $subjectSetting = Setting::where('key', 'anniversary_email_subject')->first()?->value ?? 'Şirketimizdeki {yil}. Yılınız Kutlu Olsun! 🎊';
            $bodySetting = Setting::where('key', 'anniversary_email_body')->first()?->value ?? 'Sayın {personel_adi}, şirketimizdeki {yil}. yılınızı kutlar, başarılarınızın devamını dileriz.';
            
            $subject = str_replace(['{personel_adi}', '{yil}'], [$notifiable->name, $this->years], $subjectSetting);
            $body = str_replace(['{personel_adi}', '{yil}'], [$notifiable->name, $this->years], $bodySetting);

            return (new MailMessage)
                ->subject($subject)
                ->greeting('Tebrikler ' . $notifiable->name . '!')
                ->line($body)
                ->line('Şirketimizdeki ' . $this->years . '. yılınızı geride bırakırken, kattığınız tüm değerler için teşekkür ederiz.')
                ->action('Paneli Görüntüle', url('/dashboard'))
                ->line('Birlikte daha nice başarılı yıllara!');
        } elseif ($this->type === 'manager') {
            $subjectSetting = Setting::where('key', 'anniversary_leader_email_subject')->first()?->value ?? 'Ekibinizde Bir İş Yıldönümü! 🎊';
            $bodySetting = Setting::where('key', 'anniversary_leader_email_body')->first()?->value ?? 'Merhaba {yonetici_adi}, bugün ekibinizden {personel_adi} personelin şirketimizdeki {yil}. yılı.';
            
            $subject = str_replace(['{yonetici_adi}', '{personel_adi}', '{yil}'], [$notifiable->name, $this->anniversaryUser->name, $this->years], $subjectSetting);
            $body = str_replace(['{yonetici_adi}', '{personel_adi}', '{yil}'], [$notifiable->name, $this->anniversaryUser->name, $this->years], $bodySetting);

            return (new MailMessage)
                ->subject($subject)
                ->greeting('Merhaba ' . $notifiable->name . ',')
                ->line($body)
                ->line('Ekip arkadaşımızı bu anlamlı gününde tebrik etmek isterseniz profiline gidip bir mesaj bırakabilirsiniz.')
                ->action('Tebrik Et', route('profile.show', $this->anniversaryUser->id) . '?tab=yorumlar&anniv_msg=1')
                ->line('Birlikte daha nice başarılı yıllara!');
        } else {
            // Colleague
            $subjectSetting = Setting::where('key', 'anniversary_colleague_email_subject')->first()?->value ?? 'Bir Ekip Arkadaşınızın İş Yıldönümü! 🎊';
            $bodySetting = Setting::where('key', 'anniversary_colleague_email_body')->first()?->value ?? 'Merhaba, bugün bölüm arkadaşınız {personel_adi}\'nın şirketimizdeki {yil}. yılı.';
            
            $subject = str_replace(['{personel_adi}', '{yil}'], [$this->anniversaryUser->name, $this->years], $subjectSetting);
            $body = str_replace(['{personel_adi}', '{yil}'], [$this->anniversaryUser->name, $this->years], $bodySetting);

            return (new MailMessage)
                ->subject($subject)
                ->greeting('Merhaba ' . $notifiable->name . ',')
                ->line($body)
                ->line('Arkadaşımızı bu özel gününde tebrik etmek ister misiniz?')
                ->action('Tebrik Et', route('profile.show', $this->anniversaryUser->id) . '?tab=yorumlar&anniv_msg=1')
                ->line('Birlikte nice yıllara!');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'anniversary',
            'user_id' => $this->anniversaryUser->id,
            'user_name' => $this->anniversaryUser->name,
            'years' => $this->years,
            'message_type' => $this->type
        ];
    }
}
