<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfReports extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'refCode',
    ];
}
