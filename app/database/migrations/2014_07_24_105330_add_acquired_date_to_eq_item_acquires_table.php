<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAcquiredDateToEqItemAcquiresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_item_acquires', function(Blueprint $table) {
			$table->date('acquired_date');
			$table->dropColumn('node_from_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_item_acquires', function(Blueprint $table) {
			$table->dropColumn('acquired_date');
			$table->integer('node_from_id')->unsigned();
		});
	}

}
