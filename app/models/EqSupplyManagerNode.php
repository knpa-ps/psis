<?php

class EqSupplyManagerNode extends Eloquent {

	protected $table = 'eq_supply_manager_nodes';

	protected $guarded = array();

	public static $rules = array();

	public function supplies(){
		return $this->hasMany('EqItemSupplySet', 'from_node_id', 'id');
	}

	public function manager() {
		return $this->hasMany('User', 'manager_id', 'id');
	}

	public function parent() {
		return $this->belongsTo('EqSupplyManagerNode', 'parent_id', 'id');
	}

	public function children() {
		return $this->hasMany('EqSupplyManagerNode', 'parent_id', 'id');
	}

	/**
	 * 해당 부서가 속한 지방청에 대한 Node 모델을 불러온다.
	 * @return Node 지방청
	 */
	public function region() {
		$paths = explode(':', trim($this->full_path, ':'));

		if (count($paths)==0) {
			return null;
		}

		return Department::find($paths[0]);
	}

	public function scopeRegions($query) {
		return $query->whereNull('parent_id');
	}
}
