<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StreamerProfile;
use App\Models\StreamerShowcasedGame;
use App\Models\GameUserStatus;
use Illuminate\Support\Facades\Auth;

class ShowcaseGameManager extends Component
{
    use WithPagination;

    public StreamerProfile $streamerProfile;
    public $search = '';
    public $selectedGame = null;
    public $showcaseNote = '';
    public $showAddModal = false;
    public $maxShowcasedGames = 6; // Limit showcased games

    protected $rules = [
        'showcaseNote' => 'nullable|string|max:500',
    ];

    public function mount(StreamerProfile $streamerProfile)
    {
        // Ensure the authenticated user owns this streamer profile
        if (Auth::id() !== $streamerProfile->user_id) {
            abort(403, 'Unauthorized access to streamer profile.');
        }

        $this->streamerProfile = $streamerProfile;
    }

    public function openAddModal()
    {
        $this->showAddModal = true;
        $this->selectedGame = null;
        $this->showcaseNote = '';
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->selectedGame = null;
        $this->showcaseNote = '';
        $this->resetValidation();
    }

    public function selectGame($gameUserStatusId)
    {
        $this->selectedGame = GameUserStatus::with(['product.genre', 'product.platform'])
            ->where('user_id', $this->streamerProfile->user_id)
            ->whereHas('product') // Ensure the product exists
            ->findOrFail($gameUserStatusId);
    }

    public function addToShowcase()
    {
        $this->validate();

        if (!$this->selectedGame) {
            session()->flash('error', 'Please select a game to showcase.');
            return;
        }

        // Ensure product is loaded
        if (!$this->selectedGame->product) {
            session()->flash('error', 'Invalid game selected. Please try again.');
            return;
        }

        // Check if already showcased
        $existing = StreamerShowcasedGame::where('streamer_profile_id', $this->streamerProfile->id)
            ->where('game_user_status_id', $this->selectedGame->id)
            ->exists();

        if ($existing) {
            session()->flash('error', 'This game is already being showcased.');
            return;
        }

        // Check showcase limit
        $showcasedCount = $this->streamerProfile->showcasedGames()->count();
        if ($showcasedCount >= $this->maxShowcasedGames) {
            session()->flash('error', "You can only showcase up to {$this->maxShowcasedGames} games.");
            return;
        }

        StreamerShowcasedGame::create([
            'streamer_profile_id' => $this->streamerProfile->id,
            'game_user_status_id' => $this->selectedGame->id,
            'display_order' => StreamerShowcasedGame::getNextDisplayOrder($this->streamerProfile->id),
            'showcase_note' => $this->showcaseNote,
        ]);

        $gameName = $this->selectedGame->product->name ?? 'Unknown Game';
        $this->closeAddModal();
        session()->flash('success', "Added {$gameName} to your showcase!");
        
        // Refresh the component
        $this->dispatch('showcaseUpdated');
    }

    public function removeFromShowcase($showcasedGameId)
    {
        $showcasedGame = StreamerShowcasedGame::with(['gameUserStatus.product'])
            ->where('streamer_profile_id', $this->streamerProfile->id)
            ->findOrFail($showcasedGameId);

        $gameName = $showcasedGame->gameUserStatus->product->name ?? 'Unknown Game';
        $showcasedGame->delete();

        // Reorder remaining games
        StreamerShowcasedGame::reorderForStreamer($this->streamerProfile->id);

        session()->flash('success', "Removed {$gameName} from your showcase.");
        
        // Refresh the component
        $this->dispatch('showcaseUpdated');
    }

    public function moveUp($showcasedGameId)
    {
        $this->reorderGame($showcasedGameId, 'up');
    }

    public function moveDown($showcasedGameId)
    {
        $this->reorderGame($showcasedGameId, 'down');
    }

    private function reorderGame($showcasedGameId, $direction)
    {
        $showcasedGame = StreamerShowcasedGame::where('streamer_profile_id', $this->streamerProfile->id)
            ->findOrFail($showcasedGameId);

        $currentOrder = $showcasedGame->display_order;
        $newOrder = $direction === 'up' ? $currentOrder - 1 : $currentOrder + 1;

        // Check bounds
        $minOrder = 1;
        $maxOrder = $this->streamerProfile->showcasedGames()->count();
        
        if ($newOrder < $minOrder || $newOrder > $maxOrder) {
            return; // Can't move further
        }

        // Swap with the game at the target position
        $targetGame = StreamerShowcasedGame::where('streamer_profile_id', $this->streamerProfile->id)
            ->where('display_order', $newOrder)
            ->first();

        if ($targetGame) {
            $targetGame->update(['display_order' => $currentOrder]);
            $showcasedGame->update(['display_order' => $newOrder]);
        }

        $this->dispatch('showcaseUpdated');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Get currently showcased games
        $showcasedGames = $this->streamerProfile->showcasedGames()
            ->with(['gameUserStatus.product.genre', 'gameUserStatus.product.platform'])
            ->get();

        // Get available games from user's collection (excluding already showcased)
        $showcasedGameIds = $showcasedGames->pluck('game_user_status_id');
        
        $availableGamesQuery = GameUserStatus::with(['product.genre', 'product.platform'])
            ->where('user_id', $this->streamerProfile->user_id)
            ->whereNotIn('id', $showcasedGameIds)
            ->whereHas('product'); // Only include games that have products

        if ($this->search) {
            $availableGamesQuery->whereHas('product', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }

        $availableGames = $availableGamesQuery->paginate(10);

        return view('livewire.showcase-game-manager', [
            'showcasedGames' => $showcasedGames,
            'availableGames' => $availableGames,
        ]);
    }
}
