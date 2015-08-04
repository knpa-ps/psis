<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqItemCodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_item_code', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('category_id')->unsigned()->index();
			$table->string('code');
			$table->string('title');
			$table->smallInteger('sort_order');
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
		Schema::drop('eq_item_code');
	}

}
