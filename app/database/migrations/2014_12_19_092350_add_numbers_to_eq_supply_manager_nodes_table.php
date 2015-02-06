<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddNumbersToEqSupplyManagerNodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_supply_manager_nodes', function(Blueprint $table) {
			$table->integer('personnel');
			$table->integer('capacity');
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
			if (Schema::hasColumn('eq_supply_manager_nodes', 'personnel')) 
			{
				$table->dropColumn('personnel');
			}
			
			if (Schema::hasColumn('eq_supply_manager_nodes', 'capacity')) 
			{
				$table->dropColumn('capacity');
			}
		});
	}

}
