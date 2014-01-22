<?php

class User extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public function rank()
	{
		return $this->belongsTo('Code', 'user_rank', 'code')->where('category_code', '=', 'H001');
	}
}
