<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meningitis extends Model
{
    use HasFactory;

    protected $table = 'meningitis';
    protected $primaryKey = 'EPIID';
    public $incrementing = false;

    public $guarded = [];

    public function getEdcsFacilityName() {
        $s = DohFacility::where('healthfacility_code', $this->edcs_healthFacilityCode)->first();

        if($s) {
            return $s->facility_name;
        }
        else {
            return 'UNKNOWN';
        }
    }
}
