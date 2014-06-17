<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBgtMealCostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bgt_meal_costs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('dept_id')->unsigned()->index();
			$table->integer('creator_id')->unsigned()->index();
			$table->date('use_date')->index();
			$table->string('sit_code', 10)->index();
			$table->string('use_code', 10)->index();
			$table->string('event_name', 1024);
			$table->integer('meal_count');
			$table->decimal('meal_amount', 15, 0);
			$table->tinyInteger('is_closed')->default(0);
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
		Schema::drop('bgt_meal_costs');
	}

}
