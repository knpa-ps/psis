<?php

class EqDeleteRequest extends Eloquent {

	protected $table = 'eq_delete_request';

	protected $guarded = array();

	public static $rules = array();


	public function capEvent() {
		return $this->belongsTo('EqCapsaicinUsage', 'usage_id', 'id');
	}

	public function pavaEvent() {
		return $this->belongsTo('EqWaterPavaEvent','usage_id','id');
	}
}
