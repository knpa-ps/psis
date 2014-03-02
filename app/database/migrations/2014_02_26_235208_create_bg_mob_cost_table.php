<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBgMobCostTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bg_mob_cost', function(Blueprint $table) {
			$table->increments('id');
			$table->smallInteger('start');
			$table->smallInteger('end');
			$table->decimal('cost', 10, 1);
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
		Schema::drop('bg_mob_cost');
	}

}
