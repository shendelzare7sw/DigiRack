<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmail
{
    protected function verificationUrl($notifiable): string
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toMail($notifiable): MailMessage
    {
        $appName = config('app.name', 'Digital Hook');

        return (new MailMessage)
            ->subject('Aktivasi Akun ' . $appName)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Terima kasih sudah bergabung dengan ' . $appName . '. Untuk menjaga akun dan transaksi tetap aman, kami perlu memastikan email ini benar milik Anda.')
            ->action('Verifikasi Email Saya', $this->verificationUrl($notifiable))
            ->line('Link verifikasi ini berlaku selama ' . Config::get('auth.verification.expire', 60) . ' menit. Setelah email aktif, Anda bisa menggunakan fitur belanja dan mengajukan pembukaan toko.')
            ->line('Jika Anda tidak merasa membuat akun di ' . $appName . ', abaikan email ini.')
            ->salutation('Salam hangat, Tim ' . $appName);
    }
}
