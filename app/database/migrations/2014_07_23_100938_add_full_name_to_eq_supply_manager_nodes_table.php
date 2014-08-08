<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFullNameToEqSupplyManagerNodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_supply_manager_nodes', function(Blueprint $table) {
			$table->string('full_name', 255);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_supply_manager_nodes', function(Blueprint $table) {
			$table->dropColumn('full_name');
		});
	}

}
