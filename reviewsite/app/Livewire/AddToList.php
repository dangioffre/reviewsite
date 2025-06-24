<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ListModel;
use App\Models\ListItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AddToList extends Component
{
    public $productId;
    public $lists = [];
    public $showCreate = false;
    public $newListName = '';
    public $successMessage = '';
    public $testCount = 0;

    public function testButton()
    {
        $this->testCount++;
        Log::info('Test button clicked! Count: ' . $this->testCount);
        $this->successMessage = 'Test button clicked ' . $this->testCount . ' times!';
    }

    public function mount($productId)
    {
        Log::info('AddToList component mounted with productId: ' . $productId);
        $this->productId = $productId;
        $this->refreshLists();
    }

    public function refreshLists()
    {
        Log::info('Refreshing lists for user: ' . auth()->id());
        $this->lists = auth()->user()->lists()
            ->with(['items' => function($query) {
                $query->where('product_id', $this->productId);
            }])
            ->get();
        Log::info('Found ' . $this->lists->count() . ' lists');
    }

    public function createList()
    {
        Log::info('Creating new list: ' . $this->newListName);
        
        $this->validate([
            'newListName' => 'required|string|max:255',
        ]);

        $list = auth()->user()->lists()->create([
            'name' => $this->newListName,
            'slug' => Str::slug($this->newListName),
            'is_public' => false,
        ]);

        // Add the current game to the new list
        $list->items()->create([
            'product_id' => $this->productId,
        ]);

        Log::info('List created successfully: ' . $list->name);

        $this->newListName = '';
        $this->showCreate = false;
        $this->successMessage = 'List created and game added!';
        $this->refreshLists();
    }

    public function addToList($listId)
    {
        Log::info('Adding product ' . $this->productId . ' to list ' . $listId);
        
        $list = auth()->user()->lists()->findOrFail($listId);
        
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
        
        $list = auth()->user()->lists()->findOrFail($listId);
        
        $list->items()->where('product_id', $this->productId)->delete();
        
        Log::info('Product removed from list successfully');
        $this->successMessage = 'Removed from ' . $list->name . '!';
        $this->refreshLists();
    }

    public function render()
    {
        Log::info('Rendering AddToList component');
        return view('livewire.add-to-list');
    }
} 