<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqSupplyManagerNodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_supply_manager_nodes', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('manager_id')->unsigned()->nullable();
			$table->integer('parent_id')->unsigned()->nullable();
			$table->string('node_name', 50);
			$table->string('full_path', 255);
			$table->smallInteger('is_terminal');
			$table->string('full_name', 255);
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('eq_supply_manager_nodes');
	}

}
