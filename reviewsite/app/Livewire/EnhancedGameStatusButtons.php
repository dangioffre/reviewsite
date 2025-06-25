<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GameUserStatus;
use App\Models\Platform;
use Illuminate\Support\Facades\Auth;

class EnhancedGameStatusButtons extends Component
{
    public $product;
    public $userStatus;
    public $stats = [];
    
    // Modal states
    public $showDetailModal = false;
    public $showPlayedModal = false;
    public $showDeleteModal = false;
    
    // Form properties
    public $completion_status;
    public $hours_played;
    public $completion_percentage;
    public $started_date;
    public $completed_date;
    public $notes;
    public $rating;
    public $is_favorite = false;
    public $platform_played;
    public $times_replayed = 0;
    public $difficulty_played;
    public $dropped = false;
    public $dropped_date;
    public $drop_reason;
    
    // Quick status toggles
    public $have = false;
    public $want = false;
    public $played = false;

    public function mount($product)
    {
        $this->product = $product;
        $this->loadUserStatus();
        $this->loadStats();
    }

    public function loadUserStatus()
    {
        if (!Auth::check()) return;
        
        $this->userStatus = GameUserStatus::where('user_id', Auth::id())
            ->where('product_id', $this->product->id)
            ->first();
            
        if ($this->userStatus) {
            $this->have = $this->userStatus->have;
            $this->want = $this->userStatus->want;
            $this->played = $this->userStatus->played;
            $this->completion_status = $this->userStatus->completion_status;
            $this->hours_played = $this->userStatus->hours_played;
            $this->completion_percentage = $this->userStatus->completion_percentage;
            $this->started_date = $this->userStatus->started_date?->format('Y-m-d');
            $this->completed_date = $this->userStatus->completed_date?->format('Y-m-d');
            $this->notes = $this->userStatus->notes;
            $this->rating = $this->userStatus->rating;
            $this->is_favorite = $this->userStatus->is_favorite;
            $this->platform_played = $this->userStatus->platform_played;
            $this->times_replayed = $this->userStatus->times_replayed;
            $this->difficulty_played = $this->userStatus->difficulty_played;
            $this->dropped = $this->userStatus->dropped;
            $this->dropped_date = $this->userStatus->dropped_date?->format('Y-m-d');
            $this->drop_reason = $this->userStatus->drop_reason;
        }
    }

    public function loadStats()
    {
        $this->stats = [
            'have' => GameUserStatus::where('product_id', $this->product->id)->where('have', true)->count(),
            'want' => GameUserStatus::where('product_id', $this->product->id)->where('want', true)->count(),
            'played' => GameUserStatus::where('product_id', $this->product->id)->where('played', true)->count(),
            'completed' => GameUserStatus::where('product_id', $this->product->id)->completed()->count(),
            'currently_playing' => GameUserStatus::where('product_id', $this->product->id)->currentlyPlaying()->count(),
            'abandoned' => GameUserStatus::where('product_id', $this->product->id)->abandoned()->count(),
        ];
    }

