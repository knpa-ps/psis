<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteAmountColumnsFromEqWaterpavaEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_waterpava_event', function(Blueprint $table) {
			$table->dropColumn('warn_amount');
			$table->dropColumn('direct_amount');
			$table->dropColumn('high_angle_amount');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_waterpava_event', function(Blueprint $table) {
			$table->float('warn_amount');
			$table->float('direct_amount');
			$table->float('high_angle_amount');
		});
	}

}
