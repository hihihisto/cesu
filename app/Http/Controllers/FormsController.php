<?php

namespace App\Http\Controllers;

use App\Models\Forms;
use App\Models\Records;
use App\Exports\FormsExport;
use App\Http\Requests\FormValidationRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PragmaRX\Countries\Package\Countries;

class FormsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*
        if(request()->input('q')) {
            $forms = Forms::whereHas('records', function ($query) {
                $query->where('lname', 'LIKE', '%'.request()->input('q').'%')
                ->orWhere('fname', 'LIKE', '%'.request()->input('q').'%')
                ->orWhere('mname', 'LIKE', '%'.request()->input('q').'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        }
        else {
            $forms = Forms::orderBy('created_at', 'desc')->paginate(10);
        }
        */

        if(request()->input('view')) {
            if(request()->input('view') == 1) {
                $forms = Forms::orderBy('created_at', 'desc')->get();
            }
            else if(request()->input('view') == 2) {
                $forms = Forms::whereDate('expoDateLastCont', '<=', date('Y-m-d', strtotime("-5 Days")))->orderBy('created_at', 'desc')->get();
            }
            else if(request()->input('view') == 3) {
                $forms = Forms::where('isExported', '0')->orderBy('created_at', 'desc')->get();
            }
        }
        else {
            $forms = Forms::orderBy('created_at', 'desc')->get();
        }
        

        $records = Records::all();
        $records = $records->count();

        return view('forms', ['forms' => $forms, 'records' => $records]);
    }

    public function export(Request $request)
    {
        //Forms::whereIn('id',[implode(",", $request->listToPrint)])->update(['isExported'=>1]);

        $models = Forms::findMany([implode(",", $request->listToPrint)]);

        $models->each(function ($item){
            $item->update(['isExported'=>'1']);
        });
        
        return Excel::download(new FormsExport($request->listToPrint), 'CIF_'.date("m_d_Y_H_i_s").'.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $records = Records::all()->sortBy('lname', SORT_NATURAL);
        $countries = new Countries();
        $countries = $countries->all()->sortBy('name.common', SORT_NATURAL);
        $all = $countries->all()->pluck('name.common')->toArray();
        return view('formscreate', ['countries' => $all, 'records' => $records]);
    }

    public function ajaxGetUserRecord ($id) {
        $srec = Records::where('id',$id)->get();

        $sdata['data'] = $srec;
        echo json_encode($sdata);
        exit;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(FormValidationRequest $request)
    {
        $rec = Records::findOrFail($request->records_id);

        if($rec->isPregnant == 0) {
            $hrp = 0;
        }
        else {
            $hrp = $request->highRiskPregnancy;
        }

        $request->validated();
        
        $request->user()->form()->create([
            'records_id' => $request->records_id,
            'drunit' => $request->drunit,
            'drregion' => $request->drregion,
            'interviewerName' => $request->interviewerName,
            'interviewerMobile' => $request->interviewerMobile,
            'interviewDate' => $request->interviewDate,
            'informantName' => $request->informantName,
            'informantRelationship' => $request->informantRelationship,
            'informantMobile' => $request->informantMobile,
            'existingCaseList' => implode(",", $request->existingCaseList),
            'ecOthersRemarks' => $request->ecOthersRemarks,
            'pType' => $request->pType,
            'testingCat' => implode(",",$request->testingCat),
            'havePreviousCovidConsultation' => $request->havePreviousCovidConsultation,
            'dateOfFirstConsult' => $request->dateOfFirstConsult,
            'facilityNameOfFirstConsult' => $request->facilityNameOfFirstConsult,
            
            'dispoType' => $request->dispositionType,
            'dispoName' => $request->dispositionName,
            'dispoDate' => $request->dispositionDate,
            'healthStatus' => $request->healthStatus,
            'caseClassification' => $request->caseClassification,
            'isHealthCareWorker' => $request->isHealthCareWorker,
            'healthCareCompanyName' => $request->healthCareCompanyName,
            'healthCareCompanyLocation' => $request->healthCareCompanyLocation,
            'isOFW' => $request->isOFW,
            'OFWCountyOfOrigin' => $request->OFWCountyOfOrigin,
            'ofwType' => ($request->isOFW == 1) ? $request->ofwType : NULL,
            'isFNT' => $request->isFNT,
            'lsiType' => $request->lsiType,
            'FNTCountryOfOrigin' => $request->FNTCountryOfOrigin,
            'isLSI' => $request->isLSI,
            'LSICity' => $request->LSICity,
            'LSIProvince' => $request->LSIProvince,
            'isLivesOnClosedSettings' => $request->isLivesOnClosedSettings,
            'institutionType' => $request->institutionType,
            'institutionName' => $request->institutionName,
            'indgSpecify' => $request->indgSpecify,
            'dateOnsetOfIllness' => $request->dateOnsetOfIllness,
            'SAS' => (!is_null($request->sasCheck)) ? implode(",", $request->sasCheck) : NULL,
            'SASFeverDeg' => $request->SASFeverDeg,
            'SASOtherRemarks' => $request->SASOtherRemarks,
            'COMO' => implode(",", $request->comCheck),
            'COMOOtherRemarks' => $request->COMOOtherRemarks,
            'PregnantLMP' => $request->PregnantLMP,
            'PregnantHighRisk' => $hrp,
            'diagWithSARI' => $request->diagWithSARI,
            'imagingDoneDate' => $request->imagingDoneDate,
            'imagingDone' => $request->imagingDone,
            'imagingResult' => $request->imagingResult,
            'imagingOtherFindings' => $request->imagingResult,

            'testedPositiveUsingRTPCRBefore' => $request->testedPositiveUsingRTPCRBefore,
            'testedPositiveNumOfSwab' => $request->testedPositiveNumOfSwab,
            'testedPositiveLab' => $request->testedPositiveLab,
            'testedPositiveSpecCollectedDate' => $request->testedPositiveSpecCollectedDate,

            'testDateCollected1' => $request->testDateCollected1,
            'testDateReleased1' => $request->testDateReleased1,
            'testLaboratory1' => $request->testLaboratory1,
            'testType1' => $request->testType1,
            'testTypeOtherRemarks1' => $request->testTypeOtherRemarks1,
            'testResult1' => $request->testResult1,
            'testResultOtherRemarks1' => $request->testResultOtherRemarks1,

            'testDateCollected2' => $request->testDateCollected2,
            'testDateReleased2' => $request->testDateReleased2,
            'testLaboratory2' => $request->testLaboratory2,
            'testType2' => ($request->testType2 != "N/A") ? $request->testType2 : NULL,
            'testTypeOtherRemarks2' => $request->testTypeOtherRemarks2,
            'testResult2' => ($request->testType2 != "N/A") ? $request->testResult2 : NULL,
            'testResultOtherRemarks2' => $request->testResultOtherRemarks2,

            'outcomeCondition' => $request->outcomeCondition,
            'outcomeRecovDate' => $request->outcomeRecovDate,
            'outcomeDeathDate' => $request->outcomeDeathDate,
            'deathImmeCause' => $request->deathImmeCause,
            'deathAnteCause' => $request->deathAnteCause,
            'deathUndeCause' => $request->deathUndeCause,
            'contriCondi' => $request->contriCondi,

            'expoitem1' => $request->expoitem1,
            'expoDateLastCont' => $request->expoDateLastCont,

            'expoitem2' => $request->expoitem2,
            'intCountry' => $request->intCountry,
            'intDateFrom' => $request->intDateFrom,
            'intDateTo' => $request->intDateTo,
            'intWithOngoingCovid' => ($request->expoitem2 == 2) ? $request->intWithOngoingCovid : 'N/A',
            'intVessel' => $request->intVessel,
            'intVesselNo' => $request->intVesselNo,
            'intDateDepart' => $request->intDateDepart,
            'intDateArrive' => $request->intDateArrive,

            'placevisited' => (!is_null($request->placevisited)) ? implode(",", $request->placevisited) : NULL,

            'locName1' => $request->locName1,
            'locAddress1' => $request->locAddress1,
            'locDateFrom1' => $request->locDateFrom1,
            'locDateTo1' => $request->locDateTo1,
            'locWithOngoingCovid1' => (!is_null($request->placevisited) && in_array('Health Facility', $request->placevisited)) ? $request->locWithOngoingCovid1 : 'N/A', 

            'locName2' => $request->locName2,
            'locAddress2' => $request->locAddress2,
            'locDateFrom2' => $request->locDateFrom2,
            'locDateTo2' => $request->locDateTo2,
            'locWithOngoingCovid2' => (!is_null($request->placevisited) && in_array('Closed Settings', $request->placevisited)) ? $request->locWithOngoingCovid2 : 'N/A',
            
            'locName3' => $request->locName3,
            'locAddress3' => $request->locAddress3,
            'locDateFrom3' => $request->locDateFrom3,
            'locDateTo3' => $request->locDateTo3,
            'locWithOngoingCovid3' => (!is_null($request->placevisited) && in_array('School', $request->placevisited)) ? $request->locWithOngoingCovid3 : 'N/A',
            
            'locName4' => $request->locName4,
            'locAddress4' => $request->locAddress4,
            'locDateFrom4' => $request->locDateFrom4,
            'locDateTo4' => $request->locDateTo4,
            'locWithOngoingCovid4' => (!is_null($request->placevisited) && in_array('Workplace', $request->placevisited)) ? $request->locWithOngoingCovid4 : 'N/A',

            'locName5' => $request->locName5,
            'locAddress5' => $request->locAddress5,
            'locDateFrom5' => $request->locDateFrom5,
            'locDateTo5' => $request->locDateTo5,
            'locWithOngoingCovid5' => (!is_null($request->placevisited) && in_array('Market', $request->placevisited)) ? $request->locWithOngoingCovid5 : 'N/A',

            'locName6' => $request->locName6,
            'locAddress6' => $request->locAddress6,
            'locDateFrom6' => $request->locDateFrom6,
            'locDateTo6' => $request->locDateTo6,
            'locWithOngoingCovid6' => (!is_null($request->placevisited) && in_array('Social Gathering', $request->placevisited)) ? $request->locWithOngoingCovid6 : 'N/A',

            'locName7' => $request->locName7,
            'locAddress7' => $request->locAddress7,
            'locDateFrom7' => $request->locDateFrom7,
            'locDateTo7' => $request->locDateTo7,
            'locWithOngoingCovid7' => (!is_null($request->placevisited) && in_array('Others', $request->placevisited)) ? $request->locWithOngoingCovid7 : 'N/A',

            'localVessel1' => $request->localVessel1,
            'localVesselNo1' => $request->localVesselNo1,
            'localOrigin1' => $request->localOrigin1,
            'localDateDepart1' => $request->localDateDepart1,
            'localDest1' => $request->localDest1,
            'localDateArrive1' => $request->localDateArrive1,

            'localVessel2' => $request->localVessel2,
            'localVesselNo2' => $request->localVesselNo2,
            'localOrigin2' => $request->localOrigin2,
            'localDateDepart2' => $request->localDateDepart2,
            'localDest2' => $request->localDest2,
            'localDateArrive2' => $request->localDateArrive2,

            'contact1Name' => $request->contact1Name,
            'contact1No' => $request->contact1No,
            'contact2Name' => $request->contact2Name,
            'contact2No' => $request->contact2No,
            'contact3Name' => $request->contact3Name,
            'contact3No' => $request->contact3No,
            'contact4Name' => $request->contact4Name,
            'contact4No' => $request->contact4No,
        ]);

        return redirect()->action([FormsController::class, 'index'])->with('status', 'CIF of Patient was created successfully.')->with('statustype', 'success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $records = Forms::findOrFail($id);

        $countries = new Countries();
        $countries = $countries->all()->sortBy('name.common', SORT_NATURAL);
        $all = $countries->all()->pluck('name.common')->toArray();
        
        return view('formsedit', ['countries' => $all, 'records' => $records]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FormValidationRequest $request, $id)
    {
        $rec = Forms::findOrFail($id);
        $rec = Records::findOrFail($rec->records->id);

        if($rec->isPregnant == 0) {
            $hrp = 0;
        }
        else {
            $hrp = $request->highRiskPregnancy;
        }

        $request->validated();

        $form = Forms::where('id', $id)->update([
            'drunit' => $request->drunit,
            'drregion' => $request->drregion,
            'interviewerName' => $request->interviewerName,
            'interviewerMobile' => $request->interviewerMobile,
            'interviewDate' => $request->interviewDate,
            'informantName' => $request->informantName,
            'informantRelationship' => $request->informantRelationship,
            'informantMobile' => $request->informantMobile,
            'existingCaseList' => implode(",", $request->existingCaseList),
            'ecOthersRemarks' => $request->ecOthersRemarks,
            'pType' => $request->pType,
            'testingCat' => implode(",",$request->testingCat),
            'havePreviousCovidConsultation' => $request->havePreviousCovidConsultation,
            'dateOfFirstConsult' => $request->dateOfFirstConsult,
            'facilityNameOfFirstConsult' => $request->facilityNameOfFirstConsult,
            
            'dispoType' => $request->dispositionType,
            'dispoName' => $request->dispositionName,
            'dispoDate' => $request->dispositionDate,
            'healthStatus' => $request->healthStatus,
            'caseClassification' => $request->caseClassification,
            'isHealthCareWorker' => $request->isHealthCareWorker,
            'healthCareCompanyName' => $request->healthCareCompanyName,
            'healthCareCompanyLocation' => $request->healthCareCompanyLocation,
            'isOFW' => $request->isOFW,
            'OFWCountyOfOrigin' => $request->OFWCountyOfOrigin,
            'ofwType' => ($request->isOFW == 1) ? $request->ofwType : NULL,
            'isFNT' => $request->isFNT,
            'lsiType' => $request->lsiType,
            'FNTCountryOfOrigin' => $request->FNTCountryOfOrigin,
            'isLSI' => $request->isLSI,
            'LSICity' => $request->LSICity,
            'LSIProvince' => $request->LSIProvince,
            'isLivesOnClosedSettings' => $request->isLivesOnClosedSettings,
            'institutionType' => $request->institutionType,
            'institutionName' => $request->institutionName,
            'indgSpecify' => $request->indgSpecify,
            'dateOnsetOfIllness' => $request->dateOnsetOfIllness,
            'SAS' => (!is_null($request->sasCheck)) ? implode(",", $request->sasCheck) : NULL,
            'SASFeverDeg' => $request->SASFeverDeg,
            'SASOtherRemarks' => $request->SASOtherRemarks,
            'COMO' => implode(",", $request->comCheck),
            'COMOOtherRemarks' => $request->COMOOtherRemarks,
            'PregnantLMP' => $request->PregnantLMP,
            'PregnantHighRisk' => $hrp,
            'diagWithSARI' => $request->diagWithSARI,
            'imagingDoneDate' => $request->imagingDoneDate,
            'imagingDone' => $request->imagingDone,
            'imagingResult' => $request->imagingResult,
            'imagingOtherFindings' => $request->imagingResult,

            'testedPositiveUsingRTPCRBefore' => $request->testedPositiveUsingRTPCRBefore,
            'testedPositiveNumOfSwab' => $request->testedPositiveNumOfSwab,
            'testedPositiveLab' => $request->testedPositiveLab,
            'testedPositiveSpecCollectedDate' => $request->testedPositiveSpecCollectedDate,

            'testDateCollected1' => $request->testDateCollected1,
            'testDateReleased1' => $request->testDateReleased1,
            'testLaboratory1' => $request->testLaboratory1,
            'testType1' => $request->testType1,
            'testTypeOtherRemarks1' => $request->testTypeOtherRemarks1,
            'testResult1' => $request->testResult1,
            'testResultOtherRemarks1' => $request->testResultOtherRemarks1,

            'testDateCollected2' => $request->testDateCollected2,
            'testDateReleased2' => $request->testDateReleased2,
            'testLaboratory2' => $request->testLaboratory2,
            'testType2' => ($request->testType2 != "N/A") ? $request->testType2 : NULL,
            'testTypeOtherRemarks2' => $request->testTypeOtherRemarks2,
            'testResult2' => ($request->testType2 != "N/A") ? $request->testResult2 : NULL,
            'testResultOtherRemarks2' => $request->testResultOtherRemarks2,

            'outcomeCondition' => $request->outcomeCondition,
            'outcomeRecovDate' => $request->outcomeRecovDate,
            'outcomeDeathDate' => $request->outcomeDeathDate,
            'deathImmeCause' => $request->deathImmeCause,
            'deathAnteCause' => $request->deathAnteCause,
            'deathUndeCause' => $request->deathUndeCause,
            'contriCondi' => $request->contriCondi,

            'expoitem1' => $request->expoitem1,
            'expoDateLastCont' => $request->expoDateLastCont,

            'expoitem2' => $request->expoitem2,
            'intCountry' => $request->intCountry,
            'intDateFrom' => $request->intDateFrom,
            'intDateTo' => $request->intDateTo,
            'intWithOngoingCovid' => ($request->expoitem2 == 2) ? $request->intWithOngoingCovid : 'N/A',
            'intVessel' => $request->intVessel,
            'intVesselNo' => $request->intVesselNo,
            'intDateDepart' => $request->intDateDepart,
            'intDateArrive' => $request->intDateArrive,

            'placevisited' => (!is_null($request->placevisited)) ? implode(",", $request->placevisited) : NULL,

            'locName1' => $request->locName1,
            'locAddress1' => $request->locAddress1,
            'locDateFrom1' => $request->locDateFrom1,
            'locDateTo1' => $request->locDateTo1,
            'locWithOngoingCovid1' => (!is_null($request->placevisited) && in_array('Health Facility', $request->placevisited)) ? $request->locWithOngoingCovid1 : 'N/A', 

            'locName2' => $request->locName2,
            'locAddress2' => $request->locAddress2,
            'locDateFrom2' => $request->locDateFrom2,
            'locDateTo2' => $request->locDateTo2,
            'locWithOngoingCovid2' => (!is_null($request->placevisited) && in_array('Closed Settings', $request->placevisited)) ? $request->locWithOngoingCovid2 : 'N/A',
            
            'locName3' => $request->locName3,
            'locAddress3' => $request->locAddress3,
            'locDateFrom3' => $request->locDateFrom3,
            'locDateTo3' => $request->locDateTo3,
            'locWithOngoingCovid3' => (!is_null($request->placevisited) && in_array('School', $request->placevisited)) ? $request->locWithOngoingCovid3 : 'N/A',
            
            'locName4' => $request->locName4,
            'locAddress4' => $request->locAddress4,
            'locDateFrom4' => $request->locDateFrom4,
            'locDateTo4' => $request->locDateTo4,
            'locWithOngoingCovid4' => (!is_null($request->placevisited) && in_array('Workplace', $request->placevisited)) ? $request->locWithOngoingCovid4 : 'N/A',

            'locName5' => $request->locName5,
            'locAddress5' => $request->locAddress5,
            'locDateFrom5' => $request->locDateFrom5,
            'locDateTo5' => $request->locDateTo5,
            'locWithOngoingCovid5' => (!is_null($request->placevisited) && in_array('Market', $request->placevisited)) ? $request->locWithOngoingCovid5 : 'N/A',

            'locName6' => $request->locName6,
            'locAddress6' => $request->locAddress6,
            'locDateFrom6' => $request->locDateFrom6,
            'locDateTo6' => $request->locDateTo6,
            'locWithOngoingCovid6' => (!is_null($request->placevisited) && in_array('Social Gathering', $request->placevisited)) ? $request->locWithOngoingCovid6 : 'N/A',

            'locName7' => $request->locName7,
            'locAddress7' => $request->locAddress7,
            'locDateFrom7' => $request->locDateFrom7,
            'locDateTo7' => $request->locDateTo7,
            'locWithOngoingCovid7' => (!is_null($request->placevisited) && in_array('Others', $request->placevisited)) ? $request->locWithOngoingCovid7 : 'N/A',

            'localVessel1' => $request->localVessel1,
            'localVesselNo1' => $request->localVesselNo1,
            'localOrigin1' => $request->localOrigin1,
            'localDateDepart1' => $request->localDateDepart1,
            'localDest1' => $request->localDest1,
            'localDateArrive1' => $request->localDateArrive1,

            'localVessel2' => $request->localVessel2,
            'localVesselNo2' => $request->localVesselNo2,
            'localOrigin2' => $request->localOrigin2,
            'localDateDepart2' => $request->localDateDepart2,
            'localDest2' => $request->localDest2,
            'localDateArrive2' => $request->localDateArrive2,

            'contact1Name' => $request->contact1Name,
            'contact1No' => $request->contact1No,
            'contact2Name' => $request->contact2Name,
            'contact2No' => $request->contact2No,
            'contact3Name' => $request->contact3Name,
            'contact3No' => $request->contact3No,
            'contact4Name' => $request->contact4Name,
            'contact4No' => $request->contact4No,
			]);

            return redirect()->action([FormsController::class, 'index'])->with('status', 'CIF for '.$rec->lname.", ".$rec->fname." ".$rec->lname." has been updated successfully.")->with('statustype', 'success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
