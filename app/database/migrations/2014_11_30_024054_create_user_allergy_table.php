<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAllergyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('user_allergy', function(Blueprint $table)
            {
                    $table->integer('AllergyID', true);
                    $table->integer('UserID');
                    $table->string('Name', 256);
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
