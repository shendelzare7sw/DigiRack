<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StoreStatusNotification extends Notification
{
    use Queueable;

    protected string $type;
    protected string $title;
    protected string $message;
    protected string $actionUrl;
    protected string $icon;

    /**
     * @param string $type     e.g. 'store_verified', 'store_banned', 'store_restored'
     * @param string $title
     * @param string $message
     * @param string $actionUrl
     * @param string $icon
     */
    public function __construct(string $type, string $title, string $message, string $actionUrl = '', string $icon = '🏪')
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
