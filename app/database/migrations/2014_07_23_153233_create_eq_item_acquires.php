<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqItemAcquires extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_item_acquires', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('item_id')->unsigned();
			$table->integer('item_type_id')->unsigned();
			$table->integer('count');
			$table->integer('node_from_id')->unsigned();
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
		Schema::drop('eq_item_acquires');
	}

}
