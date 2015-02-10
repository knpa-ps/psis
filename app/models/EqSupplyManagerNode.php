<?php

class EqSupplyManagerNode extends Eloquent {

	protected $table = 'eq_supply_manager_nodes';

	protected $guarded = array();

	public static $rules = array();

	public function supplies(){
		return $this->hasMany('EqItemSupplySet', 'from_node_id', 'id');
	}

	public function manager() {
		return $this->belongsTo('User', 'manager_id', 'id');
	}

	public function parent() {
		return $this->belongsTo('EqSupplyManagerNode', 'parent_id', 'id');
	}

	public function children() {
		return $this->hasMany('EqSupplyManagerNode', 'parent_id', 'id');
	}
	/**
	 * 해당 부서의 부모 노드 중 관리자가 존재하는 가장 가까운 노드를 리턴한다
	 * @return EqSupplyManagerNode
	 */
	public function managedParent() {
		return $this->belongsTo('EqSupplyManagerNode', 'parent_manager_node','id');
	}
	/**
	 * 해당 부서의 자식 노드 중 관리자가 존재하는 가장 가까운 노드들을 리턴한
	 * @return EqSupplyManagerNode
	 */
	public function managedChildren() {
		return $this->hasMany('EqSupplyManagerNode','parent_manager_node','id');
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

		return EqSupplyManagerNode::find($paths[1]);
	}

	public function scopeRegions($query) {
		return $query->whereNull('parent_id');
	}
}
