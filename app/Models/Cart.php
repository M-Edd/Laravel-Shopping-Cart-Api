<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'discount',
    ];

    protected $casts = [
        'content' => 'array',
        'discount' => 'array',
    ];
}
