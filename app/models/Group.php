<?php

class Group extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public function scopeDefaults($query) {
		return $query->where('default', '=', 1);
	}
}
