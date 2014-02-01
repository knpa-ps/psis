<?php

class PSConfig extends Eloquent {
	protected $table = 'configs';
	protected $guarded = array();

	public static $rules = array();

	public static function category($category)
	{
		$result = self::where('key','like',"$category.%")->get();
		$configs = array();
		foreach ($result as $row)
		{
			$configs[$row->key] = $row->value;
		} 
		return $configs;
	}
}
