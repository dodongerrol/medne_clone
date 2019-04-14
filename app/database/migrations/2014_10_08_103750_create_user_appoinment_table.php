<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAppoinmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_appoinment', function(Blueprint $table)
		{
			$table->integer('UserAppoinmentID');
			$table->integer('UserID');
                        $table->integer('BookType');
                        $table->integer('DoctorSlotID');
                        $table->integer('SlotDetailID');
                        $table->integer('MediaType');
                        $table->integer('BookNumber'); 
                        $table->string('BookDate',50);
                        $table->integer('Created_on');
                        $table->integer('created_at');
                        $table->integer('updated_at');
                        $table->integer('Status');
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
		Schema::drop('medi_user_appoinment');
	}

}
