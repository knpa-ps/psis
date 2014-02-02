<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBgMealPaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bg_meal_pays', function(Blueprint $table) {
			$table->increments('id');
			$table->date('use_date');
			$table->integer('dept_id')->unsigned();
			$table->string('event_name', 1024);
			$table->decimal('demo_cnt', 10,1);
			$table->decimal('escort_cnt', 10,1);
			$table->decimal('crowd_cnt', 10,1);
			$table->decimal('rescue_cnt', 10,1);
			$table->decimal('etc_cnt', 10,1);
			$table->decimal('officer_cnt', 10,1);
			$table->decimal('officer2_cnt', 10,1);
			$table->decimal('troop_cnt', 10,1);
			$table->decimal('officer_amt', 10,1);
			$table->decimal('officer2_amt', 10, 1);
			$table->decimal('troop_amt', 10, 1);
			$table->integer('creator_id')->unsigned();
			$table->softDeletes();
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
		Schema::dropIfExists('bg_meal_pays');
	}

}
