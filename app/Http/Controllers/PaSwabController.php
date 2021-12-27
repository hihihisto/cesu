<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Forms;
use App\Models\Records;
use App\Models\PaSwabLinks;
use App\Models\Interviewers;
use Illuminate\Http\Request;
use App\Models\PaSwabDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Models\MonitoringSheetMaster;
use App\Http\Requests\PaSwabValidationRequest;
use IlluminateAgnostic\Collection\Support\Str;

class PaSwabController extends Controller
{
    public function index($locale) {
        if (! in_array($locale, ['en', 'fil'])) {
            abort(404);
        }

        App::setLocale($locale);
        
        if(request()->input('rlink') && request()->input('s')) {
            $checkcode = PaSwabLinks::where('code', mb_strtoupper(request()->input('rlink')))
            ->where('secondary_code', mb_strtoupper(request()->input('s')))
            ->where('active', 1)
            ->first();

            if($checkcode) {
                return view('paswab_index', [
                    'proceed' => 1,
                    'interviewerName' => $checkcode->interviewer->getCifName(),
                    'enableLockAddress' => $checkcode->enableLockAddress,
                    'lock_brgy' => $checkcode->lock_brgy,
                    'lock_city' => $checkcode->lock_city,
                    'lock_city_text' => $checkcode->lock_city_text,
                    'lock_province' => $checkcode->lock_province,
                    'lock_province_text' => $checkcode->lock_province_text,
                ]);
            }
            else {
                return view('paswab_index', ['proceed' => 0]);
            }
        }
        else {
            return view('paswab_index', ['proceed' => 0]);
        }
    }

    public function selectLanguage() {
        return view('paswab_language');
    }

