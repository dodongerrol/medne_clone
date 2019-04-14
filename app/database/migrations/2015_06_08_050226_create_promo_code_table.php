<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromoCodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('promo_code', function(Blueprint $table)
            {
		$table->integer('PromoCodeID', true);
                $table->string('Name', 256);
                $table->string('Code', 256);
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
