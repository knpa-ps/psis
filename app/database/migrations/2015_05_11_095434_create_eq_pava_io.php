<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqPavaIo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_pava_io', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('node_id')->unsigned()->index();
			$table->float('amount')->unsigned();
			$table->date('acquired_date');
			$table->string('caption');
			$table->tinyInteger('io');
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
		Schema::drop('eq_pava_io');
	}

}
