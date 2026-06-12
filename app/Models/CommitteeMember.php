<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'name',
        'role',
        'description',
        'profile_picture',
        'status',
        'theme',   
    ];
}

