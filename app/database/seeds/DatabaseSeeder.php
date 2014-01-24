<?php
include('FileSeeder.php');
class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('DepartmentsTableSeeder');

        $this->call('GroupsTableSeeder');
		$this->call('UsersTableSeeder');
		$this->call('CodesTableSeeder');
		
		$this->call('ModulesTableSeeder');
		$this->call('MenusTableSeeder');
		$this->call('ModuleActionsTableSeeder');
		$this->call('ModulePermissionsTableSeeder');
	}

}