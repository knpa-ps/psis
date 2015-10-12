<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteCauseFromEqItemDiscardSetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_item_discard_set', function(Blueprint $table) {
			$table->dropColumn('cause');
			$table->string('file_name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_item_discard_set', function(Blueprint $table) {
			$table->dropColumn('file_name');
			$table->string('cause');
		});
	}

}
