<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEsEquipInventoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('es_equip_inventory', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('equip_id')->unsigned();
			$table->integer('dept_id')->unsigned();
			$table->integer('creator_id')->unsigned();
			$table->integer('count');
			$table->date('acq_date');
			$table->smallInteger('acq_route');
			$table->text('acq_route_etc');
			$table->string('lifetime', 100);
			$table->smallInteger('usable');
			$table->text('not_usable_reason');
			$table->timestamps();
			
			$table->index('equip_id');
			$table->index('dept_id');
			$table->index('creator_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('es_equip_inventory');
	}

}
