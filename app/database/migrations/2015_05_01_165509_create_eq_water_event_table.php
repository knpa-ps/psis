<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqWaterEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_water_event', function(Blueprint $table) {
			$table->increments('id');
			$table->string('event_name');
			$table->integer('node_id')->unsigned();
			$table->string('location');
			$table->date('date');
			$table->float('amount');
			$table->string('attached_file_name');
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
		Schema::drop('eq_water_event');
	}

}
