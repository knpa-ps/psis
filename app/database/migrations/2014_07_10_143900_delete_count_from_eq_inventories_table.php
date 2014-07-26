<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteCountFromEqInventoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('eq_inventories', function(Blueprint $table) {
			$table->dropColumn('count');
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
			$table->integer('count');
		});
	}

}
