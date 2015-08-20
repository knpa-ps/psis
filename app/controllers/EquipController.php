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
		$userNode = $user->supplyNode;

		$inbounds = EqConvertSet::where('target_node_id','=',$userNode->id)->take(4)->get();
		$outbounds = EqConvertSet::where('from_node_id','=',$userNode->id)->take(4)->get();

		$surveys = EqItemSurvey::where('node_id','=',$userNode->id)->where('is_closed','=',0)->take(4)->get();
		if ($userNode->type_code === 'D001') {
			$toResponses = EqItemSurvey::where('node_id','=',0)->where('is_closed','=',0)->get();
		} else {
			$toResponses = EqItemSurvey::where('node_id','=',$userNode->managedParent->id)->where('is_closed','=',0)->whereHas('datas', function($q) use($userNode){
											$q->where('target_node_id','=',$userNode->id)->where('count','<>',0);
										})->take(4)->get();
		}

		if (!Cache::has('is_cached_'.$userNode->id)) {
			// 기존 캐시가 없는 경우 item별 합계 캐시를 만들어준다.
			$this->service->makeCache($userNode->id);
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

	public function deleteConfirm($reqId) {

		$req = EqDeleteRequest::find($reqId);

		DB::beginTransaction();

		switch ($req->type) {
			case 'cap':
				$usage = EqCapsaicinUsage::find($req->usage_id);

				$event = $usage->event;

				// 타청에서 사용한걸 삭제할 경우 타청사용량에서 제거해줘야 함.
				if ($usage->cross) {
					$cross = $usage->cross;
					$io = $cross->io;
					if (!$io->delete()) {
						return '타청지원 추가량 삭제 중 오류가 발생했습니다';
					}
					if (!$cross->delete()) {
						return '타청지원내역 삭제 중 오류가 발생했습니다.';
					}
				}
				// 이제 사용내역 삭제함
				if (!$usage->delete()) {
					return '캡사이신 희석액 사용내역 삭제 중 오류가 발생했습니다';
				}

				if ($event->children->count() == 0) {
					if (!$event->delete()) {
						return '캡사이신 희석액 사용 행사 삭제 중 오류가 발생했습니다';
					}
				}

				break;
			case 'pava':
				$event = EqWaterPavaEvent::find($req->usage_id);
				if (!$event->delete()) {
					return App::abort(500);
				}
				break;
			default:
				return "wrong type.";
				break;
		}

		$req->confirmed = 1;
		if (!$req->save()) {
			return App::abort(500);
		}

		DB::commit();

		return "삭제되었습니다";

	}
}
