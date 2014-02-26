<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddContactsColumnsToUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table) {
			
	            $table->string('contact', 255);
				$table->string('contact_extension', 255);
	            $table->string('contact_phone', 255);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table) {
			if (Schema::hasColumn('users', 'contact')) 
			{
				$table->dropColumn('contact');
			}
			
			if (Schema::hasColumn('users', 'contact_extension')) 
			{
				$table->dropColumn('contact_extension');
			}
			
			if (Schema::hasColumn('users', 'contact_phone')) 
			{
				$table->dropColumn('contact_phone');
			}
		});		
	}

}