    public function options(Request $request) {
        if($request->submit == 'bulkApprove') {
            $request->validate([
                'testDateCollected1' => 'required|date|after_or_equal:today',
                'testType1' => 'required|in:OPS,NPS,OPS AND NPS,ANTIGEN,ANTIBODY,OTHERS',
                'testTypeOtherRemarks1' => ($request->testType1 == "ANTIGEN" || $request->testType1 == "OTHERS") ? 'required' : 'nullable',
                'antigenKit1' => ($request->testType1 == "ANTIGEN") ? 'required' : 'nullable',
            ]);

            $list = PaSwabDetails::whereIn('id', $request->bulkIDList)->where('status', 'pending')->get();

            foreach($list as $data) {
                if(!($data->checkPaswabBrgyData())) {
                    return abort(401);
                }
                
                //Test Type final validator forAntigen
                if($data->forAntigen == 1) {
                    $ttype = 'ANTIGEN';
                    //dapat baguhin 'to ayon sa system settings
                    $tOtherRemarks = 'CONFIRMATORY';
                    $tAntigenKit = 'ABBOTT';
                }
                else {
                    $ttype = $request->testType1;
                    $tOtherRemarks = $request->testTypeOtherRemarks1;
                    $tAntigenKit = $request->antigenKit1;
                }

                if($data->status == 'pending') {
                    //create record data first
                    if($data->isNewRecord == 1) {
                        $rec = $request->user()->records()->create([
                            'status' => 'approved',
                            'lname' => mb_strtoupper($data->lname),
                            'fname' => mb_strtoupper($data->fname),
                            'mname' => (!is_null($data->mname)) ? mb_strtoupper($data->mname) : null,
                            'gender' => strtoupper($data->gender),
                            'isPregnant' => $data->isPregnant,
                            'cs' => strtoupper($data->cs),
                            'nationality' => strtoupper($data->nationality),
                            'bdate' => $data->bdate,
                            'mobile' => $data->mobile,
                            'phoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                            'email' => $data->email,
                            'philhealth' => $data->philhealth,
                            'address_houseno' => strtoupper($data->address_houseno),
                            'address_street' => strtoupper($data->address_street),
                            'address_brgy' => strtoupper($data->address_brgy),
                            'address_city' => strtoupper($data->address_city),
                            'address_cityjson' => $data->address_cityjson,
                            'address_province' => strtoupper($data->address_province),
                            'address_provincejson' => $data->address_provincejson,
            
                            'permaaddressDifferent' => 0,
                            'permaaddress_houseno' => strtoupper($data->address_houseno),
                            'permaaddress_street' => strtoupper($data->address_street),
                            'permaaddress_brgy' => strtoupper($data->address_brgy),
                            'permaaddress_city' => strtoupper($data->address_city),
                            'permaaddress_cityjson' => $data->address_cityjson,
                            'permaaddress_province' => strtoupper($data->address_province),
                            'permaaddress_provincejson' => $data->address_provincejson,
                            'permamobile' => $data->mobile,
                            'permaphoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                            'permaemail' => $data->email,
            
                            'hasOccupation' => (!is_null($data->occupation)) ? 1 : 0,
                            'occupation' => $data->occupation,
                            'worksInClosedSetting' => $data->worksInClosedSetting,
                            'occupation_lotbldg' => $data->occupation_lotbldg,
                            'occupation_street' => $data->occupation_street,
                            'occupation_brgy' => $data->occupation_brgy,
                            'occupation_city' => $data->occupation_city,
                            'occupation_cityjson' => $data->occupation_cityjson,
                            'occupation_province' => $data->occupation_province,
                            'occupation_provincejson' => $data->occupation_provincejson,
                            'occupation_name' => $data->occupation_name,
                            'occupation_mobile' => $data->occupation_mobile,
                            'occupation_email' => $data->occupation_email,
            
                            'natureOfWork' => (!is_null($data->occupation)) ? mb_strtoupper($data->natureOfWork) : NULL,
                            'natureOfWorkIfOthers' => (!is_null($data->occupation) && $data->natureOfWork == 'OTHERS') ? mb_strtoupper($data->natureOfWorkIfOthers) : NULL,

                            'vaccinationDate1' => $data->vaccinationDate1,
                            'vaccinationName1' => $data->vaccinationName1,
                            'vaccinationNoOfDose1' => $data->vaccinationNoOfDose1,
                            'vaccinationFacility1' => $data->vaccinationFacility1,
                            'vaccinationRegion1' => $data->vaccinationRegion1,
                            'haveAdverseEvents1' => $data->haveAdverseEvents1,

                            'vaccinationDate2' => $data->vaccinationDate2,
                            'vaccinationName2' => $data->vaccinationName2,
                            'vaccinationNoOfDose2' => $data->vaccinationNoOfDose2,
                            'vaccinationFacility2' => $data->vaccinationFacility2,
                            'vaccinationRegion2' => $data->vaccinationRegion2,
                            'haveAdverseEvents2' => $data->haveAdverseEvents2,
                        ]);
                    }
                    else {
                        $rec = Records::findOrFail($data->records_id);

                        $rec->update([
                            'status' => 'approved',
                            'isPregnant' => $data->isPregnant,
                            'cs' => strtoupper($data->cs),
                            'nationality' => strtoupper($data->nationality),
                            'mobile' => $data->mobile,
                            'phoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                            'email' => $data->email,
                            'philhealth' => $data->philhealth,
                            'address_houseno' => strtoupper($data->address_houseno),
                            'address_street' => strtoupper($data->address_street),
                            'address_brgy' => strtoupper($data->address_brgy),
                            'address_city' => strtoupper($data->address_city),
                            'address_cityjson' => $data->address_cityjson,
                            'address_province' => strtoupper($data->address_province),
                            'address_provincejson' => $data->address_provincejson,
            
                            'permaaddressDifferent' => 0,
                            'permaaddress_houseno' => strtoupper($data->address_houseno),
                            'permaaddress_street' => strtoupper($data->address_street),
                            'permaaddress_brgy' => strtoupper($data->address_brgy),
                            'permaaddress_city' => strtoupper($data->address_city),
                            'permaaddress_cityjson' => $data->address_cityjson,
                            'permaaddress_province' => strtoupper($data->address_province),
                            'permaaddress_provincejson' => $data->address_provincejson,
                            'permamobile' => $data->mobile,
                            'permaphoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                            'permaemail' => $data->email,
            
                            'hasOccupation' => (!is_null($data->occupation)) ? 1 : 0,
                            'occupation' => $data->occupation,
                            'worksInClosedSetting' => $data->worksInClosedSetting,
                            'occupation_lotbldg' => $data->occupation_lotbldg,
                            'occupation_street' => $data->occupation_street,
                            'occupation_brgy' => $data->occupation_brgy,
                            'occupation_city' => $data->occupation_city,
                            'occupation_cityjson' => $data->occupation_cityjson,
                            'occupation_province' => $data->occupation_province,
                            'occupation_provincejson' => $data->occupation_provincejson,
                            'occupation_name' => $data->occupation_name,
                            'occupation_mobile' => $data->occupation_mobile,
                            'occupation_email' => $data->occupation_email,
            
                            'natureOfWork' => (!is_null($data->occupation)) ? mb_strtoupper($data->natureOfWork) : NULL,
                            'natureOfWorkIfOthers' => (!is_null($data->occupation) && $data->natureOfWork == 'OTHERS') ? mb_strtoupper($data->natureOfWorkIfOthers) : NULL,

                            'vaccinationDate1' => $data->vaccinationDate1,
                            'vaccinationName1' => $data->vaccinationName1,
                            'vaccinationNoOfDose1' => $data->vaccinationNoOfDose1,
                            'vaccinationFacility1' => $data->vaccinationFacility1,
                            'vaccinationRegion1' => $data->vaccinationRegion1,
                            'haveAdverseEvents1' => $data->haveAdverseEvents1,

                            'vaccinationDate2' => $data->vaccinationDate2,
                            'vaccinationName2' => $data->vaccinationName2,
                            'vaccinationNoOfDose2' => $data->vaccinationNoOfDose2,
                            'vaccinationFacility2' => $data->vaccinationFacility2,
                            'vaccinationRegion2' => $data->vaccinationRegion2,
                            'haveAdverseEvents2' => $data->haveAdverseEvents2,
                        ]);

                        $oldform = Forms::where('records_id', $rec->id)->orderBy('created_at', 'DESC')->first();
                    }

                    if(!is_null($rec->philhealth)) {
                        if($ttype == "OPS" || $ttype == "NPS" || $ttype == "OPS AND NPS") {
                            $trigger = 0;
                            $addMinutes = 0;

                            while ($trigger != 1) {
                                $oniStartTime = date('H:i:s', strtotime('14:00:00 + '. $addMinutes .' minutes'));

                                $query = Forms::with('records')
                                ->where('testDateCollected1', $request->testDateCollected1)
                                ->whereIn('testType1', ['OPS', 'NPS', 'OPS AND NPS'])
                                ->whereHas('records', function ($q) {
                                    $q->whereNotNull('philhealth');
                                })
                                ->where('oniTimeCollected1', $oniStartTime)->get();

                                if($query->count()) {
                                    if($query->count() < 5) {
                                        $oniTimeFinal = $oniStartTime;
                                        $trigger = 1;
                                    }
                                    else {
                                        $addMinutes = $addMinutes + 5;
                                    }
                                }
                                else {
                                    $oniTimeFinal = $oniStartTime;
                                    $trigger = 1;
                                }
                            }
                        }
                        else {
                            $oniTimeFinal = NULL;
                        }
                    }
                    else {
                        $oniTimeFinal = NULL;
                    }

                    //Auto Change Testing Category/Subgroup Base on the patient data
                    $testCat = [];
                    if(!is_null($data->SAS)) {
                        array_push($testCat, "C");
                    }
                    else {
                        array_push($testCat, "D.1");
                    }

                    if($data->getAgeInt() >= 60) {
                        array_push($testCat, "B");
                    }
                    if(!in_array("D.1", $testCat) && $data->pType == 'CLOSE CONTACT') {
                        array_push($testCat, "D.1");
                    }
                    if($data->isPregnant == 1 || $data->isForHospitalization == 1) {
                        array_push($testCat, "F");
                    }

                    //Auto Change Case Classification to Probable Based on Symptoms
                    if(!is_null($data->SAS)) {
                        if(in_array('Anosmia (Loss of Smell)', explode(",", $data->SAS)) || in_array('Ageusia (Loss of Taste)', explode(",", $data->SAS))) {
                            $caseClassi = 'Probable';
                        }
                        else {
                            $caseClassi = 'Suspect';
                        }
                    }
                    else {
                        $caseClassi = 'Suspect';
                    }

                    $createform = $request->user()->form()->create([
                        'morbidityMonth' => date('Y-m-d'),
                        'dateReported' => date('Y-m-d'),
                        'majikCode' => $data->majikCode,
                        'status' => 'approved',
                        'isPresentOnSwabDay' => NULL,
                        'records_id' => $rec->id,
                        'drunit' => 'CHO GENERAL TRIAS',
                        'drregion' => '4A',
                        'drprovince' => 'CAVITE',
                        'interviewerName' => $data->getDefaultInterviewerName(),
                        'interviewerMobile' => '09190664324',
                        'interviewDate' => $data->interviewDate,
                        'informantName' => NULL,
                        'informantRelationship' => NULL,
                        'informantMobile' => NULL,
                        'existingCaseList' => '1',
                        'ecOthersRemarks' => NULL,
                        'pType' => ($data->pType == 'FOR TRAVEL') ? 'TESTING' : $data->pType,
                        'isForHospitalization' => $data->isForHospitalization,
                        'testingCat' => implode(",", $testCat),
                        'havePreviousCovidConsultation' => ($data->isNewRecord == 0) ? '1' : '0',
                        'dateOfFirstConsult' => ($data->isNewRecord == 0) ? ($oldform) ? $oldform->interviewDate : NULL : NULL,
                        'facilityNameOfFirstConsult' => ($data->isNewRecord == 0) ? ($oldform) ? $oldform->drunit : NULL : NULL,

                        'dispoType' => 3,
                        'dispoName' => NULL,
                        'dispoDate' => date('Y-m-d 08:00:00', strtotime($data->interviewDate)),
                        'healthStatus' => (!is_null($data->SAS)) ? 'Mild' : 'Asymptomatic',
                        'caseClassification' => $caseClassi,
                        'isHealthCareWorker' => '0',
                        'healthCareCompanyName' => NULL,
                        'healthCareCompanyLocation' => NULL,
                        'isOFW' => '0',
                        'OFWCountyOfOrigin' => NULL,
                        'ofwType' => NULL,
                        'isFNT' => '0',
                        'lsiType' => NULL,
                        'FNTCountryOfOrigin' => NULL,
                        'isLSI' => '0',
                        'LSICity' => NULL,
                        'LSIProvince' => NULL,
                        'isLivesOnClosedSettings' => '0',
                        'institutionType' => NULL,
                        'institutionName' => NULL,
                        'indgSpecify' => NULL,
                        'dateOnsetOfIllness' => $data->dateOnsetOfIllness,
                        'SAS' => $data->SAS,
                        'SASFeverDeg' => $data->SASFeverDeg,
                        'SASOtherRemarks' => $data->SASOtherRemarks,
                        'COMO' => $data->COMO,
                        'COMOOtherRemarks' => $data->COMOOtherRemarks,
                        'PregnantLMP' => $data->ifPregnantLMP,
                        'PregnantHighRisk' => ($data->isPregnant == 1) ? '1' : '0',
                        'diagWithSARI' => '0',
                        'imagingDoneDate' => $data->imagingDoneDate,
                        'imagingDone' => $data->imagingDone,
                        'imagingResult' => $data->imagingResult,
                        'imagingOtherFindings' => $data->imagingOtherFindings,

                        'testedPositiveUsingRTPCRBefore' => '0',
                        'testedPositiveNumOfSwab' => '0',
                        'testedPositiveLab' => NULL,
                        'testedPositiveSpecCollectedDate' => NULL,

                        'testDateCollected1' => $request->testDateCollected1,
                        'oniTimeCollected1' => $oniTimeFinal,
                        'testDateReleased1' => NULL,
                        'testLaboratory1' => NULL,
                        'testType1' => $ttype,
                        'testTypeAntigenRemarks1' => ($ttype == "ANTIGEN") ? mb_strtoupper($tOtherRemarks) : NULL,
                        'antigenKit1' => ($ttype == "ANTIGEN") ? mb_strtoupper($tAntigenKit) : NULL,
                        'testTypeOtherRemarks1' => ($ttype == "OTHERS") ? mb_strtoupper($tOtherRemarks) : NULL,
                        'testResult1' => 'PENDING',
                        'testResultOtherRemarks1' => NULL,

                        'testDateCollected2' => NULL,
                        'oniTimeCollected2' => NULL,
                        'testDateReleased2' => NULL,
                        'testLaboratory2' => NULL,
                        'testType2' => NULL,
                        'testTypeAntigenRemarks2' => NULL,
                        'antigenKit2' => NULL,
                        'testTypeOtherRemarks2' => NULL,
                        'testResult2' => NULL,
                        'testResultOtherRemarks2' => NULL,

                        'outcomeCondition' => 'Active',
                        'outcomeRecovDate' => NULL,
                        'outcomeDeathDate' => NULL,
                        'deathImmeCause' => NULL,
                        'deathAnteCause' => NULL,
                        'deathUndeCause' => NULL,
                        'contriCondi' => NULL,

                        'expoitem1' => $data->expoitem1,
                        'expoDateLastCont' => $data->expoDateLastCont,

                        'expoitem2' => '0',
                        'intCountry' => NULL,
                        'intDateFrom' => NULL,
                        'intDateTo' => NULL,
                        'intWithOngoingCovid' => 'N/A',
                        'intVessel' => NULL,
                        'intVesselNo' => NULL,
                        'intDateDepart' => NULL,
                        'intDateArrive' => NULL,

                        'placevisited' => NULL,

                        'locName1' => NULL,
                        'locAddress1' => NULL,
                        'locDateFrom1' => NULL,
                        'locDateTo1' => NULL,
                        'locWithOngoingCovid1' => 'N/A',

                        'locName2' => NULL,
                        'locAddress2' => NULL,
                        'locDateFrom2' => NULL,
                        'locDateTo2' => NULL,
                        'locWithOngoingCovid2' => 'N/A',
                        
                        'locName3' => NULL,
                        'locAddress3' => NULL,
                        'locDateFrom3' => NULL,
                        'locDateTo3' => NULL,
                        'locWithOngoingCovid3' => 'N/A',
                        
                        'locName4' => NULL,
                        'locAddress4' => NULL,
                        'locDateFrom4' => NULL,
                        'locDateTo4' => NULL,
                        'locWithOngoingCovid4' => 'N/A',

                        'locName5' => NULL,
                        'locAddress5' => NULL,
                        'locDateFrom5' => NULL,
                        'locDateTo5' => NULL,
                        'locWithOngoingCovid5' => 'N/A',

                        'locName6' => NULL,
                        'locAddress6' => NULL,
                        'locDateFrom6' => NULL,
                        'locDateTo6' => NULL,
                        'locWithOngoingCovid6' => 'N/A',

                        'locName7' => NULL,
                        'locAddress7' => NULL,
                        'locDateFrom7' => NULL,
                        'locDateTo7' => NULL,
                        'locWithOngoingCovid7' => 'N/A',

                        'localVessel1' => NULL,
                        'localVesselNo1' => NULL,
                        'localOrigin1' => NULL,
                        'localDateDepart1' => NULL,
                        'localDest1' => NULL,
                        'localDateArrive1' => NULL,

                        'localVessel2' => NULL,
                        'localVesselNo2' => NULL,
                        'localOrigin2' => NULL,
                        'localDateDepart2' => NULL,
                        'localDest2' => NULL,
                        'localDateArrive2' => NULL,

                        'contact1Name' => $data->contact1Name,
                        'contact1No' => $data->contact1No,
                        'contact2Name' => $data->contact2Name,
                        'contact2No' => $data->contact2No,
                        'contact3Name' => $data->contact3Name,
                        'contact3No' => $data->contact3No,
                        'contact4Name' => $data->contact4Name,
                        'contact4No' => $data->contact4No,

                        'remarks' => $data->patientmsg,
                    ]);

                    //Create Monitoring Sheet
                    $msheet_search = MonitoringSheetMaster::where('forms_id', $createform->id)->first();
                    if(!$msheet_search) {
                        $foundunique = false;
                        while(!$foundunique) {
                            $majik = Str::random(30);
                            
                            $search = MonitoringSheetMaster::where('magicURL', $majik);
                            if($search->count() == 0) {
                                $foundunique = true;
                            }
                        }

                        $newmsheet = new MonitoringSheetMaster;

                        $newmsheet->forms_id = $createform->id;
                        $newmsheet->region = '4A';
                        $newmsheet->date_lastexposure = (!is_null($createform->expoDateLastCont)) ? $createform->expoDateLastCont : $createform->interviewDate;
                        $newmsheet->date_startquarantine = $createform->interviewDate;
                        $newmsheet->date_endquarantine = Carbon::parse($createform->interviewDate)->addDays(13)->format('Y-m-d');
                        $newmsheet->magicURL = $majik;

                        $newmsheet->save();
                    }
                    
                    $upd = PaSwabDetails::where('id', $data->id)->update([
                        'status' => 'approved'
                    ]);

                    if($data->isNewRecord == 0 && $oldform->caseClassification != 'Confirmed' && $oldform->caseClassification != 'Non-COVID-19 Case') {
                        $fcheck = Forms::where('id', $oldform->id)->delete();
                    }
                }
                else {
                    //error code here, dapat hindi gagamit ng 'return' para di huminto yung loop
                }
            }

            return redirect()->action([PaSwabController::class, 'view'])
            ->with('msg', 'Bulk Approval of the selected data has been proccessed successfully.')
            ->with('msgtype', 'success');
        }
        else if($request->submit == 'bulkReject') {
            $request->validate([
                'rejectReason' => 'required',
            ]);

            $list = PaSwabDetails::whereIn('id', $request->bulkIDList)->where('status', 'pending')->get();

            foreach($list as $data) {
                if($data->status == 'pending') {
                    //Test Type final validator forAntigen
                    if($data->forAntigen == 1) {
                        $ttype = 'ANTIGEN';
                        //dapat baguhin 'to ayon sa system settings
                        $tOtherRemarks = 'FOR ANTIGEN REJECTED IN PASWAB';
                        $tAntigenKit = 'FOR ANTIGEN REJECTED IN PASWAB';
                    }
                    else {
                        $ttype = 'REJECTED IN PASWAB';
                        $tOtherRemarks = NULL;
                        $tAntigenKit = NULL;
                    }

                    if($data->isNewRecord == 1) {
                        $rec = $request->user()->records()->create([
                            'status' => 'approved',
                            'lname' => mb_strtoupper($data->lname),
                            'fname' => mb_strtoupper($data->fname),
                            'mname' => (!is_null($data->mname)) ? mb_strtoupper($data->mname) : null,
                            'gender' => strtoupper($data->gender),
                            'isPregnant' => $data->isPregnant,
                            'cs' => strtoupper($data->cs),
                            'nationality' => strtoupper($data->nationality),
                            'bdate' => $data->bdate,
                            'mobile' => $data->mobile,
                            'phoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                            'email' => $data->email,
                            'philhealth' => $data->philhealth,
                            'address_houseno' => strtoupper($data->address_houseno),
                            'address_street' => strtoupper($data->address_street),
                            'address_brgy' => strtoupper($data->address_brgy),
                            'address_city' => strtoupper($data->address_city),
                            'address_cityjson' => $data->address_cityjson,
                            'address_province' => strtoupper($data->address_province),
                            'address_provincejson' => $data->address_provincejson,
            
                            'permaaddressDifferent' => 0,
                            'permaaddress_houseno' => strtoupper($data->address_houseno),
                            'permaaddress_street' => strtoupper($data->address_street),
                            'permaaddress_brgy' => strtoupper($data->address_brgy),
                            'permaaddress_city' => strtoupper($data->address_city),
                            'permaaddress_cityjson' => $data->address_cityjson,
                            'permaaddress_province' => strtoupper($data->address_province),
                            'permaaddress_provincejson' => $data->address_provincejson,
                            'permamobile' => $data->mobile,
                            'permaphoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                            'permaemail' => $data->email,
            
                            'hasOccupation' => (!is_null($data->occupation)) ? 1 : 0,
                            'occupation' => $data->occupation,
                            'worksInClosedSetting' => $data->worksInClosedSetting,
                            'occupation_lotbldg' => $data->occupation_lotbldg,
                            'occupation_street' => $data->occupation_street,
                            'occupation_brgy' => $data->occupation_brgy,
                            'occupation_city' => $data->occupation_city,
                            'occupation_cityjson' => $data->occupation_cityjson,
                            'occupation_province' => $data->occupation_province,
                            'occupation_provincejson' => $data->occupation_provincejson,
                            'occupation_name' => $data->occupation_name,
                            'occupation_mobile' => $data->occupation_mobile,
                            'occupation_email' => $data->occupation_email,
            
                            'natureOfWork' => (!is_null($data->occupation)) ? mb_strtoupper($data->natureOfWork) : NULL,
                            'natureOfWorkIfOthers' => (!is_null($data->occupation) && $data->natureOfWork == 'OTHERS') ? mb_strtoupper($data->natureOfWorkIfOthers) : NULL,

                            'vaccinationDate1' => $data->vaccinationDate1,
                            'vaccinationName1' => $data->vaccinationName1,
                            'vaccinationNoOfDose1' => $data->vaccinationNoOfDose1,
                            'vaccinationFacility1' => $data->vaccinationFacility1,
                            'vaccinationRegion1' => $data->vaccinationRegion1,
                            'haveAdverseEvents1' => $data->haveAdverseEvents1,

                            'vaccinationDate2' => $data->vaccinationDate2,
                            'vaccinationName2' => $data->vaccinationName2,
                            'vaccinationNoOfDose2' => $data->vaccinationNoOfDose2,
                            'vaccinationFacility2' => $data->vaccinationFacility2,
                            'vaccinationRegion2' => $data->vaccinationRegion2,
                            'haveAdverseEvents2' => $data->haveAdverseEvents2,
                        ]);
                    }
                    else {
                        $rec = Records::findOrFail($data->records_id);

                        $rec->update([
                            'status' => 'approved',
                            'isPregnant' => $data->isPregnant,
                            'cs' => strtoupper($data->cs),
                            'nationality' => strtoupper($data->nationality),
                            'mobile' => $data->mobile,
                            'phoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                            'email' => $data->email,
                            'philhealth' => $data->philhealth,
                            'address_houseno' => strtoupper($data->address_houseno),
                            'address_street' => strtoupper($data->address_street),
                            'address_brgy' => strtoupper($data->address_brgy),
                            'address_city' => strtoupper($data->address_city),
                            'address_cityjson' => $data->address_cityjson,
                            'address_province' => strtoupper($data->address_province),
                            'address_provincejson' => $data->address_provincejson,
            
                            'permaaddressDifferent' => 0,
                            'permaaddress_houseno' => strtoupper($data->address_houseno),
                            'permaaddress_street' => strtoupper($data->address_street),
                            'permaaddress_brgy' => strtoupper($data->address_brgy),
                            'permaaddress_city' => strtoupper($data->address_city),
                            'permaaddress_cityjson' => $data->address_cityjson,
                            'permaaddress_province' => strtoupper($data->address_province),
                            'permaaddress_provincejson' => $data->address_provincejson,
                            'permamobile' => $data->mobile,
                            'permaphoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                            'permaemail' => $data->email,
            
                            'hasOccupation' => (!is_null($data->occupation)) ? 1 : 0,
                            'occupation' => $data->occupation,
                            'worksInClosedSetting' => $data->worksInClosedSetting,
                            'occupation_lotbldg' => $data->occupation_lotbldg,
                            'occupation_street' => $data->occupation_street,
                            'occupation_brgy' => $data->occupation_brgy,
                            'occupation_city' => $data->occupation_city,
                            'occupation_cityjson' => $data->occupation_cityjson,
                            'occupation_province' => $data->occupation_province,
                            'occupation_provincejson' => $data->occupation_provincejson,
                            'occupation_name' => $data->occupation_name,
                            'occupation_mobile' => $data->occupation_mobile,
                            'occupation_email' => $data->occupation_email,
            
                            'natureOfWork' => (!is_null($data->occupation)) ? mb_strtoupper($data->natureOfWork) : NULL,
                            'natureOfWorkIfOthers' => (!is_null($data->occupation) && $data->natureOfWork == 'OTHERS') ? mb_strtoupper($data->natureOfWorkIfOthers) : NULL,

                            'vaccinationDate1' => $data->vaccinationDate1,
                            'vaccinationName1' => $data->vaccinationName1,
                            'vaccinationNoOfDose1' => $data->vaccinationNoOfDose1,
                            'vaccinationFacility1' => $data->vaccinationFacility1,
                            'vaccinationRegion1' => $data->vaccinationRegion1,
                            'haveAdverseEvents1' => $data->haveAdverseEvents1,

                            'vaccinationDate2' => $data->vaccinationDate2,
                            'vaccinationName2' => $data->vaccinationName2,
                            'vaccinationNoOfDose2' => $data->vaccinationNoOfDose2,
                            'vaccinationFacility2' => $data->vaccinationFacility2,
                            'vaccinationRegion2' => $data->vaccinationRegion2,
                            'haveAdverseEvents2' => $data->haveAdverseEvents2,
                        ]);

                        $oldform = Forms::where('records_id', $rec->id)->orderBy('created_at', 'DESC')->first();
                    }
                    
                    //Auto Change Testing Category/Subgroup Base on the patient data
                    $testCat = [];
                    if(!is_null($data->SAS)) {
                        array_push($testCat, "C");
                    }
                    else {
                        array_push($testCat, "D.1");
                    }

                    if($data->getAgeInt() >= 60) {
                        array_push($testCat, "B");
                    }
                    if(!in_array("D.1", $testCat) && $data->pType == 'CLOSE CONTACT') {
                        array_push($testCat, "D.1");
                    }
                    if($data->isPregnant == 1 || $data->isForHospitalization == 1) {
                        array_push($testCat, "F");
                    }

                    //Auto Change Case Classification to Probable Based on Symptoms
                    if(!is_null($data->SAS)) {
                        if(in_array('Anosmia (Loss of Smell)', explode(",", $data->SAS)) || in_array('Ageusia (Loss of Taste)', explode(",", $data->SAS))) {
                            $caseClassi = 'Probable';
                        }
                        else {
                            $caseClassi = 'Suspect';
                        }
                    }
                    else {
                        $caseClassi = 'Suspect';
                    }

                    $request->user()->form()->create([
                        'morbidityMonth' => date('Y-m-d'),
                        'dateReported' => date('Y-m-d'),
                        'majikCode' => $data->majikCode,
                        'status' => 'paswab_rejected',
                        'isPresentOnSwabDay' => NULL,
                        'records_id' => $rec->id,
                        'drunit' => 'CHO GENERAL TRIAS',
                        'drregion' => '4A',
                        'drprovince' => 'CAVITE',
                        'interviewerName' => $data->getDefaultInterviewerName(),
                        'interviewerMobile' => '09190664324',
                        'interviewDate' => $data->interviewDate,
                        'informantName' => NULL,
                        'informantRelationship' => NULL,
                        'informantMobile' => NULL,
                        'existingCaseList' => '1',
                        'ecOthersRemarks' => NULL,
                        'pType' => ($data->pType == 'FOR TRAVEL') ? 'TESTING' : $data->pType,
                        'isForHospitalization' => $data->isForHospitalization,
                        'testingCat' => implode(",", $testCat),
                        'havePreviousCovidConsultation' => ($data->isNewRecord == 0) ? '1' : '0',
                        'dateOfFirstConsult' => ($data->isNewRecord == 0) ? ($oldform) ? $oldform->interviewDate : NULL : NULL,
                        'facilityNameOfFirstConsult' => ($data->isNewRecord == 0) ? ($oldform) ? $oldform->drunit : NULL : NULL,

                        'dispoType' => 3,
                        'dispoName' => NULL,
                        'dispoDate' => date('Y-m-d 08:00:00', strtotime($data->interviewDate)),
                        'healthStatus' => (!is_null($data->SAS)) ? 'Mild' : 'Asymptomatic',
                        'caseClassification' => $caseClassi,
                        'isHealthCareWorker' => '0',
                        'healthCareCompanyName' => NULL,
                        'healthCareCompanyLocation' => NULL,
                        'isOFW' => '0',
                        'OFWCountyOfOrigin' => NULL,
                        'ofwType' => NULL,
                        'isFNT' => '0',
                        'lsiType' => NULL,
                        'FNTCountryOfOrigin' => NULL,
                        'isLSI' => '0',
                        'LSICity' => NULL,
                        'LSIProvince' => NULL,
                        'isLivesOnClosedSettings' => '0',
                        'institutionType' => NULL,
                        'institutionName' => NULL,
                        'indgSpecify' => NULL,
                        'dateOnsetOfIllness' => $data->dateOnsetOfIllness,
                        'SAS' => $data->SAS,
                        'SASFeverDeg' => $data->SASFeverDeg,
                        'SASOtherRemarks' => $data->SASOtherRemarks,
                        'COMO' => $data->COMO,
                        'COMOOtherRemarks' => $data->COMOOtherRemarks,
                        'PregnantLMP' => $data->ifPregnantLMP,
                        'PregnantHighRisk' => ($data->isPregnant == 1) ? '1' : '0',
                        'diagWithSARI' => '0',
                        'imagingDoneDate' => $data->imagingDoneDate,
                        'imagingDone' => $data->imagingDone,
                        'imagingResult' => $data->imagingResult,
                        'imagingOtherFindings' => $data->imagingOtherFindings,

                        'testedPositiveUsingRTPCRBefore' => '0',
                        'testedPositiveNumOfSwab' => '0',
                        'testedPositiveLab' => NULL,
                        'testedPositiveSpecCollectedDate' => NULL,

                        'testDateCollected1' => '2121-01-01',
                        'oniTimeCollected1' => NULL,
                        'testDateReleased1' => NULL,
                        'testLaboratory1' => NULL,
                        'testType1' => $ttype,
                        'testTypeAntigenRemarks1' => ($ttype == "ANTIGEN") ? mb_strtoupper($tOtherRemarks) : NULL,
                        'antigenKit1' => ($ttype == "ANTIGEN") ? mb_strtoupper($tAntigenKit) : NULL,
                        'testTypeOtherRemarks1' => ($ttype == "OTHERS") ? mb_strtoupper($tOtherRemarks) : NULL,
                        'testResult1' => 'PENDING',
                        'testResultOtherRemarks1' => NULL,

                        'testDateCollected2' => NULL,
                        'oniTimeCollected2' => NULL,
                        'testDateReleased2' => NULL,
                        'testLaboratory2' => NULL,
                        'testType2' => NULL,
                        'testTypeAntigenRemarks2' => NULL,
                        'antigenKit2' => NULL,
                        'testTypeOtherRemarks2' => NULL,
                        'testResult2' => NULL,
                        'testResultOtherRemarks2' => NULL,

                        'outcomeCondition' => 'Active',
                        'outcomeRecovDate' => NULL,
                        'outcomeDeathDate' => NULL,
                        'deathImmeCause' => NULL,
                        'deathAnteCause' => NULL,
                        'deathUndeCause' => NULL,
                        'contriCondi' => NULL,

                        'expoitem1' => $data->expoitem1,
                        'expoDateLastCont' => $data->expoDateLastCont,

                        'expoitem2' => '0',
                        'intCountry' => NULL,
                        'intDateFrom' => NULL,
                        'intDateTo' => NULL,
                        'intWithOngoingCovid' => 'N/A',
                        'intVessel' => NULL,
                        'intVesselNo' => NULL,
                        'intDateDepart' => NULL,
                        'intDateArrive' => NULL,

                        'placevisited' => NULL,

                        'locName1' => NULL,
                        'locAddress1' => NULL,
                        'locDateFrom1' => NULL,
                        'locDateTo1' => NULL,
                        'locWithOngoingCovid1' => 'N/A',

                        'locName2' => NULL,
                        'locAddress2' => NULL,
                        'locDateFrom2' => NULL,
                        'locDateTo2' => NULL,
                        'locWithOngoingCovid2' => 'N/A',
                        
                        'locName3' => NULL,
                        'locAddress3' => NULL,
                        'locDateFrom3' => NULL,
                        'locDateTo3' => NULL,
                        'locWithOngoingCovid3' => 'N/A',
                        
                        'locName4' => NULL,
                        'locAddress4' => NULL,
                        'locDateFrom4' => NULL,
                        'locDateTo4' => NULL,
                        'locWithOngoingCovid4' => 'N/A',

                        'locName5' => NULL,
                        'locAddress5' => NULL,
                        'locDateFrom5' => NULL,
                        'locDateTo5' => NULL,
                        'locWithOngoingCovid5' => 'N/A',

                        'locName6' => NULL,
                        'locAddress6' => NULL,
                        'locDateFrom6' => NULL,
                        'locDateTo6' => NULL,
                        'locWithOngoingCovid6' => 'N/A',

                        'locName7' => NULL,
                        'locAddress7' => NULL,
                        'locDateFrom7' => NULL,
                        'locDateTo7' => NULL,
                        'locWithOngoingCovid7' => 'N/A',

                        'localVessel1' => NULL,
                        'localVesselNo1' => NULL,
                        'localOrigin1' => NULL,
                        'localDateDepart1' => NULL,
                        'localDest1' => NULL,
                        'localDateArrive1' => NULL,

                        'localVessel2' => NULL,
                        'localVesselNo2' => NULL,
                        'localOrigin2' => NULL,
                        'localDateDepart2' => NULL,
                        'localDest2' => NULL,
                        'localDateArrive2' => NULL,

                        'contact1Name' => $data->contact1Name,
                        'contact1No' => $data->contact1No,
                        'contact2Name' => $data->contact2Name,
                        'contact2No' => $data->contact2No,
                        'contact3Name' => $data->contact3Name,
                        'contact3No' => $data->contact3No,
                        'contact4Name' => $data->contact4Name,
                        'contact4No' => $data->contact4No,

                        'remarks' => $data->patientmsg,
                    ]);
                    
                    //UPDATE PARAMETERS
                    $upd = PaSwabDetails::find($data->id);

                    $upd->status = 'rejected';
                    $upd->remarks = $request->rejectReason;
                    $upd->processedAt = date('Y-m-d');

                    $upd->save();

                    if($data->isNewRecord == 0 && $oldform->caseClassification != 'Confirmed' && $oldform->caseClassification != 'Non-COVID-19 Case') {
                        //hayaan munang naka-disable para di mag-overlap sa existing data
                        
                        //$fcheck = Forms::where('id', $oldform->id)->delete();
                    }
                }
            }

            return redirect()->action([PaSwabController::class, 'view'])
            ->with('msg', 'Bulk Rejection of the selected data has been proccessed successfully.')
            ->with('msgtype', 'success');
        }
    }

