<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListItem extends Model
{
    use HasFactory;
    protected $fillable = ['list_id', 'product_id', 'sort_order'];

    public function list()
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($listItem) {
            if (is_null($listItem->sort_order)) {
                // Set sort_order to the next highest value
                $maxOrder = static::where('list_id', $listItem->list_id)->max('sort_order') ?? 0;
                $listItem->sort_order = $maxOrder + 1;
            }
        });
    }
} 