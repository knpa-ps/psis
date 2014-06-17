<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddApprovedToModUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mod_user', function(Blueprint $table)
		{
			$table->integer('approved');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mod_user', function(Blueprint $table)
		{
			$table->dropColumn('approved');
		});
	}

}
