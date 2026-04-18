<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderNotification extends Notification
{
    use Queueable;

    protected string $type;
    protected string $title;
    protected string $message;
    protected string $actionUrl;
    protected string $icon;

    /**
     * @param string $type     e.g. 'new_order', 'order_shipped', 'order_completed'
     * @param string $title    Judul singkat notifikasi
     * @param string $message  Pesan detail
     * @param string $actionUrl URL tujuan saat notifikasi diklik
     * @param string $icon     Emoji/icon identifier
     */
    public function __construct(string $type, string $title, string $message, string $actionUrl, string $icon = '📦')
    {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
        $this->icon = $icon;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'icon' => $this->icon,
        ];
    }
}
