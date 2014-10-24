<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddMakerInfoToEqItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_items', function(Blueprint $table) {
			$table->string('maker_name');
			$table->string('maker_phone');
			$table->date('acquired_date');
			$table->dropColumn('unit');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_items', function(Blueprint $table) {
			$table->dropColumn('maker_name');
			$table->dropColumn('maker_phone');
			$table->dropColumn('acquired_date');
			$table->string('unit');
		});
	}

}
