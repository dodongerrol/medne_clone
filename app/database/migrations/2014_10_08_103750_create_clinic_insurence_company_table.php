<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClinicInsurenceCompanyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clinic_insurence_company', function(Blueprint $table)
		{
			$table->integer('ClinicInsurenceID', true);
			$table->integer('ClinicID');
			$table->integer('InsuranceID');
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
		Schema::drop('medi_clinic_insurence_company');
	}

}
