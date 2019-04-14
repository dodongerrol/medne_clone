<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorSlotsManageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('doctor_slots_manage', function(Blueprint $table)
            {
                    $table->integer('DoctorSlotManageID', true);
                    $table->integer('DoctorSlotID');
                    //$table->integer('ClinicID');
                    //$table->integer('ClinicSession');
                    //$table->integer('ConsultationCharge');
                    //$table->string('TimeSlot', 50);
                    
                    $table->integer('TotalQueue');
                    $table->integer('CurrentTotalQueue');
                    $table->string('Date', 20);
                    
                    $table->integer('Created_on');
                    $table->integer('created_at');
                    $table->integer('updated_at');
                    $table->integer('Status');
                    $table->integer('Active');
                    
                    
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
