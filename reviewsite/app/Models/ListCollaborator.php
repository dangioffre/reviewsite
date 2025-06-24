<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListCollaborator extends Model
{
    use HasFactory;

    protected $fillable = [
        'list_id', 'user_id', 'permission', 'invited_at', 'accepted_at'
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function list()
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accept()
    {
        $this->update(['accepted_at' => now()]);
    }

    public function isPending()
    {
        return is_null($this->accepted_at);
    }

    public function isAccepted()
    {
        return !is_null($this->accepted_at);
    }

    public function canEdit()
    {
        return in_array($this->permission, ['edit', 'admin']);
    }

    public function canAdmin()
    {
        return $this->permission === 'admin';
    }
}
