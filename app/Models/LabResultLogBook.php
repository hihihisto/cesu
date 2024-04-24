<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LabResultLogBook extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'for_case_id',
        'disease_tag',
        'lname',
        'fname',
        'mname',
        'suffix',
        'age',
        'gender',
        'date_collected',
        'collector_name',
        'specimen_type',
        'sent_to_ritm',
        'ritm_date_sent',
        'driver_name',
        'ritm_date_received',
        'ritm_received_by',
        'lab_number',

        'date_sent_others',
        'date_received_others',
        'facility_name_others',

        'date_released',
        
        'test_type',
        'result',
        'result_updated_date',
        'result_updated_by',

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

    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
