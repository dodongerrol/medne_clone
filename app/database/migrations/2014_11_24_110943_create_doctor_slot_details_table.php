<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorSlotDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('doctor_slot_details', function(Blueprint $table)
            {
                $table->integer('SlotDetailID', true);
                $table->integer('DoctorSlotID');
                $table->string('SlotID',50);
                $table->string('Date',50);
                $table->string('Time',50);
                $table->integer('Available');
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
