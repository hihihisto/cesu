<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVaxcertConcernsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vaxcert_concerns', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('PENDING');
            $table->string('vaxcert_refno')->nullable();

            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('suffix');

            $table->date('bdate');
            $table->string('contact_number');
            $table->string('email')->nullable();

            $table->text('address_region_code');
            $table->text('address_region_text');
            $table->text('address_province_code');
            $table->text('address_province_text');
            $table->text('address_muncity_code');
            $table->text('address_muncity_text');
            $table->text('address_brgy_code');
            $table->text('address_brgy_text');

            $table->date('dose1_date');
            $table->string('dose1_manufacturer');
            $table->string('dose1_batchno')->nullable();
            $table->string('dose1_lotno')->nullable();
            $table->string('dose1_bakuna_center_text')->nullable();
            $table->string('dose1_vaccinator_last_name')->nullable();
            $table->string('dose1_vaccinator_first_name')->nullable();

            $table->date('dose2_date');
            $table->string('dose2_manufacturer');
            $table->string('dose2_batchno')->nullable();
            $table->string('dose2_lotno')->nullable();
            $table->string('dose2_bakuna_center_text')->nullable();
            $table->string('dose2_vaccinator_last_name')->nullable();
            $table->string('dose2_vaccinator_first_name')->nullable();

            $table->date('dose3_date');
            $table->string('dose3_manufacturer');
            $table->string('dose3_batchno')->nullable();
            $table->string('dose3_lotno')->nullable();
            $table->string('dose3_bakuna_center_text')->nullable();
            $table->string('dose3_vaccinator_last_name')->nullable();
            $table->string('dose3_vaccinator_first_name')->nullable();

            $table->date('dose4_date');
            $table->string('dose4_manufacturer');
            $table->string('dose4_batchno')->nullable();
            $table->string('dose4_lotno')->nullable();
            $table->string('dose4_bakuna_center_text')->nullable();
            $table->string('dose4_vaccinator_last_name')->nullable();
            $table->string('dose4_vaccinator_first_name')->nullable();

            $table->string('concern_type');
            $table->text('concern_msg');

            $table->text('id_file');
            $table->text('vaxcard_file');
            $table->string('ref_no');
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
        Schema::dropIfExists('vaxcert_concerns');
    }
}