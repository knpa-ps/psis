<?php

class User extends Cartalyst\Sentry\Users\Eloquent\User {

	public function rank()
	{
		return $this->belongsTo('Code', 'user_rank', 'code')->where('category_code', '=', 'H001');
	}

	public function department()
	{
		return $this->belongsTo('Department', 'dept_id', 'id');
	}

	public function groups()
	{
		return $this->belongsToMany('Group', 'users_groups', 'user_id', 'group_id');
	}

	public static function table()
	{
		return DB::table('users')->leftJoin('codes', function($query){
			$query->on('codes.code','=','users.user_rank')
			->where('codes.category_code', '=', 'H001');
		})->leftJoin('departments', 'departments.id','=','users.dept_id');
	}
}
