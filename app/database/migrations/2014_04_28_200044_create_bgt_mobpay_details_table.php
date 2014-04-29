<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBgtMobpayDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bgt_mobpay_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('master_id')->unsigned()->index();
			$table->integer('dept_id')->unsigned()->index();
			$table->string('rank_code', 10);
			$table->string('name', 20);
			$table->dateTime('start');
			$table->dateTime('end');
			$table->decimal('amount', 15, 0);
			$table->softDeletes();
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
		Schema::drop('bgt_mobpay_details');
	}

}
