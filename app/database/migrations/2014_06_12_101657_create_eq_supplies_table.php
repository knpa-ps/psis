<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqSuppliesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_supplies', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('supply_dept_id')->unsigned()->index();
			$table->integer('creator_id')->unsigned()->index();
			$table->integer('item_id')->unsigned()->index();

			$table->date('supply_date')->index();

			$table->string('title');
			$table->text('description');

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
		Schema::drop('eq_supplies');
	}

}
