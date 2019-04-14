<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDoctorAvailabilityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('doctor_availability', function(Blueprint $table)
		{
			$table->integer('DoctorAvailabilityID', true);
			$table->integer('DoctorID');
			$table->integer('ClinicID');
			$table->integer('StartTime');
			$table->integer('EndTime');
                        $table->integer('created_at');
                        $table->integer('updated_at');
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
		Schema::drop('medi_doctor_availability');
	}

}
