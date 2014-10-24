<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqConvertSetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_convert_set', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('item_id');
			$table->integer('from_node_id');
			$table->integer('target_node_id');
			$table->date('converted_date');
			$table->string('explanation');
			$table->tinyInteger('is_confirmed');	
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
		Schema::drop('eq_convert_set');
	}

}
