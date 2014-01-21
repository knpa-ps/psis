<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_permissions', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('action_id')->unsigned();
			$table->string('key', 255);
			$table->text('description');
			$table->timestamps();

			$table->unique('key');
			$table->foreign('action_id')
			->references('id')
			->on('module_actions')
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
		Schema::dropIfExists('action_permissions');
	}

}
