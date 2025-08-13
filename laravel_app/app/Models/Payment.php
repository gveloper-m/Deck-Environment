<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'booking_id',
        'amount',
        'payed',
        'when',
        'name',
    ];

    protected $casts = [
        'payed' => 'boolean',
        'when'  => 'datetime',
    ];
}
