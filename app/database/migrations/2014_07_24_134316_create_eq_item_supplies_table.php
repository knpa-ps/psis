<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqItemSuppliesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_item_supplies', function(Blueprint $table) {
			$table->increments('id');

			$table->integer('supply_set_id')->unsigned()->index();
			$table->integer('item_type_id')->unsigned()->index();
			$table->integer('count')->unsigned();
			$table->integer('to_node_id')->unsigned()->index();
			$table->tinyInteger('is_closed')->default(0);
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
		Schema::drop('eq_item_supplies');
	}

}
