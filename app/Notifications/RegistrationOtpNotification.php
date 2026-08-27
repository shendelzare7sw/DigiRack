<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationOtpNotification extends Notification
{
    public function __construct(
        protected string $code,
        protected string $name,
        protected int $expiresMinutes = 10,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'Digital Hook');

        return (new MailMessage)
            ->subject('Kode OTP Pendaftaran ' . $appName . ': ' . $this->code)
            ->greeting('Halo ' . $this->name . ',')
            ->line('Gunakan kode berikut untuk menyelesaikan pendaftaran akun ' . $appName . ' Anda:')
            ->line('**' . $this->code . '**')
            ->line('Kode ini berlaku selama ' . $this->expiresMinutes . ' menit dan hanya untuk satu kali pendaftaran.')
            ->line('Jika Anda tidak meminta pendaftaran ini, abaikan email ini — tidak ada akun yang akan dibuat.')
            ->salutation('Salam, Tim ' . $appName);
    }
}