    public function toggle($type)
    {
        if (!Auth::check()) {
            $this->dispatch('show-login');
            return;
        }

        $status = GameUserStatus::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
        ]);

        $status->$type = !$status->$type;
        
        // Auto-set played status if setting detailed completion
        if ($type === 'played' && $status->played && !$status->completion_status) {
            $status->completion_status = GameUserStatus::STATUS_STARTED;
            $status->started_date = now();
        }
        
        $status->save();
        $this->loadUserStatus();
        $this->loadStats();
        
        session()->flash('message', ucfirst($type) . ' status updated!');
    }

    public function openDetailModal()
    {
        if (!Auth::check()) {
            $this->dispatch('show-login');
            return;
        }
        
        // Close the played modal when opening detailed modal
        $this->showPlayedModal = false;
        $this->showDetailModal = true;
    }

    public function openPlayedModal()
    {
        if (!Auth::check()) {
            $this->dispatch('show-login');
            return;
        }
        
        $this->showPlayedModal = true;
    }

    public function saveDetailedStatus()
    {
        $this->validate([
            'completion_status' => 'nullable|string',
            'hours_played' => 'nullable|integer|min:0|max:99999',
            'completion_percentage' => 'nullable|integer|min:0|max:100',
            'started_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:10',
            'platform_played' => 'nullable|string',
            'times_replayed' => 'nullable|integer|min:0|max:99',
            'difficulty_played' => 'nullable|string',
            'dropped_date' => 'nullable|date',
            'drop_reason' => 'nullable|string|max:500',
        ]);

        $status = GameUserStatus::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
        ]);

        // Update all fields
        $status->fill([
            'completion_status' => $this->completion_status,
            'hours_played' => $this->hours_played,
            'completion_percentage' => $this->completion_percentage,
            'started_date' => $this->started_date,
            'completed_date' => $this->completed_date,
            'notes' => $this->notes,
            'rating' => $this->rating,
            'is_favorite' => $this->is_favorite,
            'platform_played' => $this->platform_played,
            'times_replayed' => $this->times_replayed,
            'difficulty_played' => $this->difficulty_played,
            'dropped' => $this->dropped,
            'dropped_date' => $this->dropped_date,
            'drop_reason' => $this->drop_reason,
        ]);

        // Auto-set played status if has completion status
        if ($this->completion_status && $this->completion_status !== GameUserStatus::STATUS_NOT_STARTED) {
            $status->played = true;
            $this->played = true;
        }

        $status->save();
        $this->loadUserStatus();
        $this->loadStats();
        
        $this->showDetailModal = false;
        $this->showPlayedModal = false;
        
        session()->flash('message', 'Game status updated with detailed information!');
    }

    public function quickSetStatus($statusKey)
    {
        if (!Auth::check()) {
            $this->dispatch('show-login');
            return;
        }

        $status = GameUserStatus::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
        ]);

        $status->completion_status = $statusKey;
        $status->played = true;
        
        // Auto-set dates based on status
        if ($statusKey === GameUserStatus::STATUS_STARTED && !$status->started_date) {
            $status->started_date = now();
        } elseif (in_array($statusKey, [GameUserStatus::STATUS_COMPLETED, GameUserStatus::STATUS_FULLY_COMPLETED]) && !$status->completed_date) {
            $status->completed_date = now();
        } elseif ($statusKey === GameUserStatus::STATUS_ABANDONED && !$status->dropped_date) {
            $status->dropped_date = now();
            $status->dropped = true;
        }

        $status->save();
        $this->loadUserStatus();
        $this->loadStats();
        
        $statusDetails = GameUserStatus::getCompletionStatuses()[$statusKey];
        session()->flash('message', 'Status set to: ' . $statusDetails['label']);
    }

    public function toggleFavorite()
    {
        if (!Auth::check()) {
            $this->dispatch('show-login');
            return;
        }

        $status = GameUserStatus::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
        ]);

        $status->is_favorite = !$status->is_favorite;
        $status->save();
        
        $this->loadUserStatus();
        
        session()->flash('message', $status->is_favorite ? 'Added to favorites!' : 'Removed from favorites!');
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->showPlayedModal = false;
    }
    
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
    }
    
    public function closePlayedModal()
    {
        $this->showPlayedModal = false;
    }

    public function getCompletionStatuses()
    {
        return GameUserStatus::getCompletionStatuses();
    }

    public function getDifficultyLevels()
    {
        return GameUserStatus::getDifficultyLevels();
    }

    public function getPlatforms()
    {
        return Platform::orderBy('name')->get();
    }

    public function unselectPlay()
    {
        if (!Auth::check() || !$this->userStatus) {
            return;
        }

        // Reset played status and all related completion data
        $this->userStatus->update([
            'played' => false,
            'completion_status' => null,
            'hours_played' => null,
            'completion_percentage' => null,
            'started_date' => null,
            'completed_date' => null,
            'dropped' => false,
            'dropped_date' => null,
            'drop_reason' => null,
        ]);

        $this->loadUserStatus();
        $this->loadStats();
        
        session()->flash('message', 'Play status removed and progress data cleared!');
    }

    public function confirmDelete()
    {
        if (!Auth::check()) {
            $this->dispatch('show-login');
            return;
        }
        
        $this->showDeleteModal = true;
    }

    public function deleteFromCollection()
    {
        if (!Auth::check() || !$this->userStatus) {
            return;
        }

        $gameName = $this->product->name;
        $this->userStatus->delete();
        
        // Reset all local properties
        $this->userStatus = null;
        $this->have = false;
        $this->want = false;
        $this->played = false;
        $this->completion_status = null;
        $this->hours_played = null;
        $this->completion_percentage = null;
        $this->started_date = null;
        $this->completed_date = null;
        $this->notes = null;
        $this->rating = null;
        $this->is_favorite = false;
        $this->platform_played = null;
        $this->times_replayed = 0;
        $this->difficulty_played = null;
        $this->dropped = false;
        $this->dropped_date = null;
        $this->drop_reason = null;
        
        $this->showDeleteModal = false;
        $this->loadStats();
        
        session()->flash('message', "'{$gameName}' has been removed from your collection!");
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
    }

    public function render()
    {
        return view('livewire.enhanced-game-status-buttons');
    }
} 