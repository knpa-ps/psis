<?php

class EquipController extends BaseController {
	protected $service;

	public function __construct() {
		$this->service = new EqService;
	}

	/**
	 * 장비관리의 초기 페이지.
	 * @return type
	 */
	public function index() {
		$user = Sentry::getUser();
		if ($user->department->type_code == Department::TYPE_HEAD) {
			return View::make('equip.dashboard');
		} else {
			return Redirect::to('equips/items');
		}
	}

	public function getNodeName($nodeId) {
		$node = EqSupplyManagerNode::find($nodeId);
		return $node->full_name;
	}
}
