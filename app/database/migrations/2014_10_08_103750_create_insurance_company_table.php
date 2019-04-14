<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsuranceCompanyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('insurance_company', function(Blueprint $table)
		{
			$table->integer('CompanyID', true);
			$table->string('Name', 256);
			$table->string('Description', 256);
                        $table->string('Image', 500);
                        $table->string('Annotation', 500);
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
		Schema::drop('medi_insurance_company');
	}

}
