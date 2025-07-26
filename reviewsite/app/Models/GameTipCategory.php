<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameTipCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    // Relationships
    public function tips(): HasMany
    {
        return $this->hasMany(GameTip::class, 'game_tip_category_id');
    }
}
