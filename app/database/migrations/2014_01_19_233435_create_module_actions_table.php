<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModuleActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('module_actions', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('module_id')->unsigned();
			$table->integer('menu_id')->unsigned()->nullable();
			$table->smallInteger('menu_default');
			$table->string('action', 255);
			$table->string('name', 255);
			$table->integer('type');
			$table->timestamps();
			$table->unique(array('module_id', 'action'));

			$table->foreign('module_id')
			->references('id')
			->on('modules')
			->onDelete('cascade')
			->onUpdate('cascade');

			$table->foreign('menu_id')
			->references('id')
			->on('menus')
			->onDelete('set null')
			->onUpdate('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('module_actions');
	}

}
