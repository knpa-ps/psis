<?php

class EqItemDiscardData extends Eloquent {
	protected $table = 'eq_item_discard_data';

	protected $guarded = array();

	public static $rules = array();

	public function discardSet() {
		return $this->belongsTo('EqItemDiscardSet','discard_set_id','id');
	}
}
