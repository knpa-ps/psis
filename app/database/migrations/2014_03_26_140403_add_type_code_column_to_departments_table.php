<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTypeCodeColumnToDepartmentsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('departments', function(Blueprint $table) {
			$table->string('type_code', 4)->index();
		});
	}
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('departments', function(Blueprint $table) {
			$table->dropColumn('type_code');
		});
	}
}
