<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMedicalHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('user_medical_history', function(Blueprint $table)
            {
                    $table->integer('HistoryID', true);
                    $table->integer('UserID');
                    $table->string('VisitType', 256);
                    $table->string('Doctor_Name', 256);
                    $table->string('Clinic_Name', 256);                   
                    $table->integer('DoctorID');
                    $table->string('Note',1000);
                    $table->string('Date', 20);
                    $table->integer('Created_on');
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
		//
	}

}
