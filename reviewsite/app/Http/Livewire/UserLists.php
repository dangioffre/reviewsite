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
        ]);

        $this->newListName = '';
        $this->showCreate = false;
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
        if (strlen($this->searchTerm) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Product::where('name', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
            ->limit(10)
            ->get();
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
        $this->searchGames();
    }

    public function render()
    {
        return view('livewire.user-lists');
    }
} 