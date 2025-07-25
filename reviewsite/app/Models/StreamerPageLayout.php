<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreamerPageLayout extends Model
{
    protected $fillable = [
        'user_id',
        'streamer_id',
        'layout', // JSON: {order, visibility, theme}
    ];

    protected $casts = [
        'layout' => 'array',
    ];
}
