<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqSupplyDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_supply_details', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('supply_id')->unsigned()->index();
			$table->integer('target_dept_id')->unsigned()->index();
			$table->integer('count')->unsigned();
			
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
		Schema::drop('eq_supply_details');
	}

}
