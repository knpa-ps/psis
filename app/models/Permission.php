<?php

class Permission extends Eloquent {

	protected $table = 'module_permissions';

	protected $guarded = array();

	public static $rules = array();

	public function action()
	{
		return $this->belongsTo('Action', 'action_id', 'id');
	}
}
