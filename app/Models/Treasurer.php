<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treasurer extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'name',
        'bank_name',
        'account_number',
        'qr_payment',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}

