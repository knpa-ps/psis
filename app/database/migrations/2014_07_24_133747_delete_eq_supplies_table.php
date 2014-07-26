<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteEqSuppliesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_supplies', function(Blueprint $table) {
			Schema::drop('eq_supplies');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
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

}
