<?php

class ModUserGroup extends Eloquent {
	protected $table = 'mod_usergroup';
	protected $guarded = array('created_at', 'updated_at');

	public static $rules = array();

	public function user() 
	{
		return $this->belongsTo('User','user_id','id');
	}
}