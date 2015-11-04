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

		$query = EqItemSupplySet::where('is_closed','=',0)->where('from_node_id','=',$userNode->id);
		$supplies = $query->paginate(15);

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

	// Item 지정해서 캐시 생성해주기

	public function makeCache($itemId, $nodeId) {
		$invSet = EqInventorySet::where('node_id','=',$nodeId)->where('item_id','=',$itemId)->first();
		if ($invSet !== null) {
			$countSum = EqInventoryData::where('inventory_set_id','=',$invSet->id)->get()->sum('count');
			$wreckedSum = EqInventoryData::where('inventory_set_id','=',$invSet->id)->get()->sum('wrecked');

			Cache::forever('avail_sum_'.$nodeId.'_'.$itemId, $countSum-$wreckedSum);
			Cache::forever('wrecked_sum_'.$nodeId.'_'.$itemId, $wreckedSum);
		} else {
			Cache::forever('avail_sum_'.$nodeId.'_'.$itemId, 0);
			Cache::forever('wrecked_sum_'.$nodeId.'_'.$itemId, 0);
		}
	}

	public function makeCacheForAll() {
		$items = EqItem::where('is_active','=',1)->get();
		$nodes = EqSupplyManagerNode::where('is_selectable', '=',1)->get();
		foreach ($nodes as $node) {
			// if(!Cache::has('is_cached_'.$node->id)){
				foreach ($items as $item) {
					$this->makeCache($item->id, $node->id);
				}
			// }
			Cache::forever('is_cached_'.$node->id,1);
		}
	}

	public function makeCacheForItem($itemId) {
		$nodes = EqSupplyManagerNode::where('is_selectable','=',1)->get();
		foreach ($nodes as $node) {
			$this->makeCache($itemId,$node->id);
		}
	}

	public function makeCacheForNode($nodeId) {
		$items = EqItem::where('is_active','=',1)->get();
		if(Cache::has('is_cached_'.$nodeId)){
			foreach ($items as $item) {
				$this->makeCache($item->id,$nodeId);
			}
		}
		Cache::forever('is_cached_'.$nodeId,1);
	}

	public function checkCacheForAll() {
		$items = EqItem::where('is_active','=',1)->get();
		$nodes = EqSupplyManagerNode::where('is_selectable', '=',1)->get();
		foreach ($nodes as $node) {
			if(!Cache::has('is_cached_'.$node->id)){
				echo $node->id.",";
				echo $node->full_name."<br>";
			}
		}
	}

	public function makeSubCacheClear($itemId) {
		$nodes = EqSupplyManagerNode::where('is_selectable','=',1)->get();
		foreach ($nodes as $node) {
			Cache::forget('is_sub_cached_'.$node->id.'_'.$itemId);
			Cache::forget('sub_wrecked_sum_'.$node->id.'_'.$itemId);
			Cache::forget('sub_avail_sum_'.$node->id.'_'.$itemId);
			Cache::forget('is_item_sub_cached_'.$itemId);
		}
	}

	public function makeSubCache($itemId) {
		$nodes = EqSupplyManagerNode::where('is_selectable','=',1)->get();
		foreach ($nodes as $node) {
			$this->makeCache($itemId,$node->id);
		}

		foreach ($nodes as $node){
			Cache::forget('is_sub_cached_'.$node->id.'_'.$itemId);
			Cache::forget('sub_wrecked_sum_'.$node->id.'_'.$itemId);
			Cache::forget('sub_avail_sum_'.$node->id.'_'.$itemId);
			Cache::forget('is_item_sub_cached_'.$itemId);

			$parentId = $node->id;
			// 자신의 파손, 가용수량을 가져온다.
			$wreckedSum = Cache::get('wrecked_sum_'.$parentId.'_'.$itemId);
			$availSum = Cache::get('avail_sum_'.$parentId.'_'.$itemId);


			// 자신부터 본청까지 올라가면서 parent에 자신의 파손, 가용수량을 더한다.
			while ($parentId != 0){
				if (!Cache::get('is_sub_cached_'.$parentId.'_'.$itemId)) {
					Cache::forever('sub_wrecked_sum_'.$parentId.'_'.$itemId, $wreckedSum);
					Cache::forever('sub_avail_sum_'.$parentId.'_'.$itemId, $availSum);
					Cache::forever('is_sub_cached_'.$parentId.'_'.$itemId, 1);
				} else {
					$subWreckedSum = Cache::get('sub_wrecked_sum_'.$parentId.'_'.$itemId);
					$subAvailSum = Cache::get('sub_avail_sum_'.$parentId.'_'.$itemId);
					Cache::forever('sub_wrecked_sum_'.$parentId.'_'.$itemId, $subWreckedSum + $wreckedSum);
					Cache::forever('sub_avail_sum_'.$parentId.'_'.$itemId, $subAvailSum + $availSum);
				}
				$parentId = EqSupplyManagerNode::find($parentId)->parent_manager_node;
			}
		}
		Cache::forever('is_item_sub_cached_'.$itemId, 1);
	}

	public function makeSubCacheForCode($codeId) {
		$items = EqItemCode::find($codeId)->items()->get();
		foreach ($items as $item) {
			$this->makeSubCache($item->id);
		}
	}

	public function makeSubCacheForAll() {
		$items = EqItem::where('is_active','=',1)->get();
		foreach ($items as $item) {
			if(!Cache::has('is_item_sub_cached_'.$item->id)){
				$this->makeSubCache($item->id);
			}
		}
	}

	public function checkSubCacheForAll() {
		$items = EqItem::where('is_active','=',1)->get();
		foreach ($items as $item) {
			if(!Cache::has('is_item_sub_cached_'.$item->id)){
				echo $item->id.": not Cached<br>";
			}
		}
	}

	public function clearItemData($nodeId, $itemId){
		$userNode = EqSupplyManagerNode::find($nodeId);
		$nodes = EqSupplyManagerNode::where('full_path','like',$userNode->full_path.'%')->get();

		DB::beginTransaction();
		foreach ($nodes as $node) {
			$invSet = EqInventorySet::where('item_id','=',$itemId)->where('node_id','=',$node->id)->first();
			$types = EqItem::find($itemId)->types;
			echo $node->full_name;
			if($invSet){
				foreach ($types as $t){
					$data = EqInventoryData::where('inventory_set_id','=',$invSet->id)->where('item_type_id','=',$t->id)->first();
					$data->count = 0;
					$data->wrecked = 0;
					echo "Data cleared <br>";
					if (!$data->save()) {
						return App::abort(500);
					}
				}
				Cache::forever('avail_sum_'.$node->id.'_'.$itemId, 0);
				Cache::forever('wrecked_sum_'.$node->id.'_'.$itemId, 0);
			}
		}
		DB::commit();
		echo "finished";
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

	public function checkPeriodForEachItem() {
		$items = EqItem::where('is_active','=',1)->get();
		DB::beginTransaction();
		foreach ($items as $item) {
			$checkPeriod = new EqQuantityCheckPeriod;
			$checkPeriod->check_start = "2014-10-01";
			$checkPeriod->check_end = "2015-10-30";
			$checkPeriod->item_id = $item->id;
			$checkPeriod->save();
		}
		DB::commit();
	}

	public function setCheckPeriod() {
		$checkPeriods = EqQuantityCheckPeriod::get();
		$items = EqItem::where('is_active','=',1)->where('acquired_date','like','2015'.'%')->get();
		DB::beginTransaction();
		foreach ($items as $item) {
			$checkPeriod = EqQuantityCheckPeriod::where('item_id','=',$item->id)->first();

			$checkPeriod->check_end = "2015-10-30";
			$checkPeriod->save();
		}
		DB::commit();
	}

	public function deleteDiscardedItem($nodeId, $itemId) {
		$node = EqSupplyManagerNode::find($node);
		$children = EqSupplyManagerNode::where('full_path','like',$node->full_path)->get();
		DB::beginTransaction();
		foreach ($children as $child) {
			$set = EqItemDiscardSet::where('item_id','=',$itemId)->where('node_id','=',$nodeId)->get();
			foreach ($set as $s) {
				$data = EqItemDiscardData::where('discard_set_id','=',$s->id)->get();
				foreach ($data as $d) {
					if($d->delete()){
						return App::abort(500);
					}
				}
				if($s->delete()){
					return App::abort(500);
				}
			}
		}
		DB::commit();
	}
}
