<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePsReportHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ps_reports_history', function(Blueprint $table) {
			$table->increments('id');
			$table->longText('content');
			$table->text('file_ids');
			$table->integer('creator_id')->unsigned();
			$table->integer('report_id')->unsigned();
			$table->softDeletes();
			$table->timestamps();

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
		Schema::drop('ps_reports_history');
	}

}
