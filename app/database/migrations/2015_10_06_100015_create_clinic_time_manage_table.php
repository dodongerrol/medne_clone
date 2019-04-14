<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClinicTimeManageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('clinic_time_manage', function(Blueprint $table)
            {
                $table->integer('TimeManageID', true);
                $table->integer('ClinicID');
                $table->integer('Type');
                $table->string('From_Week',50);
                $table->string('To_Week',50);
                $table->integer('Status');
                $table->integer('Repeat');
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
		//
	}

}
