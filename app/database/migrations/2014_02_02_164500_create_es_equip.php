<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEsEquip extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('es_equip', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('category_id')->unsigned();
			$table->string('name', 255);
			$table->timestamps();
			$table->index('category_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('es_equip');
	}

}
