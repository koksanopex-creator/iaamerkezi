<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ReportRoleAuthorization;

class AnalizRaporuYetkiVerildiNotification extends Notification
{
    use Queueable;

    public string $dataScope;
    public string $atayanAdi;

    public function __construct(string $dataScope, string $atayanAdi)
    {
        $this->dataScope = $dataScope;
        $this->atayanAdi = $atayanAdi;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $scopeLabels = ReportRoleAuthorization::DATA_SCOPE_OPTIONS;
        $kapsam = $scopeLabels[$this->dataScope] ?? $this->dataScope;

        return (new MailMessage)
            ->subject('Müşteri Şikayet Analiz Raporu Yetkisi Verildi')
            ->greeting("Merhaba {$notifiable->name},")
            ->line("Sistem yöneticisi **{$this->atayanAdi}** tarafından Müşteri Şikayet Analiz Raporu'na erişim yetkiniz tanımlanmış veya güncellenmiştir.")
            ->line("Veri Kapsamınız: **{$kapsam}**")
            ->action('Raporu Görüntüle', url('/admin/musteri-sikayet-analiz-raporu'))
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray(object $notifiable): array
    {
        $scopeLabels = ReportRoleAuthorization::DATA_SCOPE_OPTIONS;
        $kapsam = $scopeLabels[$this->dataScope] ?? $this->dataScope;

        return [
            'message' => "Şikayet Analiz Raporu erişim yetkiniz güncellendi ({$kapsam}).",
            'url' => url('/admin/musteri-sikayet-analiz-raporu'),
            'icon' => 'chart-pie',
            'color' => 'blue',
        ];
    }
}
