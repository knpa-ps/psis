<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDepartmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('departments')) {
			Schema::create('departments', function(Blueprint $table) {
				$table->increments('id');
				$table->integer('parent_id');
				$table->smallInteger('depth');
				$table->string('dept_name', 50);
				$table->string('full_path', 255);
				$table->string('full_name', 1024);
				$table->smallInteger('is_alive');
				$table->smallInteger('is_terminal');
				$table->timestamps();
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
		Schema::dropIfExists('departments');
	}

}
