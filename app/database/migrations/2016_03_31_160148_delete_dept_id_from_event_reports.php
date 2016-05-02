<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteDeptIdFromEventReports extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('event_reports', function(Blueprint $table) {
			$table->dropColumn('dept_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('event_reports', function(Blueprint $table) {
			$table->integer('dept_id')->unsigned();
		});
	}

}
