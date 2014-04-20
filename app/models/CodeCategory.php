<?php

class CodeCategory extends Eloquent {

	protected $table = 'codes_categories';

	protected $guarded = array();

	public static $rules = array();

	public function codes()
    {
        return $this->hasMany('Code', 'category_code', 'category_code');
    }

    public function scopeOfName($query, $name) {
    	return $query->where('category_code', '=', $name);
    }
}
