<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqCapsaicinIoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_capsaicin_io', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('node_id')->unsigned()->index();
			$table->float('amount')->unsigned();
			$table->date('acquired_date');
			$table->string('caption');
			$table->tinyInteger('io');
			$table->timestamps();
		});

		Schema::drop('eq_capsaicin_acquire');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('eq_capsaicin_io');

		Schema::create('eq_capsaicin_acquire', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('node_id')->unsigned();
			$table->float('amount')->unsigned();
			$table->date('acquired_date');
			$table->string('caption');	
			$table->timestamps();
		});
	}

}
