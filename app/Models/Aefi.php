<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aefi extends Model
{
    use HasFactory;

    protected $table = 'pidsr_AEFI';
    protected $primaryKey = 'EPIID';
    public $incrementing = false;

    public $guarded = [];
}
