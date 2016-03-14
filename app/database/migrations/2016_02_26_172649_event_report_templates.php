<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EventReportTemplates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	 public function up()
 	{
 		Schema::create('event_report_templates', function(Blueprint $table)
 		{
 			$table->increments('id');
 			$table->string('name');
 			$table->longText('content');
 			$table->tinyInteger('is_default');
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
 		Schema::drop('event_report_templates');
 	}

 }
