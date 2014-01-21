<?php

class User extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public function rank()
	{
		return $this->hasOne('Code', 'code', 'user_rank');
	}

	public function department()
	{

	}

	public function status()
	{

	}

	public function withAll()
	{
		return $this->with(array(
				'rank'=> function($query) {

				},
				'department'=> function($query) {

				},
				'status' => function($query) {

				}	
			));
	}
}
