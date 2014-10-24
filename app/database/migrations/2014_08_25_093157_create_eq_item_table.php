<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqItemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_items', function(Blueprint $table) {
			$table->increments('id');
			$table->string('classification');
			$table->string('item_code');
			$table->string('maker_name');
			$table->string('maker_phone');
			$table->date('acquired_date');
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
