<?php
class DatabaseSeeder extends Seeder {
	
	private $tasks = array();

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

       	$this->call('GroupsTableSeeder');
		$this->call('UsersTableSeeder');

		$this->addTask('codes_categories');
		$this->addTask('codes');
		$this->addTask('configs');
		$this->addTask('menus');
		$this->addTask('departments');
		$this->addTask('permissions');

		$this->readAndSeed();
	}

	private function readAndSeed()
	{
		foreach ($this->tasks as $task) 
		{
			$this->doSeed($task);
		}
	}

	private function addTask($table)
	{
		$this->tasks[] = $table;
	}

	private function doSeed($table)
	{
		$fileName = app_path().'/database/seeds/data/'.$table.'.js';
		
		$data = json_decode(File::get($fileName), true);
		if ($data === NULL)
		{
			throw new Exception("json error : ".json_last_error());
		}

		DB::beginTransaction();

		try
		{
			DB::table($table)->delete();
			
			foreach ($data as $row) 
			{
				DB::table($table)->insert($row);
			}

			DB::commit();
			ob_start();
			echo "Seeded: ".$table.PHP_EOL;
			ob_flush();
		}
		catch (\Exception $e)
		{
			DB::rollBack();
			throw $e;
		}
	}
}
