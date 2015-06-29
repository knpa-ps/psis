<?php

class EqDeleteRequest extends Eloquent {

	protected $table = 'eq_delete_request';

	protected $guarded = array();

	public static $rules = array();


	public function event() {
		switch ($this->type) {
			case 'cap':
				return $this->belongsTo('EqCapsaicinUsage', 'usage_id', 'id');
				break;
			case 'pava':
				return $this->belongsTo('EqWaterPavaEvent', 'usage_id', 'id');
				break;
			default:
				return null;
				break;
		}
	}
}
