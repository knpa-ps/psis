<?php

class EqItemDetail extends Eloquent {
	protected $table = 'eq_item_details';

	public function creator() {
		return $this->belongsTo('User', 'creator_id', 'id');
	}

	public function item() {
		return $this->belongsTo('EqItem', 'item_id', 'id');
	}
}
