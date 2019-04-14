<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClinicTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('clinic_types', function(Blueprint $table)
            {
		$table->integer('ClinicTypeID', true);
                $table->string('Name', 256);
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
