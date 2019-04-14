<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorSlotsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('doctor_slots', function(Blueprint $table)
            {
                    $table->integer('DoctorSlotID', true);
                    $table->integer('DoctorID');
                    $table->integer('ClinicID');
                    $table->integer('ClinicSession');
                    $table->integer('ConsultationCharge');
                    $table->string('TimeSlot', 50);
                    
                    $table->integer('QueueNumber');
                    $table->string('QueueTime', 50);
                    
                    $table->integer('created_at');
                    $table->integer('updated_at');
                    $table->integer('StartTime');
                    $table->integer('EndTime');
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
