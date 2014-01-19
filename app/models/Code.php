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

	public static function withCategory($categoryCode) 
	{
		return self::where('category_code', '=', $categoryCode)->get();
	}
}