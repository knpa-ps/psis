<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteItemTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('eq_item_types');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('eq_item_types', function(Blueprint $table) {
			$table->increments('id');
			$table->string('type');
			$table->integer('count');
			$table->integer('inventory_id')->unsigned()->index();
			$table->timestamps();
		});
	}

}
