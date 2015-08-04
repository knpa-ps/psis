<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqInventoriesDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_inventories_data', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('inventory_set_id')->unsigned()->index();
			$table->integer('item_type_id')->unsigned()->index();
			$table->integer('count')->unsigned();
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
		Schema::drop('eq_inventories_data');
	}

}
