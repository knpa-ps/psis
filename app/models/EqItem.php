<?php

class EqItem extends Eloquent {
	protected $table = 'eq_items';

	public function category() {
		return $this->belongsTo('EqCategory', 'category_id', 'id');
	}

	public function details() {
		return $this->hasMany('EqItemDetail', 'item_id', 'id');
	}

	public function inventories() {
		return $this->hasMany('EqInventory', 'item_id', 'id');
	}

	public function images() {
		return $this->hasMany('EqItemImage', 'item_id', 'id');
	}

	public function getPersistYearsAttribute($value) {
		return $value.'년';
	}
}
