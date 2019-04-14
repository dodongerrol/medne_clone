<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserInsurancePolicyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_insurance_policy', function(Blueprint $table)
		{
			$table->integer('UserInsurancePolicyID', true);
			$table->integer('UserID');
			$table->integer('InsuaranceCompanyID');
			$table->integer('PolicyNo');
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
		Schema::drop('medi_user_insurance_policy');
	}

}
