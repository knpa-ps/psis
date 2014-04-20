<?php

class Code extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'codes';

	public function category() 
	{
		return $this->belongsTo('CodeCategory', 'category_code', 'category_code');
	}

	public function scopeVisible($query) {
		return $query->where('visible', '=', 1);
	}

	/**
	 * @deprecated use in($categoryCode)
	 * @param  [type] $categoryCode
	 * @return [type]
	 */
	public static function withCategory($categoryCode) 
	{
		return self::where('category_code', '=', $categoryCode)->where('visible','=','1')->get();
	}

	public static function in($categoryCode)
	{
		return self::where('category_code', '=', $categoryCode)->where('visible','=','1')->get();
	}
}