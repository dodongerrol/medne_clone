<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClinicTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clinic', function(Blueprint $table)
		{
			$table->integer('ClinicID', true);
			$table->string('Name', 256);
			$table->text('Description');
                        $table->text('image');
                        $table->string('Address', 256);
                        $table->string('City', 256);
                        $table->string('State', 256);
                        $table->string('Country', 256);
                        $table->string('Postal', 256);
                        $table->string('District', 256);
                        $table->string('Lat', 256);
			$table->string('Lng', 256);
                        $table->string('Phone', 256);
                        $table->string('MRT', 256);
                        $table->string('Opening', 256);
                        $table->integer('Created_on', 50);
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
		Schema::drop('medi_clinic');
	}

}
