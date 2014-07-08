<?php

class EqInventory extends Eloquent {
	protected $table = 'eq_inventories';

	public function creator() {
		return $this->belongsTo('User', 'creator_id', 'id');
	}

	public function item() {
		return $this->belongsTo('EqItem', 'item_id', 'id');
	}

	public function department() {
		return $this->belongsTo('Department', 'dept_id', 'id');
	}

	public function supplies() {
		return $this->hasMany('EqSupply', 'inventory_id', 'id');
	}
}
