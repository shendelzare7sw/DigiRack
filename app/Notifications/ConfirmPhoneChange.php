<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class ConfirmPhoneChange extends Notification
{
    use Queueable;

    public function __construct(
        protected User $user,
        protected string $phone,
        protected string $token,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expire = Config::get('auth.verification.expire', 60);
        $url = URL::temporarySignedRoute(
            'profile.phone.confirm',
            now()->addMinutes($expire),
            [
                'user' => $this->user->getKey(),
                'token' => $this->token,
            ],
        );

        return (new MailMessage)
            ->subject('Konfirmasi Perubahan Nomor Telepon DigiRack')
            ->greeting('Halo ' . $this->user->name . ',')
            ->line('Kami menerima permintaan untuk mengganti nomor telepon akun DigiRack Anda menjadi ' . $this->phone . '.')
            ->line('Untuk keamanan akun, perubahan ini perlu dikonfirmasi melalui email yang sudah terverifikasi.')
            ->action('Konfirmasi Nomor Telepon', $url)
            ->line('Link konfirmasi ini berlaku selama ' . $expire . ' menit. Jika Anda tidak meminta perubahan ini, abaikan email ini dan nomor lama Anda tetap digunakan.')
            ->salutation('Salam aman, Tim DigiRack');
    }
}