    public function store(PaSwabValidationRequest $request, $locale) {
        $request->validated();

        /*
        
        Old Pa-swab duplicate Entry checker

        if(PaSwabDetails::where('lname', mb_strtoupper($request->lname))
		->where('fname', mb_strtoupper($request->fname))
		->where(function ($query) use ($request) {
			$query->where('mname', mb_strtoupper($request->mname))
			->orWhereNull('mname');
		})->where('status', 'pending')
		->exists()) {
			$param2 = 1;
		}
		else {
			$param2 = 0;
		}
        */
        $c1 = PaSwabDetails::ifEntryPending($request->lname, $request->fname, $request->mname);

        if(!is_null($c1)) {
            return back()
			->withInput()
			->with('msg', 'Your pa-swab request is still pending. Please wait for the approval from CESU Staff/Encoders before sending another pa-swab request.')
            ->with('msgtype', 'warning')
            ->with('skipmodal', true);
        }
        else {
            /*

            Old Pa-swab duplicate Entry checker

            if(PaSwabDetails::where('lname', mb_strtoupper($request->lname))
            ->where('fname', mb_strtoupper($request->fname))
            ->where(function ($query) use ($request) {
                $query->where('mname', mb_strtoupper($request->mname))
                ->orWhereNull('mname');
            })->whereIn('status', ['pending', 'approved'])
            ->whereDate('created_at', date('Y-m-d'))
            ->exists()) {
                $param3 = 1;
            }
            else {
                $param3 = 0;
            }
            */

            $c2 = PaSwabDetails::ifHaveEntryToday($request->lname, $request->fname, $request->mname);

            if(!is_null($c2)) {
                return back()
                ->withInput()
                ->with('msg', 'You can only send one pa-swab request per day. Please wait for your request to be approved by CESU Staff/Encoders.')
                ->with('msgtype', 'warning')
                ->with('skipmodal', true);
            }
            else {
                $foundunique = false;

                while(!$foundunique) {
                    $majik = strtoupper(Str::random(6));
                    
                    $search = PaSwabDetails::where('majikCode', $majik);
                    if($search->count() == 0) {
                        $foundunique = true;
                    }
                }

                /*
                if($request->filled('philhealth')) {
                    if (strpos($request->philhealth, '-') !== false && substr($request->philhealth, -2, 1) == "-" && substr($request->philhealth, -12, 1) == "-") {
                        $philhealth_organized = $request->philhealth;
                    }
                    else {
                        $philhealth_organized = str_replace('-','', $request->philhealth);
                        $philhealth_organized = substr($philhealth_organized, 0, 2)."-".substr($philhealth_organized,2,9)."-".substr($philhealth_organized,11,1);
                    }
                }
                else {
                    $philhealth_organized = null;
                }
                */

                $check = PaSwabLinks::where('code', mb_strtoupper($request->linkcode))
                ->where('secondary_code', mb_strtoupper($request->linkcode2nd))
                ->where('active', 1)
                ->first();

                if($check) {
                    $finalproceed = 1;
                }
                else {
                    $finalproceed = 0;
                }

                if($finalproceed == 1) {
                    $checku = Records::ifDuplicateFound($request->lname, $request->fname, $request->mname, $request->bdate);
                    /*
                    Old Pa-swab duplicate Entry checker

                    $checku = Records::where('lname', mb_strtoupper($request->lname))
                    ->where('fname', mb_strtoupper($request->fname))
                    ->where(function ($query) use ($request) {
                        $query->where('mname', mb_strtoupper($request->mname))
                        ->orWhereNull('mname');
                    })->first();
                    */

                    if(!is_null($checku)) {
                        $ifScheduledToday = Forms::where('records_id', $checku->id)
                        ->where(function ($query) {
                            $query->whereDate('testDateCollected1', '>=', date('Y-m-d'))
                            ->orWhere('testDateCollected2', '>=', date('Y-m-d'));
                        })->first();
                    }
                    else {
                        $ifScheduledToday = false;
                    }

                    if($ifScheduledToday) {
                        return back()
                        ->withInput()
                        ->with('msg', 'Error: You are not allowed to submit another pa-swab request because you currently have an Approved Schedule. Please see and use your Schedule Code for more details.')
                        ->with('msgtype', 'danger')
                        ->with('skipmodal', true);
                    }
                    else {
                        if($check->enableLockAddress == 1) {
                            $brgy = mb_strtoupper($check->lock_brgy);
                            $city = mb_strtoupper($check->lock_city_text);
                            $city_json = $check->lock_city;
                            $province = mb_strtoupper($check->lock_province_text);
                            $province_json = $check->lock_province;
                        }
                        else {
                            $brgy = mb_strtoupper($request->address_brgy);
                            $city = mb_strtoupper($request->address_city);
                            $city_json = $request->saddress_city;
                            $province = mb_strtoupper($request->address_province);
                            $province_json = $request->saddress_province;
                        }

                        $data = PaSwabDetails::create([
                            'isNewRecord' => (!is_null($checku)) ? 0 : 1,
                            'records_id' => (!is_null($checku)) ? $checku->id : NULL,
                            'majikCode' => $majik,
                            'pType' => $request->pType,
                            'linkCode' => $request->linkcode,
                            'isForHospitalization' => $request->isForHospitalization,
                            'interviewDate' => $request->interviewDate,
                            'forAntigen' => $request->forAntigen,
                            'lname' => mb_strtoupper($request->lname),
                            'fname' => mb_strtoupper($request->fname),
                            'mname' => ($request->filled('mname') && mb_strtoupper($request->mname) != "N/A") ? mb_strtoupper($request->mname) : null,
                            'bdate' => $request->bdate,
                            'gender' => strtoupper($request->gender),
                            'isPregnant' => ($request->gender == 'FEMALE') ? $request->isPregnant : 0,
                            'ifPregnantLMP' => ($request->gender == 'FEMALE' && $request->isPregnant == 1) ? $request->lmp : NULL,
                            'cs' => strtoupper($request->cs),
                            'nationality' => strtoupper($request->nationality),
                            'mobile' => $request->mobile,
                            'phoneno' => ($request->filled('phoneno')) ? $request->phoneno : NULL,
                            'email' => $request->email,
                            'philhealth' => $request->philhealth,
                            'address_houseno' => strtoupper($request->address_houseno),
                            'address_street' => strtoupper($request->address_street),

                            'address_brgy' => $brgy,
                            'address_city' => $city,
                            'address_cityjson' => $city_json,
                            'address_province' => $province,
                            'address_provincejson' => $province_json,
    
                            'vaccinationDate1' => ($request->vaccineq1 == 1) ? $request->vaccinationDate1 : NULL,
                            'vaccinationName1'=> ($request->vaccineq1 == 1) ? $request->nameOfVaccine : NULL,
                            'vaccinationNoOfDose1'=> ($request->vaccineq1 == 1) ? 1 : NULL,
                            'vaccinationFacility1'=> ($request->vaccineq1 == 1) ? $request->vaccinationFacility1 : NULL,
                            'vaccinationRegion1'=> ($request->vaccineq1 == 1) ? $request->vaccinationRegion1 : NULL,
                            'haveAdverseEvents1'=> ($request->vaccineq1 == 1) ? $request->haveAdverseEvents1 : NULL,
    
                            'vaccinationDate2' => ($request->vaccineq1 == 1 && $request->howManyDose == 2) ? $request->vaccinationDate2 : NULL,
                            'vaccinationName2' => ($request->vaccineq1 == 1 && $request->howManyDose == 2) ? $request->nameOfVaccine : NULL,
                            'vaccinationNoOfDose2' => ($request->vaccineq1 == 1 && $request->howManyDose == 2) ? 2 : NULL,
                            'vaccinationFacility2' => ($request->vaccineq1 == 1 && $request->howManyDose == 2) ? $request->vaccinationFacility2 : NULL,
                            'vaccinationRegion2' => ($request->vaccineq1 == 1 && $request->howManyDose == 2) ? $request->vaccinationRegion2 : NULL,
                            'haveAdverseEvents2' => ($request->vaccineq1 == 1 && $request->howManyDose == 2) ? $request->haveAdverseEvents2 : NULL,
            
                            'occupation' => ($request->haveOccupation == 1) ? mb_strtoupper($request->occupation) : NULL,
                            'occupation_name' => ($request->filled('occupation_name')) ? mb_strtoupper($request->occupation_name) : NULL,
                            'natureOfWork' => ($request->haveOccupation == 1) ? mb_strtoupper($request->natureOfWork) : NULL,
                            'natureOfWorkIfOthers' => ($request->haveOccupation == 1 && $request->natureOfWork == "OTHERS") ? mb_strtoupper($request->natureOfWorkIfOthers) : NULL,
                            'worksInClosedSetting' => ($request->haveOccupation == 1) ? $request->worksInClosedSetting : 'UNKNOWN',

                            'occupation_lotbldg' => ($request->haveOccupation == 1) ? mb_strtoupper($request->occupation_lotbldg) : NULL,
                            'occupation_street' => ($request->haveOccupation == 1) ? mb_strtoupper($request->occupation_street) : NULL,
                            'occupation_brgy' => ($request->haveOccupation == 1) ? mb_strtoupper($request->occupation_brgy) : NULL,
                            'occupation_city' => ($request->haveOccupation == 1) ? mb_strtoupper($request->occupation_city) : NULL,
                            'occupation_cityjson' => ($request->haveOccupation == 1) ? mb_strtoupper($request->soccupation_city) : NULL,
                            'occupation_province' => ($request->haveOccupation == 1) ? mb_strtoupper($request->occupation_province) : NULL,
                            'occupation_provincejson' => ($request->haveOccupation == 1) ? mb_strtoupper($request->soccupation_province) : NULL,
                            'occupation_mobile' => $request->occupation_mobile,
                            'occupation_email' => $request->occupation_email,
            
                            'dateOnsetOfIllness' => ($request->haveSymptoms == 1) ? $request->dateOnsetOfIllness : NULL,
                            'SAS' => ($request->haveSymptoms == 1 && !is_null($request->sasCheck)) ? implode(",", $request->sasCheck) : NULL,
                            'SASFeverDeg' => ($request->haveSymptoms == 1) ? $request->SASFeverDeg : NULL,
                            'SASOtherRemarks' => $request->SASOtherRemarks,
            
                            'COMO' => implode(",", $request->comCheck),
                            'COMOOtherRemarks' => $request->COMOOtherRemarks,
            
                            'imagingDoneDate' => $request->imagingDoneDate,
                            'imagingDone' => $request->imagingDone,
                            'imagingResult' => $request->imagingResult,
                            'imagingOtherFindings' => $request->imagingOtherFindings,
            
                            'expoitem1' => $request->expoitem1,
                            'expoDateLastCont' => $request->expoDateLastCont,
            
                            'contact1Name' => ($request->filled('contact1Name')) ? mb_strtoupper($request->contact1Name) : NULL,
                            'contact1No' => $request->contact1No,
                            'contact2Name' => ($request->filled('contact2Name')) ? mb_strtoupper($request->contact2Name) : NULL,
                            'contact2No' => $request->contact2No,
                            'contact3Name' => ($request->filled('contact3Name')) ? mb_strtoupper($request->contact3Name) : NULL,
                            'contact3No' => $request->contact3No,
                            'contact4Name' => ($request->filled('contact4Name')) ? mb_strtoupper($request->contact4Name) : NULL,
                            'contact4No' => $request->contact4No,
        
                            'patientmsg' => $request->patientmsg,
            
                            'senderIP' => request()->ip(),
                        ]);
                        
                        return redirect()->route('paswab.complete', ['locale' => $locale])
                        ->with('majik', $majik)
                        ->with('statustype', 'success')
                        ->with('fcode', mb_strtoupper($request->linkcode))
                        ->with('scode', mb_strtoupper($request->linkcode2nd));
                    }
                }
                else {
                    return back()
                    ->withInput()
                    ->with('msg', 'Error: Pa-swab Referal Link Code is invalid or currently disabled. Please use proper Pa-Swab URL and then try again.')
                    ->with('msgtype', 'danger')
                    ->with('skipmodal', true);
                }
            }
        }
    }

