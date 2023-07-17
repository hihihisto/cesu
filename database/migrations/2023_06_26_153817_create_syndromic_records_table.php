<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyndromicRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syndromic_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('syndromic_patient_id')->constrained('syndromic_patients')->onDelete('cascade');
            $table->text('opdno');
            $table->datetime('consulation_date');
            $table->string('chief_complain');
            $table->string('temperature');
            $table->string('bloodpressure');
            $table->string('weight');
            $table->string('respiratoryrate')->nullable();
            $table->string('pulserate')->nullable();
            $table->string('saturationperioxigen')->nullable();
            $table->tinyInteger('fever');
            $table->date('fever_onset')->nullable();
            $table->text('fever_remarks')->nullable();
            $table->tinyInteger('rash');
            $table->date('rash_onset')->nullable();
            $table->text('rash_remarks')->nullable();
            $table->tinyInteger('cough');
            $table->date('cough_onset')->nullable();
            $table->text('cough_remarks')->nullable();
            $table->tinyInteger('colds');
            $table->date('colds_onset')->nullable();
            $table->text('colds_remarks')->nullable();
            $table->tinyInteger('conjunctivitis');
            $table->date('conjunctivitis_onset')->nullable();
            $table->text('conjunctivitis_remarks')->nullable();
            $table->tinyInteger('mouthsore');
            $table->date('mouthsore_onset')->nullable();
            $table->text('mouthsore_remarks')->nullable();
            $table->tinyInteger('sorethroat');
            $table->date('sorethroat_onset')->nullable();
            $table->text('sorethroat_remarks')->nullable();
            $table->tinyInteger('lossoftaste');
            $table->date('lossoftaste_onset')->nullable();
            $table->text('lossoftaste_remarks')->nullable();
            $table->tinyInteger('lossofsmell');
            $table->date('lossofsmell_onset')->nullable();
            $table->text('lossofsmell_remarks')->nullable();
            $table->tinyInteger('headache');
            $table->date('headache_onset')->nullable();
            $table->text('headache_remarks')->nullable();
            $table->tinyInteger('jointpain');
            $table->date('jointpain_onset')->nullable();
            $table->text('jointpain_remarks')->nullable();
            $table->tinyInteger('musclepain');
            $table->date('musclepain_onset')->nullable();
            $table->text('musclepain_remarks')->nullable();
            $table->tinyInteger('diarrhea');
            $table->tinyInteger('bloody_stool');
            $table->date('diarrhea_onset')->nullable();
            $table->text('diarrhea_remarks')->nullable();
            $table->tinyInteger('abdominalpain');
            $table->date('abdominalpain_onset')->nullable();
            $table->text('abdominalpain_remarks')->nullable();
            $table->tinyInteger('vomiting');
            $table->date('vomiting_onset')->nullable();
            $table->text('vomiting_remarks')->nullable();
            $table->tinyInteger('weaknessofextremities');
            $table->date('weaknessofextremities_onset')->nullable();
            $table->text('weaknessofextremities_remarks')->nullable();
            $table->tinyInteger('paralysis');
            $table->date('paralysis_onset')->nullable();
            $table->text('paralysis_remarks')->nullable();
            $table->tinyInteger('alteredmentalstatus');
            $table->date('alteredmentalstatus_onset')->nullable();
            $table->text('alteredmentalstatus_remarks')->nullable();
            $table->tinyInteger('animalbite');
            $table->date('animalbite_onset')->nullable();
            $table->text('animalbite_remarks')->nullable();
            $table->tinyInteger('anorexia');
            $table->date('anorexia_onset')->nullable();
            $table->text('anorexia_remarks')->nullable();
            $table->tinyInteger('jaundice');
            $table->date('jaundice_onset')->nullable();
            $table->text('jaundice_remarks')->nullable();
            $table->tinyInteger('nausea');
            $table->date('nausea_onset')->nullable();
            $table->text('nausea_remarks')->nullable();
            $table->tinyInteger('fatigue');
            $table->date('fatigue_onset')->nullable();
            $table->text('fatigue_remarks')->nullable();
            $table->tinyInteger('dyspnea');
            $table->date('dyspnea_onset')->nullable();
            $table->text('dyspnea_remarks')->nullable();

            $table->tinyInteger('is_hospitalized');
            $table->date('date_admitted')->nullable();
            $table->date('date_released')->nullable();

            $table->text('bigmessage')->nullable();
            $table->string('status');
            $table->tinyInteger('brgy_verified')->default(0);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('cascade');

            $table->integer('age_years')->nullable();
            $table->integer('age_months')->nullable();
            $table->integer('age_days')->nullable();

            $table->string('outcome');
            $table->date('outcome_recovered_date')->nullable();
            $table->date('outcome_died_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('syndromic_records');
    }
}
