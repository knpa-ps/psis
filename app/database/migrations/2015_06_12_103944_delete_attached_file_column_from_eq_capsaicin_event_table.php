<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteAttachedFileColumnFromEqCapsaicinEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_capsaicin_event', function(Blueprint $table) {
			$table->dropColumn('attached_file_name');
		});
		Schema::table('eq_capsaicin_usage', function(Blueprint $table) {
			$table->string('attached_file_name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_capsaicin_event', function(Blueprint $table) {
			$table->string('attached_file_name');
		});
		Schema::table('eq_capsaicin_usage', function(Blueprint $table) {
			$table->dropColumn('attached_file_name');
		});
	}

}
