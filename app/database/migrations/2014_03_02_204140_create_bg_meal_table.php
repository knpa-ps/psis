<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBgMealTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bg_meal', function(Blueprint $table) {
			$table->increments('id');
			$table->date('mob_date');
			$table->integer('dept_id')->unsigned();
			$table->string('event_name', 1024);
			$table->string('mob_code', 10);
			$table->integer('count_officer')->unsigned();
			$table->integer('count_officer_troop')->unsigned();
			$table->integer('count_troop')->unsigned();
			$table->integer('amount')->unsigned();
			$table->integer('creator_id')->unsigned();

			$table->timestamps();

			$table->index('dept_id');
			$table->index('mob_date');
			$table->index('mob_code');
			
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bg_meal');
	}

}
