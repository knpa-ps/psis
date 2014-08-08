<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEqItemSurveyResponsesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eq_item_survey_responses', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('survey_id')->unsigned()->index();
			$table->integer('node_id')->unsigned()->index();
			$table->integer('creator_id')->unsigned()->index();
			$table->integer('item_type_id')->unsigned()->index();
			$table->integer('count')->unsigned();
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
		Schema::drop('eq_item_survey_responses');
	}

}
