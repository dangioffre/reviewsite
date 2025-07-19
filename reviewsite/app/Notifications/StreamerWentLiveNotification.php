<?php

namespace App\Notifications;

use App\Models\StreamerProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class StreamerWentLiveNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
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
            ->subject("{$this->streamerProfile->getDisplayName()} is now live!")
            ->greeting("Hey {$notifiable->name}!")
            ->line("{$this->streamerProfile->getDisplayName()} just went live on {$this->streamerProfile->platform}!")
            ->line("Don't miss out on the stream!")
            ->action('Watch Now', $this->streamerProfile->channel_url)
            ->line('You can manage your notification preferences in your followed streamers settings.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'streamer_live',
            'streamer_profile_id' => $this->streamerProfile->id,
            'streamer_name' => $this->streamerProfile->getDisplayName(),
            'platform' => $this->streamerProfile->platform,
            'channel_url' => $this->streamerProfile->channel_url,
            'message' => "{$this->streamerProfile->getDisplayName()} is now live!",
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