    public function complete($locale) {
        if (! in_array($locale, ['en', 'fil'])) {
            abort(404);
        }

        App::setLocale($locale);

        return view('paswab_complete');
    }

    public function check (Request $request, $locale) {
        $request->validate([
            'scode' => 'required',
        ]);

        $check = PaSwabDetails::where('majikCode', strtoupper($request->scode))->first();

        if($check) {
            if($check->status == 'approved') {
                $form = Forms::where('majikCode', $check->majikCode)->first();

                return view('paswab_check', [
                    'data' => $check,
                    'form' => $form,
                ]);
            }
            else {
                return view('paswab_check', [
                    'data' => $check
                ]);
            }
        }
        else {
            return back()
			->withInput()
            ->with('openform', 'patient')
			->with('msg', 'Schedule Code does not exist. Please try again.')
            ->with('msgtype', 'danger');
        }
    }

    public function view() {
        if(request()->input('q')) {
            $list = PaSwabDetails::where(function ($query) {
                $query->where(DB::raw('CONCAT(lname," ",fname," ", mname)'), 'LIKE', "%".str_replace(',','',mb_strtoupper(request()->input('q')))."%")
                ->orWhere(DB::raw('CONCAT(lname," ",fname)'), 'LIKE', "%".str_replace(',','',mb_strtoupper(request()->input('q')))."%")
                ->orWhere('majikCode', 'LIKE', "%".mb_strtoupper(request()->input('q'))."%")
                ->orWhere('linkCode', 'LIKE', "%".mb_strtoupper(request()->input('q'))."%");
            })->where('status', 'pending');
		}
        else {
            $list = PaSwabDetails::where('status', 'pending');
        }

        if(auth()->user()->isBrgyAccount()) {
            $list = $list->where('address_province', auth()->user()->brgy->city->province->provinceName)
            ->where('address_city', auth()->user()->brgy->city->cityName)
            ->where('address_brgy', auth()->user()->brgy->brgyName);
        }

        $list = $list
        ->orderBy('created_at', 'asc')
        ->paginate(10);
        
        return view('paswab_view', ['list' => $list]);
    }

