<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ames extends Model
{
    //AMES
    
    use HasFactory;
    
    protected $table = 'ames';
    protected $primaryKey = 'EPIID';
    public $incrementing = false;

    public $guarded = [];
}
