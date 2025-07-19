<?php

namespace App\Notifications;

use App\Models\StreamerProfile;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StreamerFollowNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $follower,
        public StreamerProfile $streamerProfile
    ) {}

    /**
     * Get the notification's delivery channels.
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
            ->subject("New follower for {$this->streamerProfile->channel_name}!")
            ->greeting("Hey {$notifiable->name}!")
            ->line("{$this->follower->name} just started following your channel {$this->streamerProfile->channel_name}!")
            ->line("Your community is growing!")
            ->action('View Profile', route('streamer-profiles.show', $this->streamerProfile))
            ->line('Keep up the great content!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_follower',
            'follower_id' => $this->follower->id,
            'follower_name' => $this->follower->name,
            'streamer_profile_id' => $this->streamerProfile->id,
            'streamer_name' => $this->streamerProfile->channel_name,
            'message' => "{$this->follower->name} started following {$this->streamerProfile->channel_name}",
        ];
    }
}