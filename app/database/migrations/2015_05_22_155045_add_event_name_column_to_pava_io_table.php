<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddEventNameColumnToPavaIoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_pava_io', function(Blueprint $table) {
			$table->string('event_name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_pava_io', function(Blueprint $table) {
			$table->dropColumn('event_name');
		});
	}

}
