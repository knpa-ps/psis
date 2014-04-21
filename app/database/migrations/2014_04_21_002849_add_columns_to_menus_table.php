<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnsToMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('menus', function(Blueprint $table)
		{
			$table->string('browser_title');
			$table->tinyInteger('type')->index();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('menus', function(Blueprint $table)
		{
			$table->dropColumn('browser_title');
			$table->dropColumn('type');
		});
	}

}
