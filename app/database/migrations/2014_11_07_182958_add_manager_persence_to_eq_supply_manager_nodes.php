<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddManagerPersenceToEqSupplyManagerNodes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_supply_manager_nodes', function(Blueprint $table) {
			$table->integer('parent_manager_node')->nullable();
			$table->tinyInteger('is_selectable');
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
			$table->dropColumn('parent_manager_node');
			$table->dropColumn('is_selectable');
		});
	}

}
