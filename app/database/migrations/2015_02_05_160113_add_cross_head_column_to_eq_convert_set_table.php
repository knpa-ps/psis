<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCrossHeadColumnToEqConvertSetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_convert_set', function(Blueprint $table) {
			$table->boolean('cross_head');
			$table->boolean('head_confirmed');
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
			if (Schema::hasColumn('eq_convert_set', 'cross_head')) 
			{
				$table->dropColumn('cross_head');
			}
			if (Schema::hasColumn('eq_convert_set', 'head_confirmed')) 
			{
				$table->dropColumn('head_confirmed');
			}
		});
	}

}
