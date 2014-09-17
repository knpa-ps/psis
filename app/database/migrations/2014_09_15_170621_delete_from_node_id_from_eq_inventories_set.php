<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteFromNodeIdFromEqInventoriesSet extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_inventories_set', function(Blueprint $table) {
			$table->dropColumn('from_node_id');
			$table->dropColumn('is_confirmed');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('eq_inventories_set', function(Blueprint $table) {
			$table->integer('from_node_id')->unsigned();
			$table->tinyInteger('is_confirmed');
		});
	}

}
