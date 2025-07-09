<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Review;
use App\Models\User;

class NewPodcastComment extends Notification implements ShouldQueue
{
    use Queueable;

    public $commentId;
    public $episodeTitle;
    public $episodeSlug;
    public $podcastSlug;
    public $commenterName;
    public $commenterId;

    /**
     * Create a new notification instance.
     */
    public function __construct($commentId, $episodeTitle, $episodeSlug, $podcastSlug, $commenterName, $commenterId)
    {
        $this->commentId = $commentId;
        $this->episodeTitle = $episodeTitle;
        $this->episodeSlug = $episodeSlug;
        $this->podcastSlug = $podcastSlug;
        $this->commenterName = $commenterName;
        $this->commenterId = $commenterId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->commentId,
            'episode_title' => $this->episodeTitle,
            'episode_slug' => $this->episodeSlug,
            'podcast_slug' => $this->podcastSlug,
            'commenter_name' => $this->commenterName,
            'commenter_id' => $this->commenterId,
        ];
    }
}
