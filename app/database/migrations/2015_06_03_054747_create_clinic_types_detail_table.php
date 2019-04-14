<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClinicTypesDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('clinic_types_detail', function(Blueprint $table)
            {
		$table->integer('ClinicTypesDetailID', true);
                $table->integer('ClinicTypeID');
                $table->integer('ClinicID');
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
