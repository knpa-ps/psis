<?php

class EqCapsaicinEvent extends Eloquent {

	protected $table = "eq_capsaicin_event";

	protected $guarded = array();

	public static $rules = array();

	public function children(){
		return $this->hasMany('EqCapsaicinUsage','event_id','id');
	}
}
