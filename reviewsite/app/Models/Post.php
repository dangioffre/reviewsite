<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Post extends Model
{
    use HasFactory, HasSEO;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'author',
        'featured_image',
        'is_featured',
        'type',
    ];

    /**
     * Get dynamic SEO data for this post
     */
    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->title,
            description: $this->excerpt,
            author: $this->author,
            image: $this->featured_image,
            published_time: $this->created_at,
            modified_time: $this->updated_at,
        );
    }
}
