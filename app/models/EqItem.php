<?php

class EqItem extends Eloquent {
	protected $table = 'eq_items';

	public function category() {
		return $this->belongsTo('EqCategory', 'category_id', 'id');
	}

	public function details() {
		return $this->hasMany('EqItemDetails', 'item_id', 'id');
	}
}
