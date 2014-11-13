<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqCapsaicinFirstdayTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_capsaicin_firstday', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('node_id');
			$table->string('year');
			$table->float('amount');
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
		Schema::drop('eq_capsaicin_firstday');
	}

}
