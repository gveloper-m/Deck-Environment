<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'when',
        'notes',
        'happened'
    ];

    protected $casts = [
        'when' => 'datetime',
        'happened' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
