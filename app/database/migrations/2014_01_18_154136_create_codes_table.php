<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('codes_categories');
		Schema::dropIfExists('codes');

		Schema::create('codes_categories', function(Blueprint $table) {
			$table->increments('id');
			$table->string('category_code', 4);
			$table->string('name', 100);
			$table->text('description');
			$table->integer('sort_order');
			$table->smallInteger('is_public');
			$table->timestamps();
			$table->softDeletes();
			$table->unique('category_code');
			$table->index('sort_order');
		});

		Schema::create('codes', function(Blueprint $table) {
			$table->increments('id');
			$table->string('category_code', 4);
			$table->string('code', 4);
			$table->string('title', 100);
			$table->text('group_ids');
			$table->integer('sort_order');
			$table->smallInteger('visible');
			$table->unique('code');
			$table->timestamps();
			$table->softDeletes();
			$table->unique(array('category_code', 'code'));
			$table->index('sort_order');
			$table->foreign('category_code')
			->references('category_code')
			->on('codes_categories')
			->onDelete('cascade')
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
		Schema::dropIfExists('codes');
		Schema::dropIfExists('codes_categories');
	}

}
