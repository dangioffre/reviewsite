<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\StreamerProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class StreamerNewFollowerNotification extends Notification implements ShouldQueue
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
            ->subject('You have a new follower!')
            ->greeting("Hey {$notifiable->name}!")
            ->line("{$this->follower->name} is now following your channel {$this->streamerProfile->getDisplayName()}.")
            ->action('View Your Profile', route('streamer.profile.show', $this->streamerProfile))
            ->line('Keep up the great content!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'new_follower',
            'follower_id' => $this->follower->id,
            'follower_name' => $this->follower->name,
            'streamer_profile_id' => $this->streamerProfile->id,
            'streamer_name' => $this->streamerProfile->getDisplayName(),
            'message' => "{$this->follower->name} is now following your channel {$this->streamerProfile->getDisplayName()}",
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}