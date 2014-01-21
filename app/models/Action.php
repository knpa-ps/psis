<?php

class Action extends Eloquent {

	protected $table = 'module_actions';

	protected $guarded = array();

	public static $rules = array();

	public function module()
	{
		return $this->belongsTo('Module', 'module_id', 'id');
	}

	public static function info($actionKey)
	{
		return self::where('action', '=', $actionKey)->first();
	}
}
