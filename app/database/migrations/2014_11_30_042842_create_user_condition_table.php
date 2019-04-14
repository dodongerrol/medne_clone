<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserConditionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('user_condition', function(Blueprint $table)
            {
                    $table->integer('ConditionID', true);
                    $table->integer('UserID');
                    $table->string('Name', 256);
                    $table->string('Date', 20);
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
