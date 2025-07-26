<?php

namespace App\Livewire;

use App\Models\GameTip;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GameTipLike extends Component
{
    public GameTip $tip;
    public bool $liked = false;
    public int $likesCount = 0;
    public bool $canLike = false;

    public function mount(GameTip $tip)
    {
        $this->tip = $tip;
        $this->likesCount = $tip->likes_count;
        $this->canLike = Auth::check();
        
        if ($this->canLike) {
            $this->liked = $tip->isLikedBy(Auth::user());
        }
    }

    public function toggleLike()
    {
        if (!$this->canLike) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if ($this->liked) {
            // Unlike
            $this->tip->likes()->where('user_id', $user->id)->delete();
            $this->tip->decrement('likes_count');
            $this->liked = false;
        } else {
            // Like
            $this->tip->likes()->create(['user_id' => $user->id]);
            $this->tip->increment('likes_count');
            $this->liked = true;
        }

        $this->tip->refresh();
        $this->likesCount = $this->tip->likes_count;
    }

    public function render()
    {
        return view('livewire.game-tip-like');
    }
}
