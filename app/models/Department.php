<?php

class Department extends Eloquent {

	protected $table = 'departments';

	protected $guarded = array();

	public static $rules = array();

	public static function children($parentId) 
	{
		return self::where('parent_id', '=', $parentId)->where('is_alive', '=', 1)->get();
	}	

	public function parseFullName()
	{
		return trim(str_replace(':', ' ', $this->full_name));
	}
}
