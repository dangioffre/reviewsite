<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ListModel extends Model
{
    use HasFactory;
    protected $table = 'lists';
    protected $fillable = ['user_id', 'name', 'slug', 'is_public'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ListItem::class, 'list_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'list_items', 'list_id', 'product_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name) . '-' . Str::random(6);
            }
        });
    }
} 