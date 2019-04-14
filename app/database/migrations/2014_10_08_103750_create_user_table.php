<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user', function(Blueprint $table)
		{
			$table->integer('UserID', true);
			$table->integer('UserType');
			$table->integer('ClinicID')->nullable();
			$table->string('TimeSlotDuration', 256)->nullable();
			$table->string('Name', 256);
			$table->string('NRIC', 256)->nullable();
			$table->string('FIN', 256)->nullable();
			$table->string('PhoneNo', 256);
			$table->string('Email', 256);
			$table->string('Password', 256);
                        $table->string('DOB', 50);
                        $table->integer('Age');
                        $table->float('Bmi');
                        $table->float('Weight');
                        $table->float('Height');
                        $table->string('Insurance_Company', 256);
                        $table->string('Insurance_Policy_No', 256);
			$table->string('Lat', 256)->nullable();
			$table->string('Lng', 256)->nullable();
			$table->string('Country', 100);
			$table->string('City', 100);
			$table->string('State', 100);
			$table->integer('created_at');
			$table->integer('updated_at')->nullable();
                        $table->integer('Ref_ID');
                        $table->string('ActiveLink',500);
                        $table->integer('Status');
                        $table->string('ResetLink',500);
                        $table->integer('Recon');
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
		Schema::drop('medi_user');
	}

}
