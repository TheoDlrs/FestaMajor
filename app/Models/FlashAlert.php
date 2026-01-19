<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashAlert extends Model
{
    protected $fillable = [
        'is_active',
        'type',
        'title',
        'message',
        'button_text',
        'button_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}