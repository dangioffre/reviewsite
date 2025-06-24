<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ListModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserLists extends Component
{
    public $lists;
    public $newListName = '';
    public $editingListId = null;
    public $editingListName = '';

    protected $rules = [
        'newListName' => 'required|string|max:255',
        'editingListName' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->refreshLists();
    }

    public function refreshLists()
    {
        $this->lists = ListModel::where('user_id', Auth::id())->withCount('items')->get();
    }

    public function createList()
    {
        $this->validateOnly('newListName');
        ListModel::create([
            'user_id' => Auth::id(),
            'name' => $this->newListName,
            'slug' => Str::slug($this->newListName) . '-' . Str::random(6),
        ]);
        $this->newListName = '';
        $this->refreshLists();
    }

    public function startEditing($listId)
    {
        $this->editingListId = $listId;
        $this->editingListName = $this->lists->find($listId)->name ?? '';
    }

    public function saveEdit($listId)
    {
        $this->validateOnly('editingListName');
        $list = ListModel::where('id', $listId)->where('user_id', Auth::id())->firstOrFail();
        $list->name = $this->editingListName;
        $list->save();
        $this->editingListId = null;
        $this->editingListName = '';
        $this->refreshLists();
    }

    public function togglePublic($listId)
    {
        $list = ListModel::where('id', $listId)->where('user_id', Auth::id())->firstOrFail();
        $list->is_public = !$list->is_public;
        $list->save();
        $this->refreshLists();
    }

    public function deleteList($listId)
    {
        $list = ListModel::where('id', $listId)->where('user_id', Auth::id())->firstOrFail();
        $list->delete();
        $this->refreshLists();
    }

    public function render()
    {
        return view('livewire.user-lists');
    }
} 