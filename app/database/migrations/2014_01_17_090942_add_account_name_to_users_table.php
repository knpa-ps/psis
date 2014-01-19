<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAccountNameToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('users')) {
			Schema::table('users', function(Blueprint $table) {
				$table->string('account_name', 255);
	            $table->integer('dept_id');
	            $table->smallInteger('user_rank');
	            $table->string('user_name', 20);
	            $table->unique('account_name');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table) {
			if (Schema::hasColumn('users', 'account_name')) 
			{
				$table->dropColumn('account_name');
			}
			if (Schema::hasColumn('users', 'dept_id')) 
			{
				$table->dropColumn('dept_id');
			}
			if (Schema::hasColumn('users', 'user_rank')) 
			{
				$table->dropColumn('user_rank');
			}
			if (Schema::hasColumn('users', 'user_name')) 
			{
				$table->dropColumn('user_name');
			}
		});
	}

}
