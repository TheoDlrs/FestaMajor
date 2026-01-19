<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flyer extends Model
{
    protected $fillable = [
        'title',
        'image_url',
        'subtitle',
        'headline',
        'description',
        'quote_text',
        'quote_author',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}