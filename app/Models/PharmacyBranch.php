<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'focal_person',
        'contact_number',
    ];
}