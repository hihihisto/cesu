<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LabResultLogBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'for_case_id',
        'disease_tag',
        'lname',
        'fname',
        'mname',
        'suffix',
        'gender',
        'date_collected',
        'collector_name',
        'specimen_type',
        'sent_to_ritm',
        'ritm_date_sent',
        'ritm_date_received',
        'driver_name',
        'test_type',
        'result',
        'interpretation',
        'remarks',
        'facility_id',
    ];

    public function getName() {
        $fullname = $this->lname.", ".$this->fname;

        if(!is_null($this->mname)) {
            $fullname = $fullname." ".$this->mname;
        }

        if(!is_null($this->suffix)) {
            $fullname = $fullname." ".$this->suffix;
        }

        return $fullname;
        //return $this->lname.", ".$this->fname.' '.$this->suffix." ".$this->mname;
    }
}
