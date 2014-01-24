<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangePermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('action_permissions');
		Schema::create('module_permissions', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('module_id')->unsigned();
			$table->string('key', 255);
			$table->text('description');
			$table->timestamps();

			$table->unique('key');
			$table->foreign('module_id')
			->references('id')
			->on('modules')
			->onUpdate('cascade')
			->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('permission');
	}

}
