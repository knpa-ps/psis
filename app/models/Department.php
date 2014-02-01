<?php

class Department extends Eloquent {

	protected $table = 'departments';

	protected $guarded = array();

	public static $rules = array();

	public static function children($parentId) 
	{
		return self::where('parent_id', '=', $parentId)->where('is_alive', '=', 1)->orderBy('sort_order', 'asc')->get();
	}	

	public function parseFullName()
	{
		return trim(str_replace(':', ' ', $this->full_name));
	}

	public static function region($deptId)
	{
		$dept = self::where('id','=',$deptId)->first();
		$paths = explode(':', trim($dept->full_path, ':'));
		if (count($paths) == 0) 
		{
			return NULL;
		}
		
		$regionId = $paths[0];

		return self::where('id', '=', $regionId)->first();
	}

	public static function regions()
	{
		return self::where('parent_id', '=', 0)->where('id', '!=', 1)->orderBy('sort_order','asc')->get();
	}
}
