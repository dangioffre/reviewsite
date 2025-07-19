<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StreamerReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Review $review
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
        $streamerName = $this->review->streamerProfile->channel_name;
        $gameName = $this->review->game->name;

        return (new MailMessage)
            ->subject("{$streamerName} posted a new review!")
            ->greeting("Hey {$notifiable->name}!")
            ->line("{$streamerName} just posted a review for {$gameName}!")
            ->line("Rating: {$this->review->rating}/5 stars")
            ->action('Read Review', route('games.show', $this->review->game))
            ->line('You can manage your notification preferences in your account settings.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'streamer_review',
            'review_id' => $this->review->id,
            'streamer_profile_id' => $this->review->streamerProfile->id,
            'streamer_name' => $this->review->streamerProfile->channel_name,
            'game_id' => $this->review->game->id,
            'game_name' => $this->review->game->name,
            'rating' => $this->review->rating,
            'message' => "{$this->review->streamerProfile->channel_name} posted a review for {$this->review->game->name}",
        ];
    }
}