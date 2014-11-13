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

		$inbounds = EqConvertSet::where('target_node_id','=',$user->supplyNode->id)->take(4)->get();
		$outbounds = EqConvertSet::where('from_node_id','=',$user->supplyNode->id)->take(4)->get();

		$surveys = EqItemSurvey::where('node_id','=',$user->supplyNode->id)->take(4)->get();
		$toResponses = EqItemSurvey::where('node_id','=',$user->supplyNode->managedParent)->take(4)->get();
		return View::make('equip.dashboard', get_defined_vars());
	}

	public function getNodeName($nodeId) {
		$node = EqSupplyManagerNode::find($nodeId);
		return $node->full_name;
	}
}