    public function viewspecific($id) {
        $data = PaSwabDetails::findOrFail($id);

        $interviewers = Interviewers::orderBy('lname', 'asc')->get();

        if($data->checkPaswabBrgyData()) {
            return view('paswab_view_specific', ['data' => $data, 'interviewers' => $interviewers]);
        }
        else {
            return abort(401);
        }
    }

    public function approve($id, Request $request) {
        $data = PaSwabDetails::findOrFail($id);

        if(!($data->checkPaswabBrgyData())) {
            return abort(401);
        }

        //Test Type final validator forAntigen
        if($data->forAntigen == 1) {
            $ttype = 'ANTIGEN';
        }
        else {
            $ttype = $request->testType1;
        }

        $request->validate([
            'interviewerName' => 'required',
            'testDateCollected1' => 'required|date|after_or_equal:today',
            'testType1' => 'required|in:OPS,NPS,OPS AND NPS,ANTIGEN,ANTIBODY,OTHERS',
            'testTypeOtherRemarks1' => ($ttype == "ANTIGEN" || $ttype == "OTHERS") ? 'required' : 'nullable',
            'antigenKit1' => ($ttype == "ANTIGEN") ? 'required' : 'nullable',
        ]);

        if($data->status == 'pending') {
            //create record data first
            if($data->isNewRecord == 1) {
                $rec = $request->user()->records()->create([
                    'status' => 'approved',
                    'lname' => mb_strtoupper($data->lname),
                    'fname' => mb_strtoupper($data->fname),
                    'mname' => (!is_null($data->mname)) ? mb_strtoupper($data->mname) : null,
                    'gender' => strtoupper($data->gender),
                    'isPregnant' => $data->isPregnant,
                    'cs' => strtoupper($data->cs),
                    'nationality' => strtoupper($data->nationality),
                    'bdate' => $data->bdate,
                    'mobile' => $data->mobile,
                    'phoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                    'email' => $data->email,
                    'philhealth' => $data->philhealth,
                    'address_houseno' => strtoupper($data->address_houseno),
                    'address_street' => strtoupper($data->address_street),
                    'address_brgy' => strtoupper($data->address_brgy),
                    'address_city' => strtoupper($data->address_city),
                    'address_cityjson' => $data->address_cityjson,
                    'address_province' => strtoupper($data->address_province),
                    'address_provincejson' => $data->address_provincejson,
    
                    'permaaddressDifferent' => 0,
                    'permaaddress_houseno' => strtoupper($data->address_houseno),
                    'permaaddress_street' => strtoupper($data->address_street),
                    'permaaddress_brgy' => strtoupper($data->address_brgy),
                    'permaaddress_city' => strtoupper($data->address_city),
                    'permaaddress_cityjson' => $data->address_cityjson,
                    'permaaddress_province' => strtoupper($data->address_province),
                    'permaaddress_provincejson' => $data->address_provincejson,
                    'permamobile' => $data->mobile,
                    'permaphoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                    'permaemail' => $data->email,
    
                    'hasOccupation' => (!is_null($data->occupation)) ? 1 : 0,
                    'occupation' => $data->occupation,
                    'worksInClosedSetting' => $data->worksInClosedSetting,
                    'occupation_lotbldg' => $data->occupation_lotbldg,
                    'occupation_street' => $data->occupation_street,
                    'occupation_brgy' => $data->occupation_brgy,
                    'occupation_city' => $data->occupation_city,
                    'occupation_cityjson' => $data->occupation_cityjson,
                    'occupation_province' => $data->occupation_province,
                    'occupation_provincejson' => $data->occupation_provincejson,
                    'occupation_name' => $data->occupation_name,
                    'occupation_mobile' => $data->occupation_mobile,
                    'occupation_email' => $data->occupation_email,
    
                    'natureOfWork' => (!is_null($data->occupation)) ? mb_strtoupper($data->natureOfWork) : NULL,
                    'natureOfWorkIfOthers' => (!is_null($data->occupation) && $data->natureOfWork == 'OTHERS') ? mb_strtoupper($data->natureOfWorkIfOthers) : NULL,

                    'vaccinationDate1' => $data->vaccinationDate1,
                    'vaccinationName1' => $data->vaccinationName1,
                    'vaccinationNoOfDose1' => $data->vaccinationNoOfDose1,
                    'vaccinationFacility1' => $data->vaccinationFacility1,
                    'vaccinationRegion1' => $data->vaccinationRegion1,
                    'haveAdverseEvents1' => $data->haveAdverseEvents1,

                    'vaccinationDate2' => $data->vaccinationDate2,
                    'vaccinationName2' => $data->vaccinationName2,
                    'vaccinationNoOfDose2' => $data->vaccinationNoOfDose2,
                    'vaccinationFacility2' => $data->vaccinationFacility2,
                    'vaccinationRegion2' => $data->vaccinationRegion2,
                    'haveAdverseEvents2' => $data->haveAdverseEvents2,
                ]);
            }
            else {
                $rec = Records::findOrFail($data->records_id);

                $rec->update([
                    'status' => 'approved',
                    'isPregnant' => $data->isPregnant,
                    'cs' => strtoupper($data->cs),
                    'nationality' => strtoupper($data->nationality),
                    'mobile' => $data->mobile,
                    'phoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                    'email' => $data->email,
                    'philhealth' => $data->philhealth,
                    'address_houseno' => strtoupper($data->address_houseno),
                    'address_street' => strtoupper($data->address_street),
                    'address_brgy' => strtoupper($data->address_brgy),
                    'address_city' => strtoupper($data->address_city),
                    'address_cityjson' => $data->address_cityjson,
                    'address_province' => strtoupper($data->address_province),
                    'address_provincejson' => $data->address_provincejson,
    
                    'permaaddressDifferent' => 0,
                    'permaaddress_houseno' => strtoupper($data->address_houseno),
                    'permaaddress_street' => strtoupper($data->address_street),
                    'permaaddress_brgy' => strtoupper($data->address_brgy),
                    'permaaddress_city' => strtoupper($data->address_city),
                    'permaaddress_cityjson' => $data->address_cityjson,
                    'permaaddress_province' => strtoupper($data->address_province),
                    'permaaddress_provincejson' => $data->address_provincejson,
                    'permamobile' => $data->mobile,
                    'permaphoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                    'permaemail' => $data->email,
    
                    'hasOccupation' => (!is_null($data->occupation)) ? 1 : 0,
                    'occupation' => $data->occupation,
                    'worksInClosedSetting' => $data->worksInClosedSetting,
                    'occupation_lotbldg' => $data->occupation_lotbldg,
                    'occupation_street' => $data->occupation_street,
                    'occupation_brgy' => $data->occupation_brgy,
                    'occupation_city' => $data->occupation_city,
                    'occupation_cityjson' => $data->occupation_cityjson,
                    'occupation_province' => $data->occupation_province,
                    'occupation_provincejson' => $data->occupation_provincejson,
                    'occupation_name' => $data->occupation_name,
                    'occupation_mobile' => $data->occupation_mobile,
                    'occupation_email' => $data->occupation_email,
    
                    'natureOfWork' => (!is_null($data->occupation)) ? mb_strtoupper($data->natureOfWork) : NULL,
                    'natureOfWorkIfOthers' => (!is_null($data->occupation) && $data->natureOfWork == 'OTHERS') ? mb_strtoupper($data->natureOfWorkIfOthers) : NULL,

                    'vaccinationDate1' => $data->vaccinationDate1,
                    'vaccinationName1' => $data->vaccinationName1,
                    'vaccinationNoOfDose1' => $data->vaccinationNoOfDose1,
                    'vaccinationFacility1' => $data->vaccinationFacility1,
                    'vaccinationRegion1' => $data->vaccinationRegion1,
                    'haveAdverseEvents1' => $data->haveAdverseEvents1,

                    'vaccinationDate2' => $data->vaccinationDate2,
                    'vaccinationName2' => $data->vaccinationName2,
                    'vaccinationNoOfDose2' => $data->vaccinationNoOfDose2,
                    'vaccinationFacility2' => $data->vaccinationFacility2,
                    'vaccinationRegion2' => $data->vaccinationRegion2,
                    'haveAdverseEvents2' => $data->haveAdverseEvents2,
                ]);

                $oldform = Forms::where('records_id', $rec->id)->first();
            }

            if(!is_null($rec->philhealth)) {
                if($ttype == "OPS" || $ttype == "NPS" || $ttype == "OPS AND NPS") {
                    $trigger = 0;
                    $addMinutes = 0;

                    while ($trigger != 1) {
                        $oniStartTime = date('H:i:s', strtotime('14:00:00 + '. $addMinutes .' minutes'));

                        $query = Forms::with('records')
                        ->where('testDateCollected1', $request->testDateCollected1)
                        ->whereIn('testType1', ['OPS', 'NPS', 'OPS AND NPS'])
                        ->whereHas('records', function ($q) {
                            $q->whereNotNull('philhealth');
                        })
                        ->where('oniTimeCollected1', $oniStartTime)->get();

                        if($query->count()) {
                            if($query->count() < 5) {
                                $oniTimeFinal = $oniStartTime;
                                $trigger = 1;
                            }
                            else {
                                $addMinutes = $addMinutes + 5;
                            }
                        }
                        else {
                            $oniTimeFinal = $oniStartTime;
                            $trigger = 1;
                        }
                    }
                }
                else {
                    $oniTimeFinal = NULL;
                }
            }
            else {
                $oniTimeFinal = NULL;
            }

            //Auto Change Testing Category/Subgroup Base on the patient data
            $testCat = [];
            if(!is_null($data->SAS)) {
                array_push($testCat, "C");
            }
            else {
                array_push($testCat, "D.1");
            }

            if($data->getAgeInt() >= 60) {
                array_push($testCat, "B");
            }
            if(!in_array("D.1", $testCat) && $data->pType == 'CLOSE CONTACT') {
                array_push($testCat, "D.1");
            }
            if($data->isPregnant == 1 || $data->isForHospitalization == 1) {
                array_push($testCat, "F");
            }

            //Auto Change Case Classification to Probable Based on Symptoms
            if(!is_null($data->SAS)) {
                if(in_array('Anosmia (Loss of Smell)', explode(",", $data->SAS)) || in_array('Ageusia (Loss of Taste)', explode(",", $data->SAS))) {
                    $caseClassi = 'Probable';
                }
                else {
                    $caseClassi = 'Suspect';
                }
            }
            else {
                $caseClassi = 'Suspect';
            }

            $createform = $request->user()->form()->create([
                'morbidityMonth' => date('Y-m-d'),
                'dateReported' => date('Y-m-d'),
                'majikCode' => $data->majikCode,
                'status' => 'approved',
                'isPresentOnSwabDay' => NULL,
                'records_id' => $rec->id,
                'drunit' => 'CHO GENERAL TRIAS',
                'drregion' => '4A',
                'drprovince' => 'CAVITE',
                'interviewerName' => $request->interviewerName,
                'interviewerMobile' => '09190664324',
                'interviewDate' => $data->interviewDate,
                'informantName' => NULL,
                'informantRelationship' => NULL,
                'informantMobile' => NULL,
                'existingCaseList' => '1',
                'ecOthersRemarks' => NULL,
                'pType' => ($data->pType == 'FOR TRAVEL') ? 'TESTING' : $data->pType,
                'isForHospitalization' => $data->isForHospitalization,
                'testingCat' => implode(",", $testCat),
                'havePreviousCovidConsultation' => ($data->isNewRecord == 0) ? '1' : '0',
                'dateOfFirstConsult' => ($data->isNewRecord == 0) ? ($oldform) ? $oldform->interviewDate : NULL : NULL,
                'facilityNameOfFirstConsult' => ($data->isNewRecord == 0) ? ($oldform) ? $oldform->drunit : NULL : NULL,

                'dispoType' => 3,
                'dispoName' => NULL,
                'dispoDate' => date('Y-m-d 08:00:00', strtotime($data->interviewDate)),
                'healthStatus' => (!is_null($data->SAS)) ? 'Mild' : 'Asymptomatic',
                'caseClassification' => $caseClassi,
                'healthCareCompanyName' => NULL,
                'healthCareCompanyLocation' => NULL,
                'isOFW' => '0',
                'OFWCountyOfOrigin' => NULL,
                'ofwType' => NULL,
                'isFNT' => '0',
                'lsiType' => NULL,
                'FNTCountryOfOrigin' => NULL,
                'isLSI' => '0',
                'LSICity' => NULL,
                'LSIProvince' => NULL,
                'isLivesOnClosedSettings' => '0',
                'institutionType' => NULL,
                'institutionName' => NULL,
                'indgSpecify' => NULL,
                'dateOnsetOfIllness' => $data->dateOnsetOfIllness,
                'SAS' => $data->SAS,
                'SASFeverDeg' => $data->SASFeverDeg,
                'SASOtherRemarks' => $data->SASOtherRemarks,
                'COMO' => $data->COMO,
                'COMOOtherRemarks' => $data->COMOOtherRemarks,
                'PregnantLMP' => $data->ifPregnantLMP,
                'PregnantHighRisk' => ($data->isPregnant == 1) ? '1' : '0',
                'diagWithSARI' => '0',
                'imagingDoneDate' => $data->imagingDoneDate,
                'imagingDone' => $data->imagingDone,
                'imagingResult' => $data->imagingResult,
                'imagingOtherFindings' => $data->imagingOtherFindings,

                'testedPositiveUsingRTPCRBefore' => '0',
                'testedPositiveNumOfSwab' => '0',
                'testedPositiveLab' => NULL,
                'testedPositiveSpecCollectedDate' => NULL,

                'testDateCollected1' => $request->testDateCollected1,
                'oniTimeCollected1' => $oniTimeFinal,
                'testDateReleased1' => NULL,
                'testLaboratory1' => NULL,
                'testType1' => $ttype,
                'testTypeAntigenRemarks1' => ($ttype == "ANTIGEN") ? mb_strtoupper($request->testTypeOtherRemarks1) : NULL,
                'antigenKit1' => ($ttype == "ANTIGEN") ? mb_strtoupper($request->antigenKit1) : NULL,
                'testTypeOtherRemarks1' => ($ttype == "OTHERS") ? mb_strtoupper($request->testTypeOtherRemarks1) : NULL,
                'testResult1' => 'PENDING',
                'testResultOtherRemarks1' => NULL,

                'testDateCollected2' => NULL,
                'oniTimeCollected2' => NULL,
                'testDateReleased2' => NULL,
                'testLaboratory2' => NULL,
                'testType2' => NULL,
                'testTypeAntigenRemarks2' => NULL,
                'antigenKit2' => NULL,
                'testTypeOtherRemarks2' => NULL,
                'testResult2' => NULL,
                'testResultOtherRemarks2' => NULL,

                'outcomeCondition' => 'Active',
                'outcomeRecovDate' => NULL,
                'outcomeDeathDate' => NULL,
                'deathImmeCause' => NULL,
                'deathAnteCause' => NULL,
                'deathUndeCause' => NULL,
                'contriCondi' => NULL,

                'expoitem1' => $data->expoitem1,
                'expoDateLastCont' => $data->expoDateLastCont,

                'expoitem2' => '0',
                'intCountry' => NULL,
                'intDateFrom' => NULL,
                'intDateTo' => NULL,
                'intWithOngoingCovid' => 'N/A',
                'intVessel' => NULL,
                'intVesselNo' => NULL,
                'intDateDepart' => NULL,
                'intDateArrive' => NULL,

                'placevisited' => NULL,

                'locName1' => NULL,
                'locAddress1' => NULL,
                'locDateFrom1' => NULL,
                'locDateTo1' => NULL,
                'locWithOngoingCovid1' => 'N/A',

                'locName2' => NULL,
                'locAddress2' => NULL,
                'locDateFrom2' => NULL,
                'locDateTo2' => NULL,
                'locWithOngoingCovid2' => 'N/A',
                
                'locName3' => NULL,
                'locAddress3' => NULL,
                'locDateFrom3' => NULL,
                'locDateTo3' => NULL,
                'locWithOngoingCovid3' => 'N/A',
                
                'locName4' => NULL,
                'locAddress4' => NULL,
                'locDateFrom4' => NULL,
                'locDateTo4' => NULL,
                'locWithOngoingCovid4' => 'N/A',

                'locName5' => NULL,
                'locAddress5' => NULL,
                'locDateFrom5' => NULL,
                'locDateTo5' => NULL,
                'locWithOngoingCovid5' => 'N/A',

                'locName6' => NULL,
                'locAddress6' => NULL,
                'locDateFrom6' => NULL,
                'locDateTo6' => NULL,
                'locWithOngoingCovid6' => 'N/A',

                'locName7' => NULL,
                'locAddress7' => NULL,
                'locDateFrom7' => NULL,
                'locDateTo7' => NULL,
                'locWithOngoingCovid7' => 'N/A',

                'localVessel1' => NULL,
                'localVesselNo1' => NULL,
                'localOrigin1' => NULL,
                'localDateDepart1' => NULL,
                'localDest1' => NULL,
                'localDateArrive1' => NULL,

                'localVessel2' => NULL,
                'localVesselNo2' => NULL,
                'localOrigin2' => NULL,
                'localDateDepart2' => NULL,
                'localDest2' => NULL,
                'localDateArrive2' => NULL,

                'contact1Name' => $data->contact1Name,
                'contact1No' => $data->contact1No,
                'contact2Name' => $data->contact2Name,
                'contact2No' => $data->contact2No,
                'contact3Name' => $data->contact3Name,
                'contact3No' => $data->contact3No,
                'contact4Name' => $data->contact4Name,
                'contact4No' => $data->contact4No,

                'remarks' => $data->patientmsg,
            ]);

            //Create Monitoring Sheet
            $msheet_search = MonitoringSheetMaster::where('forms_id', $createform->id)->first();
            if(!$msheet_search) {
                $foundunique = false;
                while(!$foundunique) {
                    $majik = Str::random(30);
                    
                    $search = MonitoringSheetMaster::where('magicURL', $majik);
                    if($search->count() == 0) {
                        $foundunique = true;
                    }
                }

                $newmsheet = new MonitoringSheetMaster;

                $newmsheet->forms_id = $createform->id;
                $newmsheet->region = '4A';
                $newmsheet->date_lastexposure = (!is_null($createform->expoDateLastCont)) ? $createform->expoDateLastCont : $createform->interviewDate;
                $newmsheet->date_startquarantine = $createform->interviewDate;
                $newmsheet->date_endquarantine = Carbon::parse($createform->interviewDate)->addDays(13)->format('Y-m-d');
                $newmsheet->magicURL = $majik;

                $newmsheet->save();
            }
            
            $upd = PaSwabDetails::where('id', $id)->update([
                'status' => 'approved'
            ]);

            if($data->isNewRecord == 0 && $oldform->caseClassification != 'Confirmed' && $oldform->caseClassification != 'Non-COVID-19 Case') {
                $fcheck = Forms::where('id', $oldform->id)->delete();
            }
            
            return redirect()->action([PaSwabController::class, 'view'])
            ->with('msg', 'Swab Schedule for '.$data->getName()." has been approved successfully.")
            ->with('msgtype', 'success');
        }
        else {
            return redirect()->action([PaSwabController::class, 'view'])
            ->with('msg', 'You are not allowed to do that.')
            ->with('msgtype', 'warning');
        }
    }

