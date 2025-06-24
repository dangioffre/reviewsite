<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ListModel;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UserLists extends Component
{
    public $lists = [];
    public $showCreate = false;
    public $newListName = '';
    public $editingList = null;
    public $editingName = '';
    public $viewingList = null;
    public $searchTerm = '';
    public $searchResults = [];
    public $showSearch = false;
    public $successMessage = '';
    
    // New properties for enhanced features
    public $selectedCategory = 'general';
    public $selectedSortBy = 'date_added';
    public $selectedSortDirection = 'desc';
    public $allowCollaboration = false;
    public $allowComments = true;
    
    // Category editing
    public $editingCategoryListId = null;
    public $editingCategoryValue = 'general';



    public function mount()
    {
        $this->refreshLists();
    }

    public function refreshLists()
    {
        $this->lists = auth()->user()->lists()
            ->withCount('items')
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createList()
    {
        $this->validate([
            'newListName' => 'required|string|max:255',
        ]);

        auth()->user()->lists()->create([
            'name' => $this->newListName,
            'slug' => Str::slug($this->newListName),
            'is_public' => false,
            'category' => $this->selectedCategory,
            'sort_by' => $this->selectedSortBy,
            'sort_direction' => $this->selectedSortDirection,
            'allow_collaboration' => $this->allowCollaboration,
            'allow_comments' => $this->allowComments,
        ]);

        $this->newListName = '';
        $this->showCreate = false;
        $this->selectedCategory = 'general';
        $this->selectedSortBy = 'date_added';
        $this->selectedSortDirection = 'desc';
        $this->allowCollaboration = false;
        $this->allowComments = true;
        $this->successMessage = 'List created successfully!';
        $this->refreshLists();
    }

    public function startEditing($listId)
    {
        $list = $this->lists->find($listId);
        $this->editingList = $listId;
        $this->editingName = $list->name;
    }

    public function saveEdit()
    {
        $this->validate([
            'editingName' => 'required|string|max:255',
        ]);

        $list = auth()->user()->lists()->findOrFail($this->editingList);
        $list->update([
            'name' => $this->editingName,
            'slug' => Str::slug($this->editingName),
        ]);

        $this->editingList = null;
        $this->editingName = '';
        $this->successMessage = 'List updated successfully!';
        $this->refreshLists();
    }

    public function cancelEdit()
    {
        $this->editingList = null;
        $this->editingName = '';
    }

    public function togglePublic($listId)
    {
        $list = auth()->user()->lists()->findOrFail($listId);
        $list->update(['is_public' => !$list->is_public]);
        
        $status = $list->is_public ? 'public' : 'private';
        $this->successMessage = "List is now {$status}!";
        $this->refreshLists();
    }

    public function deleteList($listId)
    {
        $list = auth()->user()->lists()->findOrFail($listId);
        $list->delete();
        
        $this->successMessage = 'List deleted successfully!';
        $this->refreshLists();
        
        // Close view if we're viewing the deleted list
        if ($this->viewingList == $listId) {
            $this->viewingList = null;
        }
    }

    public function viewList($listId)
    {
        $this->viewingList = $listId;
        $this->searchTerm = '';
        $this->searchResults = [];
        $this->showSearch = false;
    }

    public function closeView()
    {
        $this->viewingList = null;
        $this->searchTerm = '';
        $this->searchResults = [];
        $this->showSearch = false;
    }

    public function copyPublicLink($listId)
    {
        $list = auth()->user()->lists()->findOrFail($listId);
        $this->successMessage = "Public link copied to clipboard!";
        
        // The actual copying will be handled by JavaScript
        $this->dispatch('copy-to-clipboard', [
            'text' => route('lists.public', $list->slug)
        ]);
    }

    public function searchGames()
    {
        Log::info('Search called with term: ' . $this->searchTerm);
        
        if (strlen($this->searchTerm) < 2) {
            $this->searchResults = [];
            Log::info('Search term too short, clearing results');
            return;
        }

        $this->searchResults = Product::where('type', 'game')
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
            })
            ->with(['genre', 'platform'])
            ->limit(10)
            ->get();
            
        Log::info('Search results count: ' . $this->searchResults->count());
        Log::info('Search results: ' . $this->searchResults->pluck('name')->toJson());
    }

    public function addGameToList($gameId)
    {
        $list = auth()->user()->lists()->findOrFail($this->viewingList);
        
        // Check if game is already in the list
        if (!$list->items()->where('product_id', $gameId)->exists()) {
            $list->items()->create(['product_id' => $gameId]);
            $this->successMessage = 'Game added to list!';
            $this->refreshLists();
        } else {
            $this->successMessage = 'Game is already in this list!';
        }
        
        $this->searchTerm = '';
        $this->searchResults = [];
    }

    public function removeGameFromList($gameId)
    {
        $list = auth()->user()->lists()->findOrFail($this->viewingList);
        $list->items()->where('product_id', $gameId)->delete();
        
        $this->successMessage = 'Game removed from list!';
        $this->refreshLists();
    }

    public function updatedSearchTerm()
    {
        Log::info('updatedSearchTerm called with: ' . $this->searchTerm);
        $this->searchGames();
    }

    // New methods for enhanced features
    public function duplicateList($listId)
    {
        $originalList = auth()->user()->lists()->with('items.product')->findOrFail($listId);
        
        $newList = auth()->user()->lists()->create([
            'name' => $originalList->name . ' (Copy)',
            'slug' => Str::slug($originalList->name . ' Copy') . '-' . Str::random(6),
            'is_public' => false, // Always create as private
            'category' => $originalList->category,
            'sort_by' => $originalList->sort_by,
            'sort_direction' => $originalList->sort_direction,
            'cloned_from' => $originalList->id,
            'allow_collaboration' => $originalList->allow_collaboration,
            'allow_comments' => $originalList->allow_comments,
        ]);
        
        // Copy all items
        foreach ($originalList->items as $item) {
            $newList->items()->create([
                'product_id' => $item->product_id,
                'sort_order' => $item->sort_order,
            ]);
        }
        
        $this->successMessage = 'List duplicated successfully!';
        $this->refreshLists();
    }

    public function updateListSort($listId, $sortBy, $direction = null)
    {
        $list = auth()->user()->lists()->findOrFail($listId);
        
        // Ensure direction is always valid
        if ($direction && in_array($direction, ['asc', 'desc'])) {
            // Use provided direction if valid
            $newDirection = $direction;
        } elseif ($list->sort_by === $sortBy) {
            // Toggle direction if same sort field
            $newDirection = $list->sort_direction === 'asc' ? 'desc' : 'asc';
        } else {
            // Default to 'asc' for new sort field
            $newDirection = 'asc';
        }
        
        $list->update([
            'sort_by' => $sortBy,
            'sort_direction' => $newDirection,
        ]);
        
        $this->successMessage = 'List sorting updated!';
        $this->refreshLists();
    }

    public function followList($listId)
    {
        $list = ListModel::where('is_public', true)->findOrFail($listId);
        
        if (!$list->isFollowedBy(auth()->id())) {
            $list->followers()->create(['user_id' => auth()->id()]);
            $list->increment('followers_count');
            $this->successMessage = 'Now following this list!';
        } else {
            $list->followers()->where('user_id', auth()->id())->delete();
            $list->decrement('followers_count');
            $this->successMessage = 'Unfollowed list.';
        }
        
        $this->refreshLists();
    }
    
    public function unfollowList($listId)
    {
        $list = ListModel::where('is_public', true)->findOrFail($listId);
        
        if ($list->isFollowedBy(auth()->id())) {
            $list->followers()->where('user_id', auth()->id())->delete();
            $list->decrement('followers_count');
            $this->successMessage = 'Unfollowed list.';
        }
        
        $this->refreshLists();
    }

    public function startEditingCategory($listId)
    {
        $list = auth()->user()->lists()->findOrFail($listId);
        $this->editingCategoryListId = $listId;
        $this->editingCategoryValue = $list->category ?? 'general';
    }
    
    public function saveCategory()
    {
        $list = auth()->user()->lists()->findOrFail($this->editingCategoryListId);
        $list->update(['category' => $this->editingCategoryValue]);
        
        $this->editingCategoryListId = null;
        $this->editingCategoryValue = 'general';
        $this->successMessage = 'List category updated!';
        $this->refreshLists();
    }
    
    public function cancelCategoryEdit()
    {
        $this->editingCategoryListId = null;
        $this->editingCategoryValue = 'general';
    }

    public function render()
    {
        return view('livewire.user-lists', [
            'categories' => ListModel::$categories,
            'sortOptions' => ListModel::$sortOptions,
        ]);
    }
} 