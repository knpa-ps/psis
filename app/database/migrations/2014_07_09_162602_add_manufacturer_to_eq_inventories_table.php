<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddManufacturerToEqInventoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_inventories', function(Blueprint $table) {
			$table->string('manufacturer');
			$table->string('manufacturer_phone');
			$table->string('manufacturer_address');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_inventories', function(Blueprint $table) {
			$table->dropColumn('manufacturer');
			$table->dropColumn('manufacturer_phone');
			$table->dropColumn('manufacturer_address');
		});
	}

}
