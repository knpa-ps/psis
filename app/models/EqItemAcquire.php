<?php

class EqItemAcquire extends Eloquent {

	protected $table = 'eq_item_acquires';

	protected $guarded = array();

	public static $rules = array();

	public function item() {
		return $this->belongsTo('EqItem', 'item_id', 'id');
	}
	
}
