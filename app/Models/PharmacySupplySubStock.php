<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacySupplySubStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'subsupply_id',
        'expiration_date',
        'current_box_stock',
        'current_piece_stock',
    ];
}
