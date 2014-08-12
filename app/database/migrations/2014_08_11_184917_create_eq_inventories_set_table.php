<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqInventoriesSetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_inventories_set', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('item_id')->unsigned()->index();
			$table->integer('from_node_id')->unsigned();
			$table->integer('node_id')->unsigned();
			$table->tinyInteger('is_confirmed');
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
		Schema::drop('eq_inventories_set');
	}

}
