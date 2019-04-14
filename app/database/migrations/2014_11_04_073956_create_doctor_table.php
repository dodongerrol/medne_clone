<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('doctor', function(Blueprint $table)
		{
			$table->integer('DoctorID', true);
			$table->string('Name', 256);
                        $table->string('Email', 256);
			$table->text('Description',500)->nullable();
                        $table->text('Qualifications',256)->nullable();
                        $table->text('Specialty',256)->nullable();
                        $table->text('Availability',256)->nullable();
                        $table->text('image');
                        $table->string('Phone', 15);
                        $table->string('Emergency', 15);
                        $table->string('Created_on', 50);
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
		Schema::drop('doctor', function(Blueprint $table)
		{
			//
		});
	}

}
