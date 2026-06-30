<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCustomerUserCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plainPassword;
    public $companyName;
    public $customSubject;
    public $customBody;

    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user->load('customer');
        $this->plainPassword = $plainPassword;
        $this->companyName = $this->user->customer->name ?? 'Firma Belirtilmedi';

        // Fetch settings or use defaults
        $settings = \App\Models\Setting::whereIn('key', ['new_customer_email_subject', 'new_customer_email_body'])->get()->keyBy('key');

        $this->customSubject = $settings->get('new_customer_email_subject')?->value ?? 'Hoşgeldiniz - Sisteme Giriş Bilgileriniz';

        $defaultBody = "Köksan Müşteri Portalı hesabınız {sirket_adi} firması için başarıyla oluşturulmuştur. Aşağıdaki bilgileri kullanarak sisteme giriş yapabilirsiniz.";
        $rawBody = $settings->get('new_customer_email_body')?->value ?? $defaultBody;

        // Replace placeholders
        $ssoUrl = config('services.central_sso.url') ?? env('CENTRAL_SSO_URL', 'http://localhost:8001');
        $this->customBody = str_replace(
            ['{musteri_adi}', '{sirket_adi}', '{email}', '{sifre}', '{giris_linki}'],
            [$user->name, $this->companyName, $user->email, $plainPassword, $ssoUrl],
            $rawBody
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->customSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-customer-user',
            with: [
                'user' => $this->user,
                'customBody' => $this->customBody,
            ],
        );
    }
}