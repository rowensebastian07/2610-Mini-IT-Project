<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

   protected $fillable = [
    'club_id', 'user_id', 'title', 'content', 'image',
    'likes_count', 'comments_count'
];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function user()
    {
    
    return $this->belongsTo(User::class);
    }
}