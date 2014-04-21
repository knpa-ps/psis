<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePsReportTempTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ps_reports_temp', function(Blueprint $table) {
			$table->increments('id');
			$table->string('title', 1024);
			$table->text('content');
			$table->integer('creator_id')->unsigned();
			$table->timestamps();

			$table->index('creator_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ps_report_temp');
	}

}
