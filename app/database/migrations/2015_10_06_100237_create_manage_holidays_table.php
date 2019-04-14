<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManageHolidaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('manage_holidays', function(Blueprint $table)
            {
                $table->integer('HolidayID', true);
                $table->integer('ClinicID');
                $table->integer('Party');
                $table->integer('Type');
                $table->string('Title',20);
                $table->string('Holiday',20);
                $table->string('From_Time',20);
                $table->string('To_Time',20);
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
