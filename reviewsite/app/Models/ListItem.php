<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListItem extends Model
{
    use HasFactory;
    protected $fillable = ['list_id', 'product_id'];

    public function list()
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
} 