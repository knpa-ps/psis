<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqPavaCrossRegion extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_pava_cross_region', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('node_id')->unsigned()->index();
			$table->integer('usage_id')->unsigned()->index();
			$table->integer('io_id')->unsigned()->index();
			$table->float('amount')->unsigned();
			$table->date('used_date');
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
		Schema::drop('eq_pava_cross_region');
	}

}
