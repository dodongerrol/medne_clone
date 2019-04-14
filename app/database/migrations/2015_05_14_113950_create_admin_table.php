<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('admin', function(Blueprint $table)
            {
                    $table->integer('AdminID', true);
                    $table->string('Name', 256);
                    $table->string('Email', 256);
                    $table->string('Password', 100);
                    $table->string('Token', 500);
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
