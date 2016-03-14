<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EventReports extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_reports', function(Blueprint $table) {
			$table->increments('id');
			$table->string('title', 1024);
			$table->tinyInteger('closed');
			$table->integer('creator_id')->unsigned();
			$table->integer('dept_id')->unsigned();
			$table->softDeletes();
			$table->nullableTimestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('event_reports');
	}

}
