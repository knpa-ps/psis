<?php

class User extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

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

	public function withAll()
	{
		return $this->with('rank', 'department', 'groups');
	}
}
