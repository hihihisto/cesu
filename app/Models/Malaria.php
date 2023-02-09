<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Malaria extends Model
{
    use HasFactory;

    protected $table = 'malaria';

    public $guarded = [];
}
