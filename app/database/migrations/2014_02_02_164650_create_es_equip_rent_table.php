<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEsEquipRentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('es_equip_rent', function(Blueprint $table) {
			$table->increments('id');
			$table->date('rent_date');
			$table->integer('equip_id')->unsigned();
			$table->integer('dept_id')->unsigned();
			$table->text('usage')->unsigned();
			$table->string('in_charge', 255);
			$table->date('return_date');
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
		Schema::drop('es_equip_rent');
	}

}
