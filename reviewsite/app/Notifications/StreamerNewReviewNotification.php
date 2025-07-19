<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class StreamerNewReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Review $review
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
        $streamerName = $this->review->streamerProfile->getDisplayName();
        $gameName = $this->review->game->name;

        return (new MailMessage)
            ->subject("{$streamerName} posted a new review!")
            ->greeting("Hey {$notifiable->name}!")
            ->line("{$streamerName} just posted a review for {$gameName}.")
            ->line("Rating: {$this->review->rating}/5 stars")
            ->action('Read Review', route('games.show', $this->review->game))
            ->line('Check out what they thought about the game!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'streamer_review',
            'review_id' => $this->review->id,
            'streamer_profile_id' => $this->review->streamer_profile_id,
            'streamer_name' => $this->review->streamerProfile->getDisplayName(),
            'game_id' => $this->review->game_id,
            'game_name' => $this->review->game->name,
            'rating' => $this->review->rating,
            'message' => "{$this->review->streamerProfile->getDisplayName()} posted a new review for {$this->review->game->name}",
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