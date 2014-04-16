<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PsReports extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ps_reports', function(Blueprint $table) {
			$table->increments('id');
			$table->string('title', 1024);
			$table->smallInteger('closed');
			$table->integer('creator_id')->unsigned();
			$table->softDeletes();
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
		Schema::dropIfExists('ps_reports');
	}

}