    public function reject($id, Request $request) {
        /*
        $data = PaSwabDetails::findOrFail($id);

        $request->validate([
            'reason' => 'required',
        ]);

        if($data->status == 'pending') {
            $upd = PaSwabDetails::where('id', $id)->update([
                'status' => 'rejected',
                'remarks' => $request->reason,
                'processedAt' => date('Y-m-d'),
            ]);

            return redirect()->action([PaSwabController::class, 'view'])
            ->with('msg', 'Swab Schedule for '.$data->getName()." has been rejected successfully.")
            ->with('msgtype', 'success');
        }
        else {
            return redirect()->action([PaSwabController::class, 'view'])
            ->with('msg', 'You are not allowed to do that.')
            ->with('msgtype', 'warning');
        }
        */
        $request->validate([
            'rejectReason' => 'required',
        ]);

        $data = PaSwabDetails::where('id', $id)->where('status', 'pending')->get();

        if(!($data->checkPaswabBrgyData())) {
            return abort(401);
        }

        if($data->status == 'pending') {
            //Test Type final validator forAntigen
            if($data->forAntigen == 1) {
                $ttype = 'ANTIGEN';
                //dapat baguhin 'to ayon sa system settings
                $tOtherRemarks = 'FOR ANTIGEN REJECTED IN PASWAB';
                $tAntigenKit = 'FOR ANTIGEN REJECTED IN PASWAB';
            }
            else {
                $ttype = 'REJECTED IN PASWAB';
                $tOtherRemarks = NULL;
                $tAntigenKit = NULL;
            }

            if($data->isNewRecord == 1) {
                $rec = $request->user()->records()->create([
                    'status' => 'approved',
                    'lname' => mb_strtoupper($data->lname),
                    'fname' => mb_strtoupper($data->fname),
                    'mname' => (!is_null($data->mname)) ? mb_strtoupper($data->mname) : null,
                    'gender' => strtoupper($data->gender),
                    'isPregnant' => $data->isPregnant,
                    'cs' => strtoupper($data->cs),
                    'nationality' => strtoupper($data->nationality),
                    'bdate' => $data->bdate,
                    'mobile' => $data->mobile,
                    'phoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                    'email' => $data->email,
                    'philhealth' => $data->philhealth,
                    'address_houseno' => strtoupper($data->address_houseno),
                    'address_street' => strtoupper($data->address_street),
                    'address_brgy' => strtoupper($data->address_brgy),
                    'address_city' => strtoupper($data->address_city),
                    'address_cityjson' => $data->address_cityjson,
                    'address_province' => strtoupper($data->address_province),
                    'address_provincejson' => $data->address_provincejson,
    
                    'permaaddressDifferent' => 0,
                    'permaaddress_houseno' => strtoupper($data->address_houseno),
                    'permaaddress_street' => strtoupper($data->address_street),
                    'permaaddress_brgy' => strtoupper($data->address_brgy),
                    'permaaddress_city' => strtoupper($data->address_city),
                    'permaaddress_cityjson' => $data->address_cityjson,
                    'permaaddress_province' => strtoupper($data->address_province),
                    'permaaddress_provincejson' => $data->address_provincejson,
                    'permamobile' => $data->mobile,
                    'permaphoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                    'permaemail' => $data->email,
    
                    'hasOccupation' => (!is_null($data->occupation)) ? 1 : 0,
                    'occupation' => $data->occupation,
                    'worksInClosedSetting' => $data->worksInClosedSetting,
                    'occupation_lotbldg' => $data->occupation_lotbldg,
                    'occupation_street' => $data->occupation_street,
                    'occupation_brgy' => $data->occupation_brgy,
                    'occupation_city' => $data->occupation_city,
                    'occupation_cityjson' => $data->occupation_cityjson,
                    'occupation_province' => $data->occupation_province,
                    'occupation_provincejson' => $data->occupation_provincejson,
                    'occupation_name' => $data->occupation_name,
                    'occupation_mobile' => $data->occupation_mobile,
                    'occupation_email' => $data->occupation_email,
    
                    'natureOfWork' => (!is_null($data->occupation)) ? mb_strtoupper($data->natureOfWork) : NULL,
                    'natureOfWorkIfOthers' => (!is_null($data->occupation) && $data->natureOfWork == 'OTHERS') ? mb_strtoupper($data->natureOfWorkIfOthers) : NULL,

                    'vaccinationDate1' => $data->vaccinationDate1,
                    'vaccinationName1' => $data->vaccinationName1,
                    'vaccinationNoOfDose1' => $data->vaccinationNoOfDose1,
                    'vaccinationFacility1' => $data->vaccinationFacility1,
                    'vaccinationRegion1' => $data->vaccinationRegion1,
                    'haveAdverseEvents1' => $data->haveAdverseEvents1,

                    'vaccinationDate2' => $data->vaccinationDate2,
                    'vaccinationName2' => $data->vaccinationName2,
                    'vaccinationNoOfDose2' => $data->vaccinationNoOfDose2,
                    'vaccinationFacility2' => $data->vaccinationFacility2,
                    'vaccinationRegion2' => $data->vaccinationRegion2,
                    'haveAdverseEvents2' => $data->haveAdverseEvents2,
                ]);
            }
            else {
                $rec = Records::findOrFail($data->records_id);

                $rec->update([
                    'status' => 'approved',
                    'isPregnant' => $data->isPregnant,
                    'cs' => strtoupper($data->cs),
                    'nationality' => strtoupper($data->nationality),
                    'mobile' => $data->mobile,
                    'phoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                    'email' => $data->email,
                    'philhealth' => $data->philhealth,
                    'address_houseno' => strtoupper($data->address_houseno),
                    'address_street' => strtoupper($data->address_street),
                    'address_brgy' => strtoupper($data->address_brgy),
                    'address_city' => strtoupper($data->address_city),
                    'address_cityjson' => $data->address_cityjson,
                    'address_province' => strtoupper($data->address_province),
                    'address_provincejson' => $data->address_provincejson,
    
                    'permaaddressDifferent' => 0,
                    'permaaddress_houseno' => strtoupper($data->address_houseno),
                    'permaaddress_street' => strtoupper($data->address_street),
                    'permaaddress_brgy' => strtoupper($data->address_brgy),
                    'permaaddress_city' => strtoupper($data->address_city),
                    'permaaddress_cityjson' => $data->address_cityjson,
                    'permaaddress_province' => strtoupper($data->address_province),
                    'permaaddress_provincejson' => $data->address_provincejson,
                    'permamobile' => $data->mobile,
                    'permaphoneno' => (!is_null($data->phoneno)) ? $data->phoneno : NULL,
                    'permaemail' => $data->email,
    
                    'hasOccupation' => (!is_null($data->occupation)) ? 1 : 0,
                    'occupation' => $data->occupation,
                    'worksInClosedSetting' => $data->worksInClosedSetting,
                    'occupation_lotbldg' => $data->occupation_lotbldg,
                    'occupation_street' => $data->occupation_street,
                    'occupation_brgy' => $data->occupation_brgy,
                    'occupation_city' => $data->occupation_city,
                    'occupation_cityjson' => $data->occupation_cityjson,
                    'occupation_province' => $data->occupation_province,
                    'occupation_provincejson' => $data->occupation_provincejson,
                    'occupation_name' => $data->occupation_name,
                    'occupation_mobile' => $data->occupation_mobile,
                    'occupation_email' => $data->occupation_email,
    
                    'natureOfWork' => (!is_null($data->occupation)) ? mb_strtoupper($data->natureOfWork) : NULL,
                    'natureOfWorkIfOthers' => (!is_null($data->occupation) && $data->natureOfWork == 'OTHERS') ? mb_strtoupper($data->natureOfWorkIfOthers) : NULL,

                    'vaccinationDate1' => $data->vaccinationDate1,
                    'vaccinationName1' => $data->vaccinationName1,
                    'vaccinationNoOfDose1' => $data->vaccinationNoOfDose1,
                    'vaccinationFacility1' => $data->vaccinationFacility1,
                    'vaccinationRegion1' => $data->vaccinationRegion1,
                    'haveAdverseEvents1' => $data->haveAdverseEvents1,

                    'vaccinationDate2' => $data->vaccinationDate2,
                    'vaccinationName2' => $data->vaccinationName2,
                    'vaccinationNoOfDose2' => $data->vaccinationNoOfDose2,
                    'vaccinationFacility2' => $data->vaccinationFacility2,
                    'vaccinationRegion2' => $data->vaccinationRegion2,
                    'haveAdverseEvents2' => $data->haveAdverseEvents2,
                ]);

                $oldform = Forms::where('records_id', $rec->id)->orderBy('created_at', 'DESC')->first();
            }

            //Auto Change Testing Category/Subgroup Base on the patient data
            $testCat = [];
            if(!is_null($data->SAS)) {
                array_push($testCat, "C");
            }
            else {
                array_push($testCat, "D.1");
            }

            if($data->getAgeInt() >= 60) {
                array_push($testCat, "B");
            }
            if(!in_array("D.1", $testCat) && $data->pType == 'CLOSE CONTACT') {
                array_push($testCat, "D.1");
            }
            if($data->isPregnant == 1 || $data->isForHospitalization == 1) {
                array_push($testCat, "F");
            }

            //Auto Change Case Classification to Probable Based on Symptoms
            if(!is_null($data->SAS)) {
                if(in_array('Anosmia (Loss of Smell)', explode(",", $data->SAS)) || in_array('Ageusia (Loss of Taste)', explode(",", $data->SAS))) {
                    $caseClassi = 'Probable';
                }
                else {
                    $caseClassi = 'Suspect';
                }
            }
            else {
                $caseClassi = 'Suspect';
            }

            $request->user()->form()->create([
                'morbidityMonth' => date('Y-m-d'),
                'dateReported' => date('Y-m-d'),
                'majikCode' => $data->majikCode,
                'status' => 'paswab_rejected',
                'isPresentOnSwabDay' => NULL,
                'records_id' => $rec->id,
                'drunit' => 'CHO GENERAL TRIAS',
                'drregion' => '4A',
                'drprovince' => 'CAVITE',
                'interviewerName' => $data->getDefaultInterviewerName(),
                'interviewerMobile' => '09190664324',
                'interviewDate' => $data->interviewDate,
                'informantName' => NULL,
                'informantRelationship' => NULL,
                'informantMobile' => NULL,
                'existingCaseList' => '1',
                'ecOthersRemarks' => NULL,
                'pType' => ($data->pType == 'FOR TRAVEL') ? 'TESTING' : $data->pType,
                'isForHospitalization' => $data->isForHospitalization,
                'testingCat' => implode(",", $testCat),
                'havePreviousCovidConsultation' => ($data->isNewRecord == 0) ? '1' : '0',
                'dateOfFirstConsult' => ($data->isNewRecord == 0) ? ($oldform) ? $oldform->interviewDate : NULL : NULL,
                'facilityNameOfFirstConsult' => ($data->isNewRecord == 0) ? ($oldform) ? $oldform->drunit : NULL : NULL,

                'dispoType' => 3,
                'dispoName' => NULL,
                'dispoDate' => date('Y-m-d 08:00:00', strtotime($data->interviewDate)),
                'healthStatus' => (!is_null($data->SAS)) ? 'Mild' : 'Asymptomatic',
                'caseClassification' => $caseClassi,
                'isHealthCareWorker' => '0',
                'healthCareCompanyName' => NULL,
                'healthCareCompanyLocation' => NULL,
                'isOFW' => '0',
                'OFWCountyOfOrigin' => NULL,
                'ofwType' => NULL,
                'isFNT' => '0',
                'lsiType' => NULL,
                'FNTCountryOfOrigin' => NULL,
                'isLSI' => '0',
                'LSICity' => NULL,
                'LSIProvince' => NULL,
                'isLivesOnClosedSettings' => '0',
                'institutionType' => NULL,
                'institutionName' => NULL,
                'indgSpecify' => NULL,
                'dateOnsetOfIllness' => $data->dateOnsetOfIllness,
                'SAS' => $data->SAS,
                'SASFeverDeg' => $data->SASFeverDeg,
                'SASOtherRemarks' => $data->SASOtherRemarks,
                'COMO' => $data->COMO,
                'COMOOtherRemarks' => $data->COMOOtherRemarks,
                'PregnantLMP' => $data->ifPregnantLMP,
                'PregnantHighRisk' => ($data->isPregnant == 1) ? '1' : '0',
                'diagWithSARI' => '0',
                'imagingDoneDate' => $data->imagingDoneDate,
                'imagingDone' => $data->imagingDone,
                'imagingResult' => $data->imagingResult,
                'imagingOtherFindings' => $data->imagingOtherFindings,

                'testedPositiveUsingRTPCRBefore' => '0',
                'testedPositiveNumOfSwab' => '0',
                'testedPositiveLab' => NULL,
                'testedPositiveSpecCollectedDate' => NULL,

                'testDateCollected1' => '2121-01-01',
                'oniTimeCollected1' => NULL,
                'testDateReleased1' => NULL,
                'testLaboratory1' => NULL,
                'testType1' => $ttype,
                'testTypeAntigenRemarks1' => ($ttype == "ANTIGEN") ? mb_strtoupper($tOtherRemarks) : NULL,
                'antigenKit1' => ($ttype == "ANTIGEN") ? mb_strtoupper($tAntigenKit) : NULL,
                'testTypeOtherRemarks1' => ($ttype == "OTHERS") ? mb_strtoupper($tOtherRemarks) : NULL,
                'testResult1' => 'PENDING',
                'testResultOtherRemarks1' => NULL,

                'testDateCollected2' => NULL,
                'oniTimeCollected2' => NULL,
                'testDateReleased2' => NULL,
                'testLaboratory2' => NULL,
                'testType2' => NULL,
                'testTypeAntigenRemarks2' => NULL,
                'antigenKit2' => NULL,
                'testTypeOtherRemarks2' => NULL,
                'testResult2' => NULL,
                'testResultOtherRemarks2' => NULL,

                'outcomeCondition' => 'Active',
                'outcomeRecovDate' => NULL,
                'outcomeDeathDate' => NULL,
                'deathImmeCause' => NULL,
                'deathAnteCause' => NULL,
                'deathUndeCause' => NULL,
                'contriCondi' => NULL,

                'expoitem1' => $data->expoitem1,
                'expoDateLastCont' => $data->expoDateLastCont,

                'expoitem2' => '0',
                'intCountry' => NULL,
                'intDateFrom' => NULL,
                'intDateTo' => NULL,
                'intWithOngoingCovid' => 'N/A',
                'intVessel' => NULL,
                'intVesselNo' => NULL,
                'intDateDepart' => NULL,
                'intDateArrive' => NULL,

                'placevisited' => NULL,

                'locName1' => NULL,
                'locAddress1' => NULL,
                'locDateFrom1' => NULL,
                'locDateTo1' => NULL,
                'locWithOngoingCovid1' => 'N/A',

                'locName2' => NULL,
                'locAddress2' => NULL,
                'locDateFrom2' => NULL,
                'locDateTo2' => NULL,
                'locWithOngoingCovid2' => 'N/A',
                
                'locName3' => NULL,
                'locAddress3' => NULL,
                'locDateFrom3' => NULL,
                'locDateTo3' => NULL,
                'locWithOngoingCovid3' => 'N/A',
                
                'locName4' => NULL,
                'locAddress4' => NULL,
                'locDateFrom4' => NULL,
                'locDateTo4' => NULL,
                'locWithOngoingCovid4' => 'N/A',

                'locName5' => NULL,
                'locAddress5' => NULL,
                'locDateFrom5' => NULL,
                'locDateTo5' => NULL,
                'locWithOngoingCovid5' => 'N/A',

                'locName6' => NULL,
                'locAddress6' => NULL,
                'locDateFrom6' => NULL,
                'locDateTo6' => NULL,
                'locWithOngoingCovid6' => 'N/A',

                'locName7' => NULL,
                'locAddress7' => NULL,
                'locDateFrom7' => NULL,
                'locDateTo7' => NULL,
                'locWithOngoingCovid7' => 'N/A',

                'localVessel1' => NULL,
                'localVesselNo1' => NULL,
                'localOrigin1' => NULL,
                'localDateDepart1' => NULL,
                'localDest1' => NULL,
                'localDateArrive1' => NULL,

                'localVessel2' => NULL,
                'localVesselNo2' => NULL,
                'localOrigin2' => NULL,
                'localDateDepart2' => NULL,
                'localDest2' => NULL,
                'localDateArrive2' => NULL,

                'contact1Name' => $data->contact1Name,
                'contact1No' => $data->contact1No,
                'contact2Name' => $data->contact2Name,
                'contact2No' => $data->contact2No,
                'contact3Name' => $data->contact3Name,
                'contact3No' => $data->contact3No,
                'contact4Name' => $data->contact4Name,
                'contact4No' => $data->contact4No,

                'remarks' => $data->patientmsg,
            ]);
            
            //UPDATE PARAMETERS
            $upd = PaSwabDetails::find($data->id);

            $upd->status = 'rejected';
            $upd->remarks = $request->rejectReason;
            $upd->processedAt = date('Y-m-d');

            $upd->save();

            if($data->isNewRecord == 0 && $oldform) {
                //hayaan munang naka-disable para di mag-overlap sa existing data
                
                //$fcheck = Forms::where('id', $oldform->id)->delete();
            }
        }

        return redirect()->action([PaSwabController::class, 'view'])
        ->with('msg', 'Bulk Rejection of the selected data has been proccessed successfully.')
        ->with('msgtype', 'success');
    }
}
