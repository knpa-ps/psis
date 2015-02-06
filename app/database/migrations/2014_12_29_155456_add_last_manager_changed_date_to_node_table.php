<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddLastManagerChangedDateToNodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_supply_manager_nodes', function(Blueprint $table) {
			$table->date('last_manager_changed_date');	
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
			$table->dropColumn('last_manager_changed_date');
		});	
	}

}
