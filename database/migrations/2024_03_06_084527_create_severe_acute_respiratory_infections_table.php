<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSevereAcuteRespiratoryInfectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('severe_acute_respiratory_infections', function (Blueprint $table) {
            $table->id();
            $table->text('edcs_caseid')->nullable();
            $table->string('epi_id');
            $table->string('patient_number')->nullable();

            $table->string('lname');
            $table->string('fname');
            $table->string('middle_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('sex');

            $table->date('birthdate');
            $table->integer('age_years');
            $table->integer('age_months');
            $table->integer('age_days');

            $table->string('region');
            $table->string('province');
            $table->string('muncity');
            $table->string('barangay')->nullable();
            $table->string('streetpurok')->nullable();

            $table->string('perm_region');
            $table->string('perm_province');
            $table->string('perm_muncity');
            $table->string('perm_barangay')->nullable();
            $table->string('perm_streetpurok')->nullable();

            $table->string('facility_name')->nullable();
            $table->text('edcs_healthFacilityCode')->nullable();

            $table->string('admitted')->nullable();
            $table->date('date_admitted')->nullable();

            $table->date('date_onset')->nullable();
            
            $table->string('ranitidine')->nullable();
            $table->string('zanamivir')->nullable();
            $table->string('amantidine')->nullable();
            $table->string('oseltamivir')->nullable();
            $table->string('others')->nullable();

            $table->string('arethereinfluenzaduringtheweek')->nullable();
            $table->string('school_daycare_workplace')->nullable();

            $table->string('receiveinfluenzavaccinepastyear')->nullable();
            $table->date('date_vaccinated')->nullable();

            $table->string('bats')->nullable();
            $table->string('camels')->nullable();
            $table->string('horses')->nullable();
            $table->string('poultry_birds')->nullable();
            $table->string('pigs')->nullable();
            $table->string('other_animal')->nullable();

            $table->string('history_of_travel')->nullable();
            $table->date('date_of_travel')->nullable();
            $table->string('specify_countries')->nullable();

            $table->string('chestxray_done')->nullable();
            $table->string('chestxray_result')->nullable();

            $table->string('temperature_at_consultation')->nullable();
            $table->string('fever')->nullable();
            $table->string('fever_duration')->nullable();
            $table->string('headache')->nullable();
            $table->string('cough')->nullable();
            $table->string('sorethroat')->nullable();
            $table->string('difficultyofbreathing')->nullable();
            $table->string('requires_hospital_admission')->nullable();
            $table->text('others')->nullable();

            $table->string('any_twomonthstofiveyears_age_withcoughordob')->nullable();
            $table->string('bftsixtybreaths_infants')->nullable();
            $table->string('bftfiftybreaths_twototwelvemonths')->nullable();
            $table->string('bftfortybreaths_onetofiveyo')->nullable();
            $table->string('requires_hospital_admission2')->nullable();
            $table->string('any_twomonthstofiveyears_age_withcoughordob2')->nullable();

            $table->string('unabletodrinkorbreastfeed')->nullable();
            $table->string('vomitseverything')->nullable();
            $table->string('convulsions')->nullable();
            $table->string('lethargic_unconscious')->nullable();
            $table->string('stridor')->nullable();
            $table->string('requires_hospital_admission3')->nullable();

            $table->string('asthma')->nullable();
            $table->string('chroniccardiacdisease')->nullable();
            $table->string('chronicliverdisesae')->nullable();
            $table->string('chronicneurological')->nullable();
            $table->string('chronicrenal')->nullable();
            $table->string('diabetes')->nullable();
            $table->string('haematologicdisorders')->nullable();
            $table->string('immunodeficiencydiseases')->nullable();
            $table->string('pregnancy')->nullable();
            $table->string('antibiotics')->nullable();
            $table->text('specify_antibiotics')->nullable();
            $table->string('antivirals')->nullable();
            $table->text('specify_antivirals')->nullable();
            $table->string('fluid_theraphy')->nullable();
            $table->text('specify_fluidtherapy')->nullable();
            $table->string('oxygen')->nullable();
            $table->text('specify_oxygen')->nullable();
            $table->string('intubation')->nullable();
            $table->text('specify_intubation')->nullable();
            $table->string('bacterialtesting')->nullable();
            $table->text('specify_bacterialtesting')->nullable();
            $table->string('othertherapeutic')->nullable();
            $table->text('specify_othertherapeutic')->nullable();

            $table->text('final_diagnosis')->nullable();
            $table->string('outcome')->nullable();
            $table->date('date_discharged')->nullable();
            $table->date('date_died')->nullable();
            $table->text('case_classification')->nullable();

            $table->string('year')->nullable();
            $table->string('morbidity_month')->nullable();
            $table->string('morbidity_week')->nullable();

            $table->string('admit_to_entry')->nullable();
            $table->string('onset_to_admit')->nullable();

            $table->tinyInteger('systemsent', 1)->default(0);
            $table->tinyInteger('enabled', 1)->default(1);
            $table->text('edcs_investigatorName')->nullable();
            $table->text('edcs_contactNo')->nullable();
            $table->text('edcs_ageGroup')->nullable();
            $table->text('edcs_verificationLevel')->nullable();
            $table->tinyInteger('from_edcs', 1)->default(1);
            $table->tinyInteger('encoded_mw')->nullable();
            $table->tinyInteger('match_casedef', 1)->default(1);
            $table->tinyInteger('system_notified', 1)->default(0);
            $table->string('edcs_userid')->nullable();
            $table->string('edcs_last_modifiedby')->nullable();
            $table->date('edcs_last_modified_date')->nullable();
            $table->tinyInteger('notify_email_sent', 1)->default(0);
            $table->timestamp('notify_email_sent_datetime')->nullable();
            $table->string('edcs_patientcontactnum')->nullable();
            $table->text('system_remarks')->nullable();
            $table->string('system_subdivision_id', 5)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('severe_acute_respiratory_infections');
    }
}
