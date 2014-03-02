<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBgMobTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bg_mob', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('dept_id')->unsigned();
			$table->string('rank_code', 10);
			$table->string('receiver_name', 10);
			$table->date('mob_date');
			$table->time('start_time');
			$table->time('end_time');
			$table->string('mob_code', 10);
			$table->text('mob_summary');
			$table->decimal('amount', 10, 1);
			$table->smallInteger('actual');
			$table->integer('creator_id')->unsigned();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bg_mob');
	}

}
