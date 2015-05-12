<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqPavaUsage extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_pava_usage', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('event_id')->unsigned();
			$table->float('amount');
			$table->integer('user_node_id')->unsigned();
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
		Schema::drop('eq_pava_usage');
	}

}
