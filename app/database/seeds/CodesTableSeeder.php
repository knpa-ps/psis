<?php

class CodesTableSeeder extends FileSeeder {

	protected function setUpTasks()
	{
		$this->addTask('codes_categories');
		$this->addTask('codes');
	}
}
