<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomResetPassword extends ResetPassword
{
    protected function resetUrl($notifiable): string
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        return URL::route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }

    public function toMail($notifiable): MailMessage
    {
        $appName = config('app.name', 'DigiRack');
        $expire = Config::get('auth.passwords.' . Config::get('auth.defaults.passwords') . '.expire', 60);

        return (new MailMessage)
            ->subject('Reset Password ' . $appName)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Kami menerima permintaan reset password untuk akun Anda. Tombol di bawah membawa Anda ke halaman reset dengan token sekali pakai.')
            ->action('Reset Password Sekarang', $this->resetUrl($notifiable))
            ->line('Token ini berlaku selama ' . $expire . ' menit dan hanya bisa digunakan untuk satu proses reset.')
            ->line('Jika Anda tidak meminta reset password, abaikan email ini. Password lama Anda tetap aman selama link ini tidak digunakan.')
            ->salutation('Salam aman, Tim ' . $appName);
    }
}
