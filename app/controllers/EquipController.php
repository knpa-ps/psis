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

		$surveys = EqItemSurvey::where('node_id','=',$user->supplyNode->id)->where('is_closed','=',0)->take(4)->get();
		if ($user->supplyNode->id == 1) {
			$toResponses = EqItemSurvey::where('node_id','=',0)->where('is_closed','=',0)->get();
		} else {
			$toResponses = EqItemSurvey::where('node_id','=',$user->supplyNode->managedParent->id)->where('is_closed','=',0)->take(4)->get();
		}
		
		return View::make('equip.dashboard', get_defined_vars());
	}

	public function getNodeName($nodeId) {
		$node = EqSupplyManagerNode::find($nodeId);
		return $node->full_name;
	}

	public function showUpdatePersonnelForm(){
		$user = Sentry::getUser();
		$node = $user->supplyNode;
		return View::make('equip.update-personnel-form', array('node'=>$node));
	} 

	public function updatePersonnel() {
		$user = Sentry::getUser();
		$node = $user->supplyNode;

		$node->personnel = (int) Input::get('personnel');
		$node->capacity = (int) Input::get('capacity');
		if (!$node->save()) {
			return array('msg'=>"관할부서 인원 변경에 실패했습니다.");
		}

		return array('msg'=>"관할부서 인원이 변경되었습니다.");
	}
}
