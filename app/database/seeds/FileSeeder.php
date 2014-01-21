<?php

abstract class FileSeeder extends Seeder {

	private $tasks = array();

	public function run()
	{
		$this->setUpTasks();
		foreach ($this->tasks as $task) 
		{
			$this->doSeed($task);
		}
	}

	abstract protected function setUpTasks();

	protected function addTask($table)
	{
		$this->tasks[] = $table;
	}

	private function doSeed($table)
	{
		$fileName = app_path().'/database/seeds/data/'.$table.'.js';
		
		$data = json_decode(File::get($fileName), true);
		if ($data === NULL)
		{
			throw new Exception(json_last_error());
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
		}
		catch (\Exception $e)
		{
			DB::rollBack();
			throw $e;
		}
	}
}
