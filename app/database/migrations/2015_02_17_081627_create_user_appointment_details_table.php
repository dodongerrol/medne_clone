<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAppointmentDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(){
            Schema::create('user_appointment_details', function(Blueprint $table){
                    $table->integer('AppoinmentDetailID',true);
                    $table->integer('AppoinmentID');
                    $table->text('Diagnosis',1500)->nullable();
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
