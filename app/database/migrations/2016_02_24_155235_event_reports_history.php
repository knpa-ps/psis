<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EventReportsHistory extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_reports_history', function(Blueprint $table) {
			$table->increments('id');
			$table->longText('content');
			$table->text('file_ids');
			$table->integer('creator_id')->unsigned();
			$table->integer('report_id')->unsigned();
			$table->softDeletes();
			$table->nullableTimestamps();

			$table->index('creator_id');
			$table->index('report_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('event_reports_history');
	}

}
