<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddWreckedToEqInventoriesDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_inventories_data', function(Blueprint $table) {
			$table->integer('wrecked')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_inventories_data', function(Blueprint $table) {
			$table->dropColumn('wrecked');
		});
	}

}
