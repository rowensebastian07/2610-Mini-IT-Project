<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $id;
    protected $messageContent;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($id, $messageContent, $type = 'general')
    {
        $this->id = $id;
        $this->messageContent = $messageContent;
        $this->type = $type; // ✅ store type (post/event/club/general)
    }

    /**
     * Get the notification's delivery channels.
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // ✅ Only database for now (skip mail to avoid errors)
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id'   => $this->user->id,
            'is_admin' => $this->user->is_admin,
            'message'   => $this->messageContent,
            'type'      => $this->type, // ✅ post / event / club / general
        ];
    }
}
