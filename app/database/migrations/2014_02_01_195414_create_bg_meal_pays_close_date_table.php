<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBgMealPaysCloseDateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bg_meal_pays_close_date', function(Blueprint $table) {
			$table->increments('id');
			$table->date('belong_month');
			$table->dateTime('close_date');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bg_meal_pays_close_date');
	}

}
