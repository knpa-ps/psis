<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDeptIdColumnToReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ps_reports', function(Blueprint $table)
		{
			$table->integer('dept_id')->unsigned()->index();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ps_reports', function(Blueprint $table)
		{
			$table->dropColumn('dept_id');
		});
	}

}
