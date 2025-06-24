<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ListModel;
use App\Models\ListItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AddToListModal extends Component
{
    public $productId;
    public $buttonText;
    public $buttonClass;
    public $iconSize;
    
    public $showModal = false;
    public $lists = [];
    public $showCreate = false;
    public $newListName = '';
    public $newListDescription = '';
    public $successMessage = '';

    public function mount($productId, $buttonText = 'Add to lists', $buttonClass = '', $iconSize = 'w-4 h-4')
    {
        $this->productId = $productId;
        $this->buttonText = $buttonText;
        $this->buttonClass = $buttonClass;
        $this->iconSize = $iconSize;
    }

    public function openModal()
    {
        Log::info('Opening modal for product: ' . $this->productId);
        $this->showModal = true;
        $this->refreshLists();
    }

    public function closeModal()
    {
        Log::info('Closing modal');
        $this->showModal = false;
        $this->showCreate = false;
        $this->successMessage = '';
    }

    public function refreshLists()
    {
        Log::info('Refreshing lists for user: ' . auth()->id());
        
        // Get user's own lists
        $ownLists = auth()->user()->lists()
            ->with(['items' => function($query) {
                $query->where('product_id', $this->productId);
            }])
            ->get();

        // Get collaborative lists where user can add games
        $collaborativeLists = \App\Models\ListModel::whereHas('collaborators', function ($query) {
                $query->where('user_id', auth()->id())
                      ->whereNotNull('accepted_at')
                      ->where('can_add_games', true);
            })
            ->with(['items' => function($query) {
                $query->where('product_id', $this->productId);
            }, 'user'])
            ->get();

        // Combine both types of lists
        $this->lists = $ownLists->concat($collaborativeLists);
        
        Log::info('Found ' . $this->lists->count() . ' lists (own + collaborative)');
    }

    public function createList()
    {
        Log::info('Creating new list: ' . $this->newListName);
        
        $this->validate([
            'newListName' => 'required|string|max:255',
        ]);

        $list = auth()->user()->lists()->create([
            'name' => $this->newListName,
            'description' => $this->newListDescription,
            'slug' => Str::slug($this->newListName),
            'is_public' => false,
        ]);

        // Add the current game to the new list
        $list->items()->create([
            'product_id' => $this->productId,
        ]);

        Log::info('List created successfully: ' . $list->name);

        $this->newListName = '';
        $this->newListDescription = '';
        $this->showCreate = false;
        $this->successMessage = 'List created and game added!';
        $this->refreshLists();
    }

    public function addToList($listId)
    {
        Log::info('Adding product ' . $this->productId . ' to list ' . $listId);
        
        // Try to find in user's own lists first
        $list = auth()->user()->lists()->find($listId);
        
        // If not found, try collaborative lists where user can add games
        if (!$list) {
            $list = \App\Models\ListModel::whereHas('collaborators', function ($query) use ($listId) {
                $query->where('user_id', auth()->id())
                      ->whereNotNull('accepted_at')
                      ->where('can_add_games', true);
            })->find($listId);
        }
        
        if (!$list) {
            Log::error('List not found or user does not have permission to add games');
            $this->successMessage = 'List not found or you do not have permission to add games to this list.';
            return;
        }
        
        // Check if already in list
        if (!$list->items()->where('product_id', $this->productId)->exists()) {
            $list->items()->create([
                'product_id' => $this->productId,
            ]);
            
            Log::info('Product added to list successfully');
            $this->successMessage = 'Added to ' . $list->name . '!';
            $this->refreshLists();
        } else {
            Log::info('Product already in list');
        }
    }

    public function removeFromList($listId)
    {
        Log::info('Removing product ' . $this->productId . ' from list ' . $listId);
        
        // Try to find in user's own lists first
        $list = auth()->user()->lists()->find($listId);
        
        // If not found, try collaborative lists where user can delete games
        if (!$list) {
            $list = \App\Models\ListModel::whereHas('collaborators', function ($query) use ($listId) {
                $query->where('user_id', auth()->id())
                      ->whereNotNull('accepted_at')
                      ->where('can_delete_games', true);
            })->find($listId);
        }
        
        if (!$list) {
            Log::error('List not found or user does not have permission to remove games');
            $this->successMessage = 'List not found or you do not have permission to remove games from this list.';
            return;
        }
        
        $list->items()->where('product_id', $this->productId)->delete();
        
        Log::info('Product removed from list successfully');
        $this->successMessage = 'Removed from ' . $list->name . '!';
        $this->refreshLists();
    }

    public function render()
    {
        return view('livewire.add-to-list-modal');
    }
}
