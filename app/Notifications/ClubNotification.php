<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClubNotification extends Notification
{
    use Queueable;

    protected $club;
    protected $messageContent;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($club, $messageContent, $type = 'general')
    {
        $this->club = $club;
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
            'club_id'   => $this->club->id,
            'club_name' => $this->club->name,
            'message'   => $this->messageContent,
            'type'      => $this->type, // ✅ post / event / club / general
        ];
    }
}