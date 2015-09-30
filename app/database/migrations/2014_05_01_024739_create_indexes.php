<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIndexes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('departments', function(Blueprint $table) {
			$table->index('parent_id');
			$table->index('full_path');
			$table->index('sort_order');
		});
		Schema::table('ps_reports', function(Blueprint $table) {
			$table->index('creator_id');
			$table->index('created_at');
		});
		Schema::table('ps_reports_history', function(Blueprint $table) {
			$table->index('creator_id');
			$table->index('report_id');
			$table->index('created_at');
		});
		Schema::table('users', function(Blueprint $table) {
			$table->index('dept_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('departments', function(Blueprint $table) {
			$table->dropColumn('parent_id');
			$table->dropColumn('full_path');
			$table->dropColumn('sort_order');
		});

		Schema::table('ps_reports', function(Blueprint $table) {
			$table->dropColumn('creator_id');
			$table->dropColumn('created_at');
		});

		Schema::table('ps_reports_history', function(Blueprint $table) {
			$table->dropColumn('creator_id');
			$table->dropColumn('report_id');
			$table->dropColumn('created_at');
		});
		Schema::table('users', function(Blueprint $table) {
			$table->dropColumn('dept_id');
		});
	}

}
