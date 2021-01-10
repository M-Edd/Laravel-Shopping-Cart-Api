<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'images_urls',
        'price',
    ];

    protected $casts = [
        'images_urls' => 'array',
    ];
}
