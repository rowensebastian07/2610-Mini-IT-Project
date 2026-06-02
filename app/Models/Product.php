<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;

class Product extends Model
{
    protected $fillable = [
        'club_id',
        'title',
        'description',
        'price',
        'image',
        'stock',
        'is_sold_out',   
    ];

    protected $casts = [
        'is_sold_out' => 'boolean',  
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
