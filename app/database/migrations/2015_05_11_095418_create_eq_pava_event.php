<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqPavaEvent extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_pava_event', function(Blueprint $table) {
			$table->increments('id');
			$table->string('type_code');
			$table->string('event_name');
			$table->integer('node_id')->unsigned();
			$table->string('location');
			$table->date('date');
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
		Schema::drop('eq_pava_event');
	}

}
