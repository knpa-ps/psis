<?php

class EqCapsaicinUsage extends Eloquent {

	protected $table = 'eq_capsaicin_usage';

	protected $guarded = array();

	public static $rules = array();

	public function event() {
		return $this->belongsTo('EqCapsaicinEvent','event_id','id');
	}
}