<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddApprovedDateToEqConvertSetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_convert_set', function(Blueprint $table) {
			$table->date('confirmed_date')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_convert_set', function(Blueprint $table) {
			$table->dropColumn('confirmed_date');
		});
	}

}
