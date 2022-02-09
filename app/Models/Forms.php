<?php

namespace App\Models;

//use App\Models\Records;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Forms extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $guarded = [];

    protected $dates = ['deleted_at'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function records() {
        return $this->belongsTo(Records::class);
    }

    public function getEditedBy() {
        if(!is_null($this->updated_by)) {
            $u = User::find($this->updated_by);
            return $u->name;
        }
        else {
            return NULL;
        }
    }

    public function getType() {
        if($this->pType == 'PROBABLE') {
            return 'SUSPECTED';
        }
        else if($this->pType == 'CLOSE CONTACT') {
            return 'CLOSE CONTACT';
        }
        else if($this->pType == 'CLOSE CONTACT') {
            return 'NON-COVID CASE';
        }
    }

    public function getTestNum() {
        if(!is_null($this->testDateCollected2)) {
            return 2;
        }
        else {
            return 1;
        }
    }

    public function ifScheduled() {
        if(!is_null($this->testDateCollected2) || !is_null($this->testDateCollected1)) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getLatestTestDate() {
        if(!is_null($this->testDateCollected2)) {
            return date('m/d/Y', strtotime($this->testDateCollected2));
        }
        else {
            return date('m/d/Y', strtotime($this->testDateCollected1));
        }
    }

    public function getLatestTestType() {
        if(!is_null($this->testDateCollected2)) {
            return $this->testType2;
        }
        else {
            return $this->testType1;
        }
    }

    public function getLatestTestResult() {
        if(!is_null($this->testDateCollected2)) {
            return $this->testResult2;
        }
        else {
            return $this->testResult1;
        }
    }

    public function getLatestTestDateReleased() {
        if(!is_null($this->testDateReleased2)) {
            return date('m/d/Y', strtotime($this->testDateReleased2));
        }
        else {
            return date('m/d/Y', strtotime($this->testDateReleased1));
        }
    }

    public function getAttendedOnSwab() {
        if(!is_null($this->isPresentOnSwabDay)) {
            if($this->isPresentOnSwabDay == 1) {
                return 'YES';
            }
            else {
                return 'NO';
            }
        }
        else {
            return 'PENDING';
        }
    }

    public function getReferralCode() {
        if(!is_null($this->majikCode)) {
            $check = PaSwabDetails::where('majikCode', $this->majikCode)->first();
            if($check) {
                return $check->linkCode;
            }
            else {
                return 'N/A';
            }
        }
        else {
            return 'N/A';
        }
    }

    public function getQuarantineStatus() {
        if($this->dispoType == 1) {
            return 'ADMITTED AT HOSPITAL';
        }
        else if($this->dispoType == 2) {
            return 'ADMITTED AT OTHER ISOLATION FACILITY';
        }
        else if($this->dispoType == 3) {
            return 'HOME QUARANTINE';
        }
        else if($this->dispoType == 4) {
            return 'DISCHARGED TO HOME';
        }
        else if($this->dispoType == 5) {
            return 'OTHERS';
        }
        else if($this->dispoType == 6) {
            return 'ADMITTED AT GENERAL TRIAS ISOLATION FACILITY';
        }
        else if($this->dispoType == 6) {
            return 'ADMITTED AT GENERAL TRIAS ISOLATION FACILITY #2';
        }
        else {
            return 'UNKNOWN';
        }
    }

    public function ifCaseFinished() {
        if($this->outcomeCondition == 'Recovered' || $this->outcomeCondition == 'Died' || $this->caseClassification == 'Non-COVID-19 Case') {
            return true;
        }
        else {
            return false;
        }
    }

    public function ifEligibleToUpdate() {
        if($this->ifCaseFinished()) {
            if(auth()->user()->ifTopAdmin()) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            if($this->caseClassification == 'Confirmed') {
                if(auth()->user()->ifTopAdmin()) {
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return true;
            }
        }
    }

    public function ifInFacilityOne() {
        if($this->status == 'approved' && $this->caseClassification == 'Confirmed' && $this->outcomeCondition == 'Active' && $this->dispoType == 6) {
            return true;
        }
        else {
            return false;
        }
    }

    public function ifOldCif() {
        //get latest cif
        $form = Forms::where('records_id', $this->records_id)->orderBy('created_at', 'DESC')->first();

        if($this->id == $form->id) {
            return false;
        }
        else {
            return true;
        }
    }

    public function getOldCif() {
        $form = Forms::where('id', '!=', $this->id)->where('records_id', $this->records_id)->orderBy('created_at', 'DESC')->get();

        return $form;
    }

    public function getNewCif() {
        $form = Forms::where('records_id', $this->records_id)->orderBy('created_at', 'DESC')->first();

        if($form) {
            return $form->id;
        }
        else {
            return false;
        }
    }

    public function getContactTracingList() {
        if(!is_null($this->ccid_list)) {
            $exploded = explode(",", $this->ccid_list);

            return Forms::whereIn('id', $exploded)->get();
        }
        else {
            return false;
        }
    }
}
