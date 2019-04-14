<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPromoCodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('user_promo_code', function(Blueprint $table)
            {
                $table->integer('UserPromoID', true);
                $table->integer('UserID');
                $table->integer('ClinicID')->nullable();
                $table->string('Code', 256)->nullable();
                $table->integer('created_at');
                $table->integer('updated_at')->nullable();
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
