<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqDeleteRequestTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_delete_request', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('usage_id');
			$table->string('type');
			$table->tinyinteger('confirmed');
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
		Schema::drop('eq_delete_request');
	}

}
