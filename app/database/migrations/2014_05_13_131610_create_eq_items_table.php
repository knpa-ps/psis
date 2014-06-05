<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_items', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('category_id')->unsigned()->index();
			$table->string('name');
			$table->string('standard');
			$table->string('unit');
			$table->tinyInteger('is_active');
			$table->smallInteger('persist_years')->unsigned();
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
		Schema::drop('eq_items');
	}

}
