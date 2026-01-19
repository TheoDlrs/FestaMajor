<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image_url',
        'has_sizes',
    ];

    protected $casts = [
        'has_sizes' => 'boolean',
    ];

    public function getImageUrlAttribute($value)
    {
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        return asset('storage/'.$value);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
