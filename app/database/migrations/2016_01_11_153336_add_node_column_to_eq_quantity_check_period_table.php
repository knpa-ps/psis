<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNodeColumnToEqQuantityCheckPeriodTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_quantity_check_period', function(Blueprint $table) {
			$table->integer('node_id')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_quantity_check_period', function(Blueprint $table) {
			$table->dropColumn('node_id');
		});
	}

}
