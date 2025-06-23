<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GameUserStatus;
use Illuminate\Support\Facades\Auth;

class GameStatusButtons extends Component
{
    public $product;
    public $have = false, $want = false, $played = false;
    public $stats = ['have' => 0, 'want' => 0, 'played' => 0];

    public function mount($product)
    {
        $this->product = $product;
        $this->loadStatus();
        $this->loadStats();
    }

    public function loadStatus()
    {
        if (!Auth::check()) return;
        $status = GameUserStatus::where('user_id', Auth::id())
            ->where('product_id', $this->product->id)
            ->first();
        $this->have = $status?->have ?? false;
        $this->want = $status?->want ?? false;
        $this->played = $status?->played ?? false;
    }

    public function loadStats()
    {
        $this->stats = [
            'have' => GameUserStatus::where('product_id', $this->product->id)->where('have', true)->count(),
            'want' => GameUserStatus::where('product_id', $this->product->id)->where('want', true)->count(),
            'played' => GameUserStatus::where('product_id', $this->product->id)->where('played', true)->count(),
        ];
    }

    public function toggle($type)
    {
        if (!Auth::check()) {
            $this->dispatchBrowserEvent('show-login');
            return;
        }
        $status = GameUserStatus::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
        ]);
        $status->$type = !$status->$type;
        $status->save();
        $this->loadStatus();
        $this->loadStats();
        session()->flash('message', ucfirst($type) . ' status updated!');
    }

    public function render()
    {
        return view('livewire.game-status-buttons');
    }
}
