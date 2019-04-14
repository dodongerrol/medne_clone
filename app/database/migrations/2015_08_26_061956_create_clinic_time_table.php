<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClinicTimeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clinic_time', function(Blueprint $table)
            {
                $table->integer('ClinicTimeID', true);
                $table->integer('ClinicID');
                $table->string('StartTime', 20)->nullable();
                $table->string('EndTime', 20)->nullable();
                $table->integer('Mon')->nullable();
                $table->integer('Tue')->nullable();
                $table->integer('Wed')->nullable();
                $table->integer('Thu')->nullable();
                $table->integer('Fri')->nullable();
                $table->integer('Sat')->nullable();
                $table->integer('Sun')->nullable();
                $table->integer('Created_on');
                $table->integer('created_at');
                $table->integer('updated_at')->nullable();
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
