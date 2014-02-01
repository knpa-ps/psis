<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('parent_id')->nullable();
			$table->string('name', 100);
			$table->text('action');
			$table->text('url');
			$table->smallInteger('is_shortcut');
			$table->text('group_ids');
			$table->integer('sort_order');
			$table->timestamps();
			$table->index('parent_id');
			$table->index('sort_order');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('menus');
	}

}
