<?php

namespace App\Exports;

use App\Models\Forms;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class FormsExport implements FromCollection, WithMapping, WithHeadings
{

    //use Exportable;
    
    public function __construct(array $id)
    {
        $this->id = $id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Forms::findMany($this->id);
    }

    public function map($form): array {
        $arr_testingcat = explode(",", $form->testingCat);
        $arr_sas = explode(",", $form->SAS);
        $arr_como = explode(",", $form->COMO);
        $arr_ima = explode(",", $form->ImagingDone);
        $arr_lab = explode(",", $form->testsDoneList);
        $arr_exp2 = explode(",", $form->placevisited);
        $arr_cc = array_filter(explode(",", $form->addContName));
        $arr_num = array_filter(explode(",", $form->addContNo));
        $arr_exp = array_filter(explode(",", $form->addContExpSet));

        $arc = array();
        $arn = array();
        $are = array();

        for($i=0;$i<=9;$i++) {
            if(!empty($arr_cc) && isset($arr_cc[$i])) {
                $arc[$i] = $arr_cc[$i];
            }
            else {
                $arc[$i] = "N/A";
            }

            if(!empty($arr_num) && isset($arr_num[$i])) {
                $arn[$i] = $arr_num[$i];
            }
            else {
                $arn[$i] = "N/A";
            }

            if(!empty($arr_exp) && isset($arr_exp[$i])) {
                $are[$i] = $arr_exp[$i];
            }
            else {
                $are[$i] = "N/A";
            }
        }

        if($form->pType == 1) {
            $ptypestr = "SUSPECT";
        }
        else if($form->pType == 2) {
            $ptypestr = "TESTING";
        }
        else if($form->pType == 3) {
            $ptypestr = "CLOSE CONTACT";
        }
        else if($form->pType == 4) {
            $ptypestr = "OTHERS";
        }

        return [
            $form->drunit,
            $form->drregion,
            (!is_null($form->records->philhealth)) ? $form->records->philhealth : "N/A",
            strtoupper($form->interviewerName),
            $form->interviewerMobile,
            date('m/d/Y', strtotime($form->interviewDate)),
            (!is_null($form->informantName)) ? strtoupper($form->informantName) : 'N/A',
            (!is_null($form->informantRelationship)) ? strtoupper($form->informantRelationship) : 'N/A',
            (!is_null($form->informantMobile)) ? $form->informantMobile : 'N/A',
            $ptypestr,
            (!is_null($form->pOthersRemarks)) ? strtoupper($form->pOthersRemarks) : 'N/A',
            (in_array("A", $arr_testingcat)) ? "YES" : "NO",
            (in_array("B", $arr_testingcat)) ? "YES" : "NO",
            (in_array("C", $arr_testingcat)) ? "YES" : "NO",
            (in_array("D", $arr_testingcat)) ? "YES" : "NO",
            (in_array("E", $arr_testingcat)) ? "YES" : "NO",
            (in_array("F", $arr_testingcat)) ? "YES" : "NO",
            (in_array("G", $arr_testingcat)) ? "YES" : "NO",
            (in_array("H", $arr_testingcat)) ? "YES" : "NO",
            (in_array("I", $arr_testingcat)) ? "YES" : "NO",
            (in_array("J", $arr_testingcat)) ? "YES" : "NO",
            $form->records->lname,
            $form->records->fname,
            $form->records->mname,
            date('m/d/Y', strtotime($form->records->bdate)),
            $form->records->getAge(),
            $form->records->gender,
            $form->records->cs,
            $form->records->nationality,
            (!is_null($form->records->occupation)) ? $form->records->occupation : 'N/A',
            $form->records->address_houseno,
            $form->records->address_street,
            $form->records->address_brgy,
            $form->records->address_city,
            $form->records->address_province,
            (!is_null($form->records->phoneno)) ? $form->records->phoneno : 'N/A',
            $form->records->mobile,
            (!is_null($form->records->email)) ? $form->records->email : 'N/A',

            (!is_null($form->records->occupation_lotbldg)) ? $form->records->occupation_lotbldg : 'N/A',
            (!is_null($form->records->occupation_street)) ? $form->records->occupation_street : 'N/A',
            (!is_null($form->records->occupation_brgy)) ? $form->records->occupation_brgy : 'N/A',
            (!is_null($form->records->occupation_city)) ? $form->records->occupation_city : 'N/A',
            (!is_null($form->records->occupation_province)) ? $form->records->occupation_province : 'N/A',
            (!is_null($form->records->occupation_name)) ? $form->records->occupation_name : 'N/A',
            (!is_null($form->records->occupation_mobile)) ? $form->records->occupation_mobile : 'N/A',
            (!is_null($form->records->occupation_email)) ? $form->records->occupation_email : 'N/A',

            ($form->havePreviousCovidConsultation == 1) ? 'YES' : 'NO',
            (!is_null($form->dateOfFirstConsult)) ? date("m/d/Y", strtotime($form->dateOfFirstConsult)) : 'N/A',
            (!is_null($form->facilityNameOfFirstConsult)) ? strtoupper($form->facilityNameOfFirstConsult) : 'N/A',
            ($form->admittedInHealthFacility == 1) ? 'YES' : 'NO',
            (!is_null($form->dateOfAdmissionInHealthFacility)) ? date("m/d/Y", strtotime($form->dateOfAdmissionInHealthFacility)): 'N/A',
            (!is_null($form->facilitynameOfFirstAdmitted)) ? strtoupper($form->facilitynameOfFirstAdmitted) : 'N/A',
            (!is_null($form->fRegionAndOffice)) ? $form->fRegionAndOffice : 'N/A',
            
            ($form->dispoType == 1) ? 'YES' : 'NO',
            ($form->dispoType == 1) ? strtoupper($form->dispoName) : 'N/A',
            ($form->dispoType == 1) ? date("m/d/Y H:i", strtotime($form->dispoDate)) : 'N/A',

            ($form->dispoType == 2) ? 'YES' : 'NO',
            ($form->dispoType == 2) ? strtoupper($form->dispoName) : 'N/A',
            ($form->dispoType == 2) ? date("m/d/Y H:i", strtotime($form->dispoDate)) : 'N/A',

            ($form->dispoType == 3) ? 'YES' : 'NO',
            ($form->dispoType == 3) ? date("m/d/Y H:i", strtotime($form->dispoDate)) : 'N/A',

            ($form->dispoType == 4) ? 'YES' : 'NO',
            ($form->dispoType == 4) ? date("m/d/Y", strtotime($form->dispoDate)) : 'N/A',

            ($form->dispoType == 5) ? 'YES' : 'NO',
            ($form->dispoType == 5) ? strtoupper($form->dispoName) : 'N/A',

            ($form->healthStatus == "Asymptomatic") ? 'YES' : 'NO',
            ($form->healthStatus == "Mild") ? 'YES' : 'NO',
            ($form->healthStatus == "Moderate") ? 'YES' : 'NO',
            ($form->healthStatus == "Severe") ? 'YES' : 'NO',
            ($form->healthStatus == "Critical") ? 'YES' : 'NO',

            ($form->caseClassification == "Suspect") ? 'YES' : 'NO',
            ($form->caseClassification == "Probable") ? 'YES' : 'NO',
            ($form->caseClassification == "Confirmed") ? 'YES' : 'NO',
            ($form->caseClassification == "Non-COVID-19 Case") ? 'YES' : 'NO',

            ($form->isHealthCareWorker == 1) ? 'YES' : 'NO',
            ($form->isHealthCareWorker == 1) ? strtoupper($form->healthCareCompanyName)." - ".strtoupper($form->healthCareCompanyLocation) : 'N/A',
            
            ($form->isOFW == 1) ? 'YES' : 'NO',
            ($form->isOFW == 1) ? strtoupper($form->OFWCountyOfOrigin) : 'N/A',

            ($form->isFNT == 1) ? 'YES' : 'NO',
            ($form->isFNT == 1) ? strtoupper($form->FNTCountryOfOrigin) : 'N/A',

            ($form->isLSI == 1) ? 'YES' : 'NO',
            ($form->isLSI == 1) ? strtoupper($form->LSICity).", ".strtoupper($form->LSIProvince) : 'N/A',

            ($form->isLivesOnClosedSettings == 1) ? 'YES' : 'NO',
            ($form->isLivesOnClosedSettings == 1) ? strtoupper($form->institutionType) : 'N/A',
            ($form->isLivesOnClosedSettings == 1) ? strtoupper($form->institutionName) : 'N/A',

            $form->records->permaaddress_houseno,
            $form->records->permaaddress_street,
            $form->records->permaaddress_brgy,
            $form->records->permaaddress_city,
            $form->records->permaaddress_province,
            (!is_null($form->records->permaphoneno)) ? $form->records->permaphoneno : 'N/A',
            $form->records->permamobile,
            (!is_null($form->records->permaemail)) ? $form->records->permaemail : 'N/A',

            (!is_null($form->oaddresslotbldg)) ? strtoupper($form->oaddresslotbldg) : 'N/A',
            (!is_null($form->oaddressstreet)) ? strtoupper($form->oaddressstreet) : 'N/A',
            (!is_null($form->oaddressscity)) ? strtoupper($form->oaddressscity) : 'N/A',
            (!is_null($form->oaddresssprovince)) ? strtoupper($form->oaddresssprovince) : 'N/A',
            (!is_null($form->oaddressscountry)) ? strtoupper($form->oaddressscountry) : 'N/A',
            (!is_null($form->placeofwork)) ? strtoupper($form->placeofwork) : 'N/A',
            (!is_null($form->employername)) ? strtoupper($form->employername) : 'N/A',
            (!is_null($form->employercontactnumber)) ? $form->employercontactnumber : 'N/A',

            (!is_null($form->dateOnsetOfIllness)) ? date("m/d/Y", strtotime($form->dateOnsetOfIllness)) : 'N/A',
            (in_array("Asymptomatic", $arr_sas)) ? "YES" : "NO",
            (in_array("Fever", $arr_sas)) ? "YES" : "NO",
            (in_array("Fever", $arr_sas)) ? $form->SASFeverDeg : "",
            (in_array("Cough", $arr_sas)) ? "YES" : "NO",
            (in_array("General Weakness", $arr_sas)) ? "YES" : "NO",
            (in_array("Fatigue", $arr_sas)) ? "YES" : "NO",
            (in_array("Headache", $arr_sas)) ? "YES" : "NO",
            (in_array("Myalgia", $arr_sas)) ? "YES" : "NO",
            (in_array("Sore throat", $arr_sas)) ? "YES" : "NO",
            (in_array("Coryza", $arr_sas)) ? "YES" : "NO",
            (in_array("Dyspnea", $arr_sas)) ? "YES" : "NO",
            (in_array("Anorexia", $arr_sas)) ? "YES" : "NO",
            (in_array("Nausea", $arr_sas)) ? "YES" : "NO",
            (in_array("Vomiting", $arr_sas)) ? "YES" : "NO",
            (in_array("Diarrhea", $arr_sas)) ? "YES" : "NO",
            (in_array("Altered Mental Status", $arr_sas)) ? "YES" : "NO",
            (in_array("Anosmia (Loss of Smell)", $arr_sas)) ? "YES" : "NO",
            (in_array("Ageusia (Loss of Taste)", $arr_sas)) ? "YES" : "NO",
            (in_array("Others", $arr_sas)) ? "YES" : "NO",
            (in_array("Others", $arr_sas)) ? strtoupper($form->SASOtherRemarks) : "N/A",
            
            (in_array("None", $arr_como)) ? "YES" : "NO",
            (in_array("Hypertension", $arr_como)) ? "YES" : "NO",
            (in_array("Diabetes", $arr_como)) ? "YES" : "NO",
            (in_array("Heart Disease", $arr_como)) ? "YES" : "NO",
            (in_array("Lung Disease", $arr_como)) ? "YES" : "NO",
            (in_array("Gastrointestinal", $arr_como)) ? "YES" : "NO",
            (in_array("Genito-urinary", $arr_como)) ? "YES" : "NO",
            (in_array("Neurological Disease", $arr_como)) ? "YES" : "NO",
            (in_array("Cancer", $arr_como)) ? "YES" : "NO",
            (in_array("Others", $arr_como)) ? "YES" : "NO",
            (in_array("Others", $arr_como)) ? strtoupper($form->COMOOtherRemarks) : "N/A",
            ($form->records->isPregnant == 1) ? "YES" : "NO",
            ($form->records->isPregnant == 1) ? date('m/d/Y', strtotime($form->PregnantLMP)) : "N/A",
            ($form->PregnantHighRisk == 1) ? "YES" : "NO",

            ($form->diagWithSARI == 1) ? "YES" : "NO",
            (in_array("Chest Radiography", $arr_ima)) ? "YES" : "NO",
            (in_array("Chest Radiography", $arr_ima)) ? $form->chestRDResult : "",
            ($form->chestRDResult == "OTHERS") ? "YES" : "NO",
            ($form->chestRDResult == "OTHERS") ? $form->chestRDOtherFindings : "N/A",
            (in_array("Chest CT", $arr_ima)) ? "YES" : "NO",
            (in_array("Chest CT", $arr_ima)) ? $form->chestCTResult : "",
            ($form->chestCTResult == "OTHERS") ? "YES" : "NO",
            ($form->chestCTResult == "OTHERS") ? $form->chestCTOtherFindings : "N/A",
            (in_array("Lung Ultrasound", $arr_ima)) ? "YES" : "NO",
            (in_array("Lung Ultrasound", $arr_ima)) ? $form->lungUSResult : "",
            ($form->lungUSResult == "OTHERS") ? "YES" : "NO",
            ($form->lungUSResult == "OTHERS") ? $form->lungUSOtherFindings : "N/A",
            (in_array("None", $arr_ima)) ? "YES" : "NO",

            (in_array("RT-PCR (OPS)", $arr_lab)) ? "YES" : "NO",
            (in_array("RT-PCR (OPS)", $arr_lab) && !is_null($form->rtpcr_ops_date_collected)) ? date("m/d/Y", strtotime($form->rtpcr_ops_date_collected)) : "",
            (in_array("RT-PCR (OPS)", $arr_lab) && !is_null($form->rtpcr_ops_laboratory)) ? strtoupper($form->rtpcr_ops_laboratory) : "",
            (in_array("RT-PCR (OPS)", $arr_lab)) ? strtoupper($form->rtpcr_ops_results) : "",
            (in_array("RT-PCR (OPS)", $arr_lab) && !is_null($form->rtpcr_ops_date_released)) ? date("m/d/Y", strtotime($form->rtpcr_ops_date_released)) : "",
            
            (in_array("RT-PCR (NPS)", $arr_lab)) ? "YES" : "NO",
            (in_array("RT-PCR (NPS)", $arr_lab) && !is_null($form->rtpcr_nps_date_collected)) ? date("m/d/Y", strtotime($form->rtpcr_nps_date_collected)) : "",
            (in_array("RT-PCR (NPS)", $arr_lab) && !is_null($form->rtpcr_nps_laboratory)) ? strtoupper($form->rtpcr_nps_laboratory) : "",
            (in_array("RT-PCR (NPS)", $arr_lab)) ? strtoupper($form->rtpcr_nps_results) : "",
            (in_array("RT-PCR (NPS)", $arr_lab) && !is_null($form->rtpcr_nps_date_released)) ? date("m/d/Y", strtotime($form->rtpcr_nps_date_released)) : "",
            
            (in_array("RT-PCR (OPS and NPS)", $arr_lab)) ? "YES" : "NO",
            (in_array("RT-PCR (OPS and NPS)", $arr_lab) && !is_null($form->rtpcr_both_date_collected)) ? date("m/d/Y", strtotime($form->rtpcr_both_date_collected)) : "",
            (in_array("RT-PCR (OPS and NPS)", $arr_lab) && !is_null($form->rtpcr_both_laboratory)) ? strtoupper($form->rtpcr_both_laboratory) : "",
            (in_array("RT-PCR (OPS and NPS)", $arr_lab)) ? strtoupper($form->rtpcr_both_results) : "",
            (in_array("RT-PCR (OPS and NPS)", $arr_lab) && !is_null($form->rtpcr_both_date_released)) ? date("m/d/Y", strtotime($form->rtpcr_both_date_released)) : "",
            
            (in_array("RT-PCR", $arr_lab)) ? "YES" : "NO",
            (in_array("RT-PCR", $arr_lab)) ? strtoupper($form->rtpcr_spec_type) : "N/A",
            (in_array("RT-PCR", $arr_lab) && !is_null($form->rtpcr_spec_date_collected)) ? date("m/d/Y", strtotime($form->rtpcr_spec_date_collected)) : "",
            (in_array("RT-PCR", $arr_lab) && !is_null($form->rtpcr_spec_laboratory)) ? strtoupper($form->rtpcr_spec_laboratory) : "",
            (in_array("RT-PCR", $arr_lab)) ? strtoupper($form->rtpcr_spec_results) : "",
            (in_array("RT-PCR", $arr_lab) && !is_null($form->rtpcr_spec_date_released)) ? date("m/d/Y", strtotime($form->rtpcr_spec_date_released)) : "",
            
            (in_array("Antigen Test", $arr_lab)) ? "YES" : "NO",
            (in_array("Antigen Test", $arr_lab) && !is_null($form->antigen_date_collected)) ? date("m/d/Y", strtotime($form->antigen_date_collected)) : "",
            (in_array("Antigen Test", $arr_lab) && !is_null($form->antigen_laboratory)) ? strtoupper($form->antigen_laboratory) : "",
            (in_array("Antigen Test", $arr_lab)) ? strtoupper($form->antigen_results) : "",
            (in_array("Antigen Test", $arr_lab) && !is_null($form->antigen_date_released)) ? date("m/d/Y", strtotime($form->antigen_date_released)) : "",
            
            (in_array("Antibody Test", $arr_lab)) ? "YES" : "NO",
            (in_array("Antibody Test", $arr_lab) && !is_null($form->antibody_date_collected)) ? date("m/d/Y", strtotime($form->antibody_date_collected)) : "",
            (in_array("Antibody Test", $arr_lab) && !is_null($form->antibody_laboratory)) ? strtoupper($form->antibody_laboratory) : "",
            (in_array("Antibody Test", $arr_lab)) ? strtoupper($form->antibody_results) : "",
            (in_array("Antibody Test", $arr_lab) && !is_null($form->antibody_date_released)) ? date("m/d/Y", strtotime($form->antibody_date_released)) : "",
            (in_array("Others", $arr_lab)) ? "YES" : "NO",
            (in_array("Others", $arr_lab)) ? strtoupper($form->others_specify) : "N/A",
            (in_array("Others", $arr_lab) && !is_null($form->others_date_collected)) ? date("m/d/Y", strtotime($form->others_date_collected)) : "",
            (in_array("Others", $arr_lab) && !is_null($form->others_laboratory)) ? strtoupper($form->others_laboratory) : "",
            (in_array("Others", $arr_lab)) ? strtoupper($form->others_results) : "",
            (in_array("Others", $arr_lab) && !is_null($form->others_date_released)) ? date("m/d/Y", strtotime($form->others_date_released)) : "",
            ($form->testedPositiveUsingRTPCRBefore == 1) ? "YES" : "NO",
            ($form->testedPositiveUsingRTPCRBefore == 1) ? date("m/d/Y", strtotime($form->testedPositiveSpecCollectedDate)) : "N/A",
            ($form->testedPositiveUsingRTPCRBefore == 1) ? strtoupper($form->testedPositiveLab) : "N/A",
            strval($form->testedPositiveNumOfSwab),

            ($form->outcomeCondition == "Active") ? "YES" : "NO",
            ($form->outcomeCondition == "Recovered") ? "YES" : "NO",
            ($form->outcomeCondition == "Recovered") ? date("m/d/Y", strtotime($form->outcomeRecovDate)) : "N/A",
            ($form->outcomeCondition == "Died") ? "YES" : "NO",
            ($form->outcomeCondition == "Died") ? date("m/d/Y", strtotime($form->outcomeDeathDate)) : "N/A",
            ($form->outcomeCondition == "Died") ? strtoupper($form->deathImmeCause) : "N/A",
            ($form->outcomeCondition == "Died") ? strtoupper($form->deathAnteCause) : "N/A",
            ($form->outcomeCondition == "Died") ? strtoupper($form->deathUndeCause) : "N/A",
            ($form->expoitem1 == 1) ? "YES" : "NO",
            ($form->expoitem1 == 1) ? date("m/d/Y", strtotime($form->expoDateLastCont)) : "N/A",
            ($form->expoitem2 == 1 || $form->expoitem2 == 3) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("1", $arr_exp2) || $form->expoitem2 == 3 && in_array("1", $arr_exp2)) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("1", $arr_exp2) || $form->expoitem2 == 3 && in_array("1", $arr_exp2)) ? strtoupper($form->vOpt1_details) : "N/A",
            ($form->expoitem2 == 1 && in_array("1", $arr_exp2) || $form->expoitem2 == 3 && in_array("1", $arr_exp2)) ? date("m/d/Y", strtotime($form->vOpt1_date)) : "N/A",
            ($form->expoitem2 == 1 && in_array("2", $arr_exp2) || $form->expoitem2 == 3 && in_array("2", $arr_exp2)) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("2", $arr_exp2) || $form->expoitem2 == 3 && in_array("2", $arr_exp2)) ? strtoupper($form->vOpt2_details) : "N/A",
            ($form->expoitem2 == 1 && in_array("2", $arr_exp2) || $form->expoitem2 == 3 && in_array("2", $arr_exp2)) ? date("m/d/Y", strtotime($form->vOpt2_date)) : "N/A",
            ($form->expoitem2 == 1 && in_array("3", $arr_exp2) || $form->expoitem2 == 3 && in_array("3", $arr_exp2)) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("3", $arr_exp2) || $form->expoitem2 == 3 && in_array("3", $arr_exp2)) ? strtoupper($form->vOpt3_details) : "N/A",
            ($form->expoitem2 == 1 && in_array("3", $arr_exp2) || $form->expoitem2 == 3 && in_array("3", $arr_exp2)) ? date("m/d/Y", strtotime($form->vOpt3_date)) : "N/A",
            ($form->expoitem2 == 1 && in_array("4", $arr_exp2) || $form->expoitem2 == 3 && in_array("4", $arr_exp2)) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("4", $arr_exp2) || $form->expoitem2 == 3 && in_array("4", $arr_exp2)) ? strtoupper($form->vOpt4_details) : "N/A",
            ($form->expoitem2 == 1 && in_array("4", $arr_exp2) || $form->expoitem2 == 3 && in_array("4", $arr_exp2)) ? date("m/d/Y", strtotime($form->vOpt4_date)) : "N/A",
            ($form->expoitem2 == 1 && in_array("5", $arr_exp2) || $form->expoitem2 == 3 && in_array("5", $arr_exp2)) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("5", $arr_exp2) || $form->expoitem2 == 3 && in_array("5", $arr_exp2)) ? strtoupper($form->vOpt5_details) : "N/A",
            ($form->expoitem2 == 1 && in_array("5", $arr_exp2) || $form->expoitem2 == 3 && in_array("5", $arr_exp2)) ? date("m/d/Y", strtotime($form->vOpt5_date)) : "N/A",
            ($form->expoitem2 == 1 && in_array("6", $arr_exp2) || $form->expoitem2 == 3 && in_array("6", $arr_exp2)) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("6", $arr_exp2) || $form->expoitem2 == 3 && in_array("6", $arr_exp2)) ? strtoupper($form->vOpt6_details) : "N/A",
            ($form->expoitem2 == 1 && in_array("6", $arr_exp2) || $form->expoitem2 == 3 && in_array("6", $arr_exp2)) ? date("m/d/Y", strtotime($form->vOpt6_date)) : "N/A",
            ($form->expoitem2 == 1 && in_array("7", $arr_exp2) || $form->expoitem2 == 3 && in_array("7", $arr_exp2)) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("7", $arr_exp2) || $form->expoitem2 == 3 && in_array("7", $arr_exp2)) ? strtoupper($form->vOpt7_details) : "N/A",
            ($form->expoitem2 == 1 && in_array("7", $arr_exp2) || $form->expoitem2 == 3 && in_array("7", $arr_exp2)) ? date("m/d/Y", strtotime($form->vOpt7_date)) : "N/A",
            ($form->expoitem2 == 1 && in_array("8", $arr_exp2) || $form->expoitem2 == 3 && in_array("8", $arr_exp2)) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("8", $arr_exp2) || $form->expoitem2 == 3 && in_array("8", $arr_exp2)) ? strtoupper($form->vOpt8_details) : "N/A",
            ($form->expoitem2 == 1 && in_array("8", $arr_exp2) || $form->expoitem2 == 3 && in_array("8", $arr_exp2)) ? date("m/d/Y", strtotime($form->vOpt8_date)) : "N/A",
            ($form->expoitem2 == 1 && in_array("9", $arr_exp2) || $form->expoitem2 == 3 && in_array("9", $arr_exp2)) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("9", $arr_exp2) || $form->expoitem2 == 3 && in_array("9", $arr_exp2)) ? strtoupper($form->vOpt9_details) : "N/A",
            ($form->expoitem2 == 1 && in_array("9", $arr_exp2) || $form->expoitem2 == 3 && in_array("9", $arr_exp2)) ? date("m/d/Y", strtotime($form->vOpt9_date)) : "N/A",
            ($form->expoitem2 == 1 && in_array("10", $arr_exp2) || $form->expoitem2 == 3 && in_array("10", $arr_exp2)) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("10", $arr_exp2) || $form->expoitem2 == 3 && in_array("10", $arr_exp2)) ? strtoupper($form->vOpt10_details) : "N/A",
            ($form->expoitem2 == 1 && in_array("10", $arr_exp2) || $form->expoitem2 == 3 && in_array("10", $arr_exp2)) ? date("m/d/Y", strtotime($form->vOpt10_date)) : "N/A",
            ($form->expoitem2 == 1 && in_array("11", $arr_exp2) || $form->expoitem2 == 3 && in_array("11", $arr_exp2)) ? "YES" : "NO",
            ($form->expoitem2 == 1 && in_array("11", $arr_exp2) || $form->expoitem2 == 3 && in_array("11", $arr_exp2)) ? strtoupper($form->vOpt11_details) : "N/A",
            ($form->expoitem2 == 1 && in_array("11", $arr_exp2) || $form->expoitem2 == 3 && in_array("11", $arr_exp2)) ? date("m/d/Y", strtotime($form->vOpt11_date)) : "N/A",

            ($form->hasTravHistOtherCountries == 1) ? "YES" : "NO",
            ($form->hasTravHistOtherCountries == 1) ? strtoupper($form->historyCountryOfExit) : "N/A",
            ($form->hasTravHistOtherCountries == 1) ? strtoupper($form->country_historyTypeOfTranspo) : "N/A",
            ($form->hasTravHistOtherCountries == 1) ? strtoupper($form->country_historyTranspoNo) : "N/A",
            ($form->hasTravHistOtherCountries == 1) ? date("m/d/Y", strtotime($form->country_historyTranspoDateOfDeparture)) : "N/A",
            ($form->hasTravHistOtherCountries == 1) ? date("m/d/Y", strtotime($form->country_historyTranspoDateOfArrival)) : "N/A",
            ($form->hasTravHistLocal == 1) ? "YES" : "NO",
            ($form->hasTravHistLocal == 1) ? strtoupper($form->historyPlaceOfOrigin) : "N/A",
            ($form->hasTravHistLocal == 1) ? strtoupper($form->local_historyTypeOfTranspo) : "N/A",
            ($form->hasTravHistLocal == 1) ? strtoupper($form->local_historyTranspoNo) : "N/A",
            ($form->hasTravHistLocal == 1) ? date("m/d/Y", strtotime($form->local_historyTranspoDateOfDeparture)) : "N/A",
            ($form->hasTravHistLocal == 1) ? date("m/d/Y", strtotime($form->local_historyTranspoDateOfArrival)) : "N/A",
            (!is_null($form->contact1Name)) ? strtoupper($form->contact1Name) : "N/A",
            (!is_null($form->contact1No)) ? $form->contact1No : "N/A",
            (!is_null($form->contact2Name)) ? strtoupper($form->contact2Name) : "N/A",
            (!is_null($form->contact2No)) ? $form->contact2No : "N/A",
            (!is_null($form->contact3Name)) ? strtoupper($form->contact3Name) : "N/A",
            (!is_null($form->contact3No)) ? $form->contact3No : "N/A",
            (!is_null($form->contact4Name)) ? strtoupper($form->contact4Name) : "N/A",
            (!is_null($form->contact4No)) ? $form->contact4No : "N/A",

            $arc[0],
            $arn[0],
            $are[0],

            $arc[1],
            $arn[1],
            $are[1],

            $arc[2],
            $arn[2],
            $are[2],

            $arc[3],
            $arn[3],
            $are[3],

            $arc[4],
            $arn[4],
            $are[4],

            $arc[5],
            $arn[5],
            $are[5],
            
            $arc[6],
            $arn[6],
            $are[6],
            
            $arc[7],
            $arn[7],
            $are[7],

            $arc[8],
            $arn[8],
            $are[8],

            $arc[9],
            $arn[9],
            $are[9],
        ];
    }

    public function headings(): array
    {
        return [
            'Disease Reporting Unit',
            'DRU Region and Province',
            'Philhealth No. *',
            'Name of Interviewer',
            'Contact Number of Interviewer',
            'Date of Interview',
            'Name of Informant (If patient unavailable)',
            'Relationship',
            'Contact Number of Informant',
            'Type of Client',
            'Others: Specify',
            'Testing Category / Subgroup A',
            'Testing Category / Subgroup B',
            'Testing Category / Subgroup C',
            'Testing Category / Subgroup D',
            'Testing Category / Subgroup E',
            'Testing Category / Subgroup F',
            'Testing Category / Subgroup G',
            'Testing Category / Subgroup H',
            'Testing Category / Subgroup I',
            'Testing Category / Subgroup J',
            'Last Name',
            'First Name',
            'Middle Name',
            'Birthday',
            'Age',
            'Sex',
            'Civil Status',
            'Nationality',
            'Occupation',
            'House No./Lot/Bldg',
            'Street/ Purok/ Sitio',
            'Barangay',
            'Municipality/ City',
            'Province',
            'Home Phone no. (&Area Code)',
            'Cellphone No.',
            'Email adress',
            
            'Lot/Bldg',
            'Street',
            'Barangay',
            'Municipality / City',
            'Province',
            'Name of Workplace',
            'Phnoe No. / Cellphone No.',
            'Email adress',
            
            'Did you have any previous COVID-19 related consultation?',
            'Date of first consult (mm/dd/yyyy)',
            'Name of facility where first consult was done',
            'Was the case admitted in a health facility?',
            'Date of admission (mm/dd/yyyy) indicate earliest date if admitted in multiple facilities',
            'Name of facility where patient was first admitted',
            'Region and Province of facility',

            'Admitted in Hospital',
            'Name of Hospital',
            'Date and Time admitted in hospital',

            'Admitted in Isolation / quarantine facility',
            'Name of Isolation / quarantine facility',
            'Date and Time Isolated / quarantined facility',

            'In home isolation / quarantine',
            'Date and time isolated / quarantined',

            'Discharged to home',
            'if Discharged: date of discharge (mm/dd/yyyy)',

            'Others',
            'Others: State reason',

            'Asymptomatic 1',
            'Mild',
            'Moderate',
            'Severe',
            'Critical',

            'Suspect',
            'Probable',
            'Confirmed',
            'Non-Covid',

            'Health Care Worker',
            'Name and location of health facility',
            'Returning overseas Filipino',
            'Country of Origin',
            'Foreign National Traveler',
            'Country of origin',
            'Locally stranded individual / APOR / Traveler',
            'City / Municipality & Province of Origin',
            'Lives in Closed settings',
            'Specify Type of institution (e.g. prisons, residential facilities, retirement communities, care homes, camps',
            'Specify Name of institution',

            'House No./Lot/Bldg',
            'Street/ Purok/ Sitio',
            'Barangay',
            'Municipality/ City',
            'Province',
            'Home Phone no. (&Area Code)',
            'Cellphone No.',
            'Email adress',

            'House No./Lot /Bldg.',
            'Street',
            'Municipality / City',
            'Province',
            'Country',
            'Place of Work',
            "Employer's Name",
            "Employer's / Office's Contact No.",

            'Date of Onset of Illness (mm/dd/yyyy)',
            'Asymptomatic',
            'Fever ',
            '°C',
            'Cough',
            'General Weakness',
            'Fatigue',
            'Headache',
            'Myalgia',
            'Sorethroat',
            'Coryza',
            'Dyspnea',
            'Anorexia',
            'Nausea',
            'Vomiting',
            'Diarrhea',
            'Altered Mental Status',
            'Anosmia (loss of smell)',
            'Ageusia (loss of taste)',
            'Others',
            'Others: Specify',

            'None',
            'Hypertension',
            'Diabetes',
            'Heart Disease',
            'Lung Disease',
            'Gastrointestinal',
            'Genito-Urinary',
            'Neurological Disease',
            'Cancer',
            'Others',
            'Others: Specify',
            'Are you pregnant?',
            'LMP (mm/dd/yyyy)',
            'High risk pregnancy?',

            'Where you diagnose with Severe Acute Respiratory Syndrome? (refer to appendix 2)',
            'Chest Radiography',
            'Chest Radiography results:',
            'Other:',
            'Other findings: Specify',
            'Chest CT ',
            'Chest CT results:',
            'Other:',
            'Other findings: Specify1',
            'Lung Ultrasound',
            'Lung Ultrasound results',
            'Other:',
            'Other findings: Specify2',
            'NONE (No imaging done)',

            'RT-PCR (OPS) ',
            'Date Collected',
            'Laboratory',
            'Results',
            'Date Released',

            'RT-PCR (NPS) ',
            'Date Collected',
            'Laboratory',
            'Results',
            'Date Released',

            'RT-PCR (OPS and NPS) ',
            'Date Collected',
            'Laboratory',
            'Results',
            'Date Released',

            'RT-PCR Others ',
            'RT-PCR (specify type) ',
            'Date Collected',
            'Laboratory',
            'Results',
            'Date Released',
            'Antigen Test',
            'Date Collected',
            'Laboratory',
            'Results',
            'Date Released',
            'Antibody test',
            'Date Collected',
            'Laboratory',
            'Results',
            'Date Released',
            'Others',
            'Others: Specify',
            'Date Collected',
            'Laboratory',
            'Results: Please specify',
            'Date Released',
            'Have you ever tested positive using RT-PCR before?',
            'Date of Specimen Collection',
            'Name of Laboratory',
            'Number of Previous RTPCR done',

            'Active (Currently admitted or in isolation / quarantine)',
            'Recovered',
            'Date of recovery (mm/dd/yyyy)',
            'Died',
            'Date Died (mm/dd/yyyy)',
            'Cause of death * Immediate cause',
            'Antecedent cause',
            'Underlying cause',
            'History of exposure to known probable and/or confirmed COVID-19 case 14 days before onset of signs and symptoms? Or asymptomatic, 14 days before swabbing or specimen collection?',
            'Date of last contact',
            'Have you been in a place with a known COVID-19 community transmission 14 days before the onset of signs and symptoms? OR if Asymptomatic, 14 days before swabbing or specimen collection?',
            'Health facility',
            'Details',
            'date of visit',
            'Closed settings (e.g. jail)',
            'Details',
            'date of visit',
            'Market',
            'Details',
            'date of visit',
            'Home',
            'Details',
            'date of visit',
            'International travel',
            'Details',
            'date of visit',
            'School',
            'Details',
            'date of visit',
            'Transportation',
            'Details',
            'date of visit',
            'Workplace',
            'Details',
            'date of visit',
            'Local Travel',
            'Details',
            'date of visit',
            'Social gathering',
            'Details',
            'date of visit',
            'Others',
            'Details',
            'date of visit',

            'History of travel/ vist/ work in other COUNTRIES with a known COVID-19 transmission 14 days before the onset of signs and symptoms:',
            'Country of Exit',
            'Airline/ Sea Vessel',
            'Flight/ Vessel No.',
            'Date of Departure (mm/dd/yyyy)',
            'Date of Arrival in the Philippines (mm/dd/yyyy)',
            'History of travel/ vist/ work in other LOCAL places with a known COVID-19 transmission 14 days before the onset of signs and symptoms:',
            'Place of Origin',
            'Airline/ Sea Vessel',
            'Flight/ Vessel No.',
            'Date of Departure (mm/dd/yyyy)',
            'Date of Arrival in the Current City / Municipality (mm/dd/yyyy)',
            'Name 1',
            'contact number',
            'Name 2',
            'contact number',
            'Name 3',
            'contact number',
            'Name 4',
            'contact number',

            'CC 1',
            'Phone no.',
            'Exposure setting (ex. Household, work)',
            'CC 2',
            'Phone no.',
            'Exposure setting (ex. Household, work)',
            'CC 3',
            'Phone no.',
            'Exposure setting (ex. Household, work)',
            'CC 4',
            'Phone no.',
            'Exposure setting (ex. Household, work)',
            'CC 5',
            'Phone no.',
            'Exposure setting (ex. Household, work)',
            'CC 6',
            'Phone no.',
            'Exposure setting (ex. Household, work)',
            'CC 7',
            'Phone no.',
            'Exposure setting (ex. Household, work)',
            'CC 8',
            'Phone no.',
            'Exposure setting (ex. Household, work)',
            'CC 9',
            'Phone no.',
            'Exposure setting (ex. Household, work)',
            'CC 10',
            'Phone no.',
            'Exposure setting (ex. Household, work)',
        ];
    }
}
