<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'field_name',
        'value',
    ];

    public function user()
    {
        return $this->belongsTo(PlatformUser::class, 'user_id');
    }
}
