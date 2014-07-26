<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqItemSuppliesSetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_item_supplies_set', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('item_id')->unsigned()->index();
			$table->integer('creator_id')->unsigned()->index();
			$table->integer('confirmer_id')->unsigned()->nullable();
			$table->integer('from_node_id')->unsigned()->index();
			$table->date('supplied_date');
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
		Schema::drop('eq_item_supplies_set');
	}

}
