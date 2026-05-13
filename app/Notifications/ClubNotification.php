<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClubNotification extends Notification
{
    use Queueable;

    protected $club;
    protected $messageContent;

    /**
     * Create a new notification instance.
     */
    public function __construct($club, $messageContent)
    {
        $this->club = $club;
        $this->messageContent = $messageContent;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Update from ' . $this->club->name)
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('There is a new update in the ' . $this->club->name)
            ->line('Message: ' . $this->messageContent)
            ->action('Visit Club Page', url('/clubs/' . $this->club->id))
            ->line('Don\'t miss out on the latest activities!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'club_id' => $this->club->id,
            'club_name' => $this->club->name,
            'message' => $this->messageContent,
        ];
    }
}
