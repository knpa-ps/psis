<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteEqCapsaicinInventoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('eq_capsaicin_inventory');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('eq_capsaicin_inventory', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('node_id');
			$table->float('stock');
			$table->timestamps();
		});
	}

}
