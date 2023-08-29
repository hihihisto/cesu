<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PharmacyStockCard extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'supply_id',
        'type',
        'before_qty',
        'qty_to_process',
        'after_qty',
        'total_cost',
        'drsi_number',

        'recipient',
        'remarks',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }
}