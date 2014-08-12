<?php

class EqItem extends Eloquent {
	protected $table = 'eq_items';

	public function category() {
		return $this->belongsTo('EqCategory', 'category_id', 'id');
	}

	public function details() {
		return $this->hasMany('EqItemDetail', 'item_id', 'id');
	}

	public function images() {
		return $this->hasMany('EqItemImage', 'item_id', 'id');
	}

	public function types() {
		return $this->hasMany('EqItemType', 'item_id', 'id');
	}

	public function inventories() {
		return $this->hasMany('EqInventorySet','item_id','id');
	}

	public function acquires() {
		return $this->hasMany('EqItemAcquire', 'item_id', 'id');
	}

	public function getPersistYearsAttribute($value) {
		return $value.'년';
	}
}
