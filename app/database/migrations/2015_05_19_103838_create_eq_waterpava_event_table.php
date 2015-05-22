<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqWaterpavaEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_waterpava_event', function(Blueprint $table) {
			$table->increments('id');
			$table->string('event_name');
			$table->integer('node_id')->unsigned();
			$table->string('location');
			$table->date('date');
			$table->float('warn_amount');
			$table->float('direct_amount');
			$table->float('high_angle_amount');
			$table->float('pava_amount')->nullable();
			$table->float('dye_amount')->nullable();
			$table->string('attached_file_name')->default('');
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
		Schema::drop('eq_waterpava_event');
	}

}
