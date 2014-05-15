<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqInventoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_inventories', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('item_id')->unsigned()->index();
			$table->integer('dept_id')->unsigned()->index();
			$table->integer('creator_id')->unsigned()->index();
			
			$table->integer('count')->unsigned();

			$table->string('model_name');

			$table->date('acq_date');
			$table->string('acq_route');

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
		Schema::drop('eq_inventories');
	}

}
