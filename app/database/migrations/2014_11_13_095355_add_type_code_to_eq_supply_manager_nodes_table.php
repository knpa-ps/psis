<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTypeCodeToEqSupplyManagerNodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_supply_manager_nodes', function(Blueprint $table) {
			$table->string('type_code', 4)->index();
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
			$table->dropColumn('type_code');
		});
	}

}
