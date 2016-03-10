<?php
use Carbon\Carbon;

class EqInventoryController extends EquipController {

	// 보유수량 수정
	public function countUpdate($itemId) {
		$item = EqItem::find($itemId);
		$user = Sentry::getUser();
		$count = Input::get('count');
		$inventory = EqInventorySet::where('node_id','=',$user->supplyNode->id)->where('item_id','=',$item->id)->first();
		if($inventory) {
			$wreckedSumBefore=Cache::get("wrecked_sum_".$user->supplyNode->id."_".$itemId);
			$availSumBefore=Cache::get("avail_sum_".$user->supplyNode->id."_".$itemId);
			DB::beginTransaction();

			$countSum=0;
			foreach ($item->types as $t) {
				$data = EqInventoryData::where('inventory_set_id','=',$inventory->id)->where('item_type_id','=',$t->id)->first();
				if ($count[$t->id] < $data->wrecked) {
					return Redirect::back()->with('message', '보유수량이 파손수량보다 적을 수 없습니다.');
				}
				$data->count = $count[$t->id];
				$countSum+=$count[$t->id];
				if (!$data->save()) {
					return App::abort(500);
				}
			}
			// 자기 캐시에 등록
			Cache::forever("avail_sum_".$user->supplyNode->id."_".$itemId,$countSum-$wreckedSumBefore);

			// 산하 캐시에 등록
			$parentId = $user->supplyNode->id;
			$availSumChanged = Cache::get('avail_sum_'.$parentId.'_'.$itemId);
			while ($parentId != 0){
				$subAvailSum = Cache::get('sub_avail_sum_'.$parentId.'_'.$itemId);
				Cache::forever('sub_avail_sum_'.$parentId.'_'.$itemId, $subAvailSum + $availSumChanged - $availSumBefore); // 변동수량 델타를 더해줌
				$parentId = EqSupplyManagerNode::find($parentId)->parent_manager_node;
			}

			DB::commit();

			return Redirect::back()->with('message','보유수량이 수정되었습니다.');

		}

		return Redirect::back()->with('message','해당 물품을 보유하고 있지 않습니다');

	}

	// 파손수량 수정
	public function wreckedUpdate($itemId) {
		$item = EqItem::find($itemId);
		$user = Sentry::getUser();
		$wrecked = Input::get('wrecked');

		// 보유수량보다 파손수량이 많아지는 경우를 제외한다
		$inventory = EqInventorySet::where('node_id','=',$user->supplyNode->id)->where('item_id','=',$item->id)->first();

		if($inventory) {

			DB::beginTransaction();
			# 이전에 저장된 파손수량, 가용수량과 비교를 해야한다. 이전에 저장된 파손수량, 가용수량이다.
			$wreckedSumBefore=Cache::get("wrecked_sum_".$user->supplyNode->id."_".$itemId);
			$availSumBefore=Cache::get("avail_sum_".$user->supplyNode->id."_".$itemId);
			# 새로 저장된 파손수량의 총합이다.
			$wreckedSum=0;
			foreach ($item->types as $t) {
				$data = EqInventoryData::where('inventory_set_id','=',$inventory->id)->where('item_type_id','=',$t->id)->first();

				if ($wrecked[$t->id] > $data->count) {
					return Redirect::back()->with('message', '보유수량보다 파손수량이 많을 수 없습니다.');
				}
				$data->wrecked = $wrecked[$t->id];
				$wreckedSum += $wrecked[$t->id];
				if (!$data->save()) {
					return App::abort(500);
				}
			}
			// 캐시에 등록
			Cache::forever("wrecked_sum_".$user->supplyNode->id."_".$itemId,$wreckedSum);
			Cache::forever("avail_sum_".$user->supplyNode->id."_".$itemId,Cache::get("avail_sum_".$user->supplyNode->id."_".$itemId)-($wreckedSum-$wreckedSumBefore));

			// 산하 캐시에 반영
			$parentId = $user->supplyNode->id;
			$wreckedSumChanged = Cache::get('wrecked_sum_'.$parentId.'_'.$itemId);
			$availSumChanged = Cache::get('avail_sum_'.$parentId.'_'.$itemId);
			while ($parentId != 0){
				$subWreckedSum = Cache::get('sub_wrecked_sum_'.$parentId.'_'.$itemId);
				$subAvailSum = Cache::get('sub_avail_sum_'.$parentId.'_'.$itemId);
				Cache::forever('sub_wrecked_sum_'.$parentId.'_'.$itemId, $subWreckedSum + $wreckedSumChanged - $wreckedSumBefore);
				Cache::forever('sub_avail_sum_'.$parentId.'_'.$itemId, $subAvailSum - ($wreckedSumChanged - $wreckedSumBefore)); // 변동수량 델타를 더해줌
				$parentId = EqSupplyManagerNode::find($parentId)->parent_manager_node;
			}

			DB::commit();

			return Redirect::back()->with('message','파손수량이 수정되었습니다.');

		}

		return Redirect::back()->with('message','해당 물품을 보유하고 있지 않습니다');
	}


	public function displayDiscardList($itemId){
		$user = Sentry::getUser();
		$data['user'] = $user;
		$data['item'] = EqItem::find($itemId);
		$data['sets'] = EqItemDiscardSet::where('item_id','=',$data['item']->id)->whereHas('node', function($q) use ($user){
			$q->where('full_path','like',$user->supplyNode->full_path.'%')->where('is_selectable','=',1);
		})->get();

		$types = EqItem::find($itemId)->types;
		$data['types'] = $types;
		foreach ($data['sets'] as $set) {
			$data['sum'][$set->id] = 0;
			foreach ($types as $t) {
				if ($set->category != "repaired") {
					$data['count'][$set->id][$t->id] = EqItemDiscardData::where('discard_set_id','=',$set->id)->where('item_type_id','=',$t->id)->first()->count;
					$data['sum'][$set->id] += $data['count'][$set->id][$t->id];
				} else {
					$data['count'][$set->id][$t->id] = "";
					$data['sum'][$set->id] = "";
				}
			}
		}

		return View::make('equip.inventories-discard-list',$data);
	}



	public function displayDiscardForm($itemId) {
		$user = Sentry::getUser();

		$data['item'] = EqItem::find($itemId);
		$inventory = EqInventorySet::where('item_id','=',$data['item']->id)->where('node_id','=',$user->supplyNode->id)->first();

		foreach ($data['item']->types as $t) {
			$holding[$t->id] = EqInventoryData::where('item_type_id','=',$t->id)->where('inventory_set_id','=',$inventory->id)->first()->count;
		}

		$data['holding'] = $holding;
		return View::make('equip.inventories-discard',$data);
	}

	public function discardItem($itemId) {

		$user = Sentry::getUser();
		$input = Input::all();
		$invSet = EqInventorySet::where('item_id','=',$itemId)->where('node_id','=',$user->supplyNode->id)->first();
		$types = EqItem::find($itemId)->types;

		//사유서 파일 업로드했는지 확인
		$validator = Validator::make($input, array(
				'file_name' => 'required'
		));
		if ($validator->fails()) {
			return Redirect::back()->with('message','사유서 파일을 업로드하세요');
		}

		DB::beginTransaction();

		//1. discard 테이블에 폐기 물품 등록
		//2. inventory에서 해당 수량만큼 차감

		$dSet = new EqItemDiscardSet;
		$dSet->discarded_date = $input['discard_date'];
		$dSet->item_id = $itemId;
		$dSet->category = $input['category'];
		$dSet->node_id = $user->supplyNode->id;
		$dSet->file_name = $input['file_name'];

		if (!$dSet->save()) {
			return App::abort(500);
		}

		$wreckedSum = 0;

		// 분실/폐기하면 카테고리 상관없이 보유수량에서 물품수량이 감소한다.
		// 하지만 inventoryWithdraw함수 안에는 캐시가 가용수량만 감소시키기 때문에 경우를 나눠서 해야한다.
		// 처분유형이 수리가 아닐 때만 EqItemDiscardData에 등록한다.
		if ($input['category']!='repaired') {
			foreach ($types as $t) {
				$dData = new EqItemDiscardData;
				$dData->discard_set_id = $dSet->id;
				$dData->item_type_id = $t->id;
				$dData->count = $input['type_counts'][$t->id];
				if (!$dData->save()) {
					return App::abort(500);
				}

				$iData = EqInventoryData::where('inventory_set_id','=',$invSet->id)->where('item_type_id','=',$t->id)->first();

				if ($iData->count < 0) {
					return Redirect::back()->with('message', '폐기수량이 보유수량을 초과합니다.');
				}

				# 분실, 불용연한 초과일 때만 inventoryWithdraw를 실행하여 보유수량을 감소, 캐시에는 가용수량을 감소시킨다. (캐시는 보유수량을 다룰 필요가 없음)
				if($input['category']=='lost'||$input['category']=='expired'){
					try {
						$this->service->inventoryWithdraw($iData, $dData->count);
					} catch (Exception $e) {
						return Redirect::back()->with('message', $e->getMessage() );
					}
				}

				# 파손물품 폐기하는 경우 보유수량 중 파손장비 처분수량을 빼고, 파손수량 중에서도 파손장비 처분수량을 뺀다.
				if ($input['category']=='wrecked') {
					$iData->count -= $dData->count;
					$iData->wrecked -= $dData->count;
					$wreckedSum += $dData->count;
					if ($iData->wrecked<0) {
						return Redirect::back()->with('message', '폐기수량이 파손수량을 초과합니다');
					}
				}

				$iData->save();
			}
		}
		$prevCache = Cache::get('wrecked_sum_'.$user->supplyNode->id.'_'.$itemId);
		Cache::forever('wrecked_sum_'.$user->supplyNode->id.'_'.$itemId, $prevCache-$wreckedSum);

		// 산하 캐시에 등록
		while ($user->supplyNode->id != 0){
			$subWreckedSum = Cache::get('sub_wrecked_sum_'.$user->supplyNode->id.'_'.$itemId);
			Cache::forever('sub_wrecked_sum_'.$user->supplyNode->id.'_'.$itemId, $subWreckedSum - $wreckedSum); // 변동수량 델타를 더해줌
			$user->supplyNode->id = EqSupplyManagerNode::find($user->supplyNode->id)->parent_manager_node;
		}

		DB::commit();

		return Redirect::back()->with('message', '물품처분 내역 등록이 완료되었습니다.');
	}

	public function cancelDiscardedItem($setId){
		$dSet = EqItemDiscardSet::where('id','=',$setId)->first();
		$invSet = EqInventorySet::where('item_id','=',$dSet->item_id)->where('node_id','=',$dSet->node_id)->first();
		$types = EqItem::find($dSet->item_id)->types;

		DB::beginTransaction();

		$wreckedSum = 0;
		if ( $dSet->category != "repaired") {
			foreach ($types as $t) {
				$iData = EqInventoryData::where('inventory_set_id','=',$invSet->id)->where('item_type_id','=',$t->id)->first();
				$dData = EqItemDiscardData::where('discard_set_id','=',$dSet->id)->where('item_type_id','=',$t->id)->first();
				# 분실, 불용연한 초과를 취소할때 inventorySupply를 실행하여 보유수량을 증가, 캐시에는 가용수량을 증가시킴
				if($dSet->category=='lost'||$dSet->category=='expired'){
					try {
						$this->service->inventorySupply($iData, $dData->count);
					} catch (Exception $e) {
						return Redirect::back()->with('message', $e->getMessage() );
					}
				}
				# 파손물품 폐기를 취소할때 보유수량과 파손수량 모두 빠진 양을 증가시킨다.
				if ($dSet->category=='wrecked') {
					$iData->count += $dData->count;
					$iData->wrecked += $dData->count;
					$wreckedSum += $dData->count;
				}
				$iData->save();
				$dData->delete();
			}
		}
		$prevCache = Cache::get('wrecked_sum_'.$dSet->node_id.'_'.$dSet->item_id);
		Cache::forever('wrecked_sum_'.$dSet->node_id.'_'.$dSet->item_id, $prevCache+$wreckedSum);

		// 산하 캐시에 등록
		while ($dSet->node_id != 0){
			$subWreckedSum = Cache::get('sub_wrecked_sum_'.$dSet->node_id.'_'.$dSet->item_id);
			Cache::forever('sub_wrecked_sum_'.$dSet->node_id.'_'.$dSet->item_id, $subWreckedSum + $wreckedSum); // 변동수량 델타를 더해줌
			$dSet->node_id = EqSupplyManagerNode::find($dSet->node_id)->parent_manager_node;
		}

		$dSet->delete();

		DB::commit();

		return Redirect::back()->with('message', '물품처분 내역 삭제가 완료되었습니다.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */

	public function show($itemCode) {
		$data['code']=EqItemCode::where('code','=',$itemCode)->first();
		$user = Sentry::getUser();
		$userNode = $user->supplyNode;

		if ($userNode->id == 2) {
			$data['children'] = EqSupplyManagerNode::where('full_path','like',$userNode->full_path.'%')->where('is_selectable','=','1')->orderBy('full_path')->get();
		} else {
			$data['children'] = $userNode->managedChildren;
		}
		$data['user']= $user;
		$items = EqItem::where('item_code','=',$itemCode)->where('is_active','=',1)->get();
		$data['items']= $items;
		$byYear = Input::get('byYear');

		if (!$byYear) {
			$data['byYear'] = true;
		} else {
			if ($byYear=='true') {
				$data['byYear'] = true;
			} else {
				$data['byYear'] = false;
			}
		}

		//item 별 연한 초과 여부를 저장하는 배열
		$timeover = array();

		foreach ($items as $i) {
			if (!Cache::has('is_cached_'.$userNode->id)) {
					$this->service->makeCache($userNode->id);
			}
			// user의 보유수량 정보(연도별, 기관별 모두 사용)
			$data['wreckedSum'][$userNode->id][$i->id] = Cache::get('wrecked_sum_'.$userNode->id.'_'.$i->id);
			$data['availSum'][$userNode->id][$i->id] = Cache::get('avail_sum_'.$userNode->id.'_'.$i->id);
			$data['subWreckedSum'][$userNode->id][$i->id] = Cache::get('sub_wrecked_sum_'.$userNode->id.'_'.$i->id);
			$data['subAvailSum'][$userNode->id][$i->id] = Cache::get('sub_avail_sum_'.$userNode->id.'_'.$i->id);

			$data['subDiscardSets'][$userNode->id][$i->id] = EqItemDiscardSet::where('item_id','=',$i->id)->whereHas('node', function($q) use ($userNode){
				$q->where('full_path','like',$userNode->full_path.'%')->where('is_selectable','=',1);
			})->count();
			# 본인 node 의 분실/폐기 건수 총합
			$data['discardSets'][$userNode->id][$i->id] = EqItemDiscardSet::where('item_id','=',$i->id)->where('node_id','=',$userNode->id)->count();
			# 본인 node 를 포함하여 그 아래에 있는 node 들의 분실/폐기 갯수 총합
			$data['subDiscardSum'][$userNode->id][$i->id] = EqItemDiscardData::whereHas('discardSet',function($q) use ($i,$userNode){
				$q->where('item_id','=',$i->id)->whereHas('node', function($qq) use ($userNode){
					$qq->where('full_path','like',$userNode->full_path.'%')->where('is_selectable','=',1);
				});
			})->sum('count');
			# 본인 node의 분실/폐기 갯수 총합
			$data['discardSum'][$userNode->id][$i->id] = EqItemDiscardData::whereHas('discardSet',function($q) use ($i,$userNode){
				$q->where('item_id','=',$i->id)->where('node_id','=',$userNode->id);
			})->sum('count');

			//불용연한 지났는지 여부 판단
			$acquired_date = $i->acquired_date;
			$acqDate = strtotime($acquired_date);
			$persist = $i->persist_years;
			$endDate = strtotime('+'.$persist.' years', $acqDate);
			$diff = (time() - $endDate)/31536000;

			time() > $endDate ? $timeover[$i->id] = ceil($diff) : $timeover[$i->id] = 0 ;
		}

		foreach ($data['children'] as $child) {
			$data['wreckedSumAllYear'][$child->id] = 0;
			$data['availSumAllYear'][$child->id] = 0;
			$data['subWreckedSumAllYear'][$child->id] = 0;
			$data['subAvailSumAllYear'][$child->id] = 0;

			$data['subDiscardSetsAllYear'][$child->id]=0;
			$data['discardSetsAllYear'][$child->id]=0;
			$data['subDiscardSumAllYear'][$child->id]=0;
			$data['discardSumAllYear'][$child->id]=0;
		}
		// user바로 밑 managed children의 보유수량 정보 (기관별에 사용)
		foreach ($data['children'] as $child) {
			foreach ($items as $i) {
				$data['wreckedSum'][$child->id][$i->id] = Cache::get('wrecked_sum_'.$child->id.'_'.$i->id);
				$data['availSum'][$child->id][$i->id] = Cache::get('avail_sum_'.$child->id.'_'.$i->id);
				$data['subWreckedSum'][$child->id][$i->id] = Cache::get('sub_wrecked_sum_'.$child->id.'_'.$i->id);
				$data['subAvailSum'][$child->id][$i->id] = Cache::get('sub_avail_sum_'.$child->id.'_'.$i->id);

				$data['subDiscardSets'][$child->id][$i->id] = EqItemDiscardSet::where('item_id','=',$i->id)->whereHas('node', function($q) use ($child){
					$q->where('full_path','like',$child->full_path.'%')->where('is_selectable','=',1);
				})->count();
				$data['discardSets'][$child->id][$i->id] = EqItemDiscardSet::where('item_id','=',$i->id)->where('node_id','=',$child->id)->count();
				$data['subDiscardSum'][$child->id][$i->id] = EqItemDiscardData::whereHas('discardSet',function($q) use ($i,$child){
					$q->where('item_id','=',$i->id)->whereHas('node', function($qq) use ($child){
						$qq->where('full_path','like',$child->full_path.'%')->where('is_selectable','=',1);
					});
				})->sum('count');
				$data['discardSum'][$child->id][$i->id] = EqItemDiscardData::whereHas('discardSet',function($q) use ($i,$child){
					$q->where('item_id','=',$i->id)->where('node_id','=',$child->id);
				})->sum('count');

				$data['wreckedSumAllYear'][$child->id] += Cache::get('wrecked_sum_'.$child->id.'_'.$i->id);
				$data['availSumAllYear'][$child->id] += Cache::get('avail_sum_'.$child->id.'_'.$i->id);
				$data['subWreckedSumAllYear'][$child->id] += Cache::get('sub_wrecked_sum_'.$child->id.'_'.$i->id);
				$data['subAvailSumAllYear'][$child->id] += Cache::get('sub_avail_sum_'.$child->id.'_'.$i->id);

				$data['subDiscardSetsAllYear'][$child->id] += EqItemDiscardSet::where('item_id','=',$i->id)->whereHas('node', function($q) use ($child){
					$q->where('full_path','like',$child->full_path.'%')->where('is_selectable','=',1);
				})->count();
				$data['discardSetsAllYear'][$child->id] += EqItemDiscardSet::where('item_id','=',$i->id)->where('node_id','=',$child->id)->count();
				$data['subDiscardSumAllYear'][$child->id] += EqItemDiscardData::whereHas('discardSet',function($q) use ($i,$child){
					$q->where('item_id','=',$i->id)->whereHas('node', function($qq) use ($child){
						$qq->where('full_path','like',$child->full_path.'%')->where('is_selectable','=',1);
					});
				})->sum('count');
				$data['discardSumAllYear'][$child->id] += EqItemDiscardData::whereHas('discardSet',function($q) use ($i,$child){
					$q->where('item_id','=',$i->id)->where('node_id','=',$child->id);
				})->sum('count');
			}
		}

		$data['timeover'] = $timeover;

		return View::make('equip.inventories-code', $data);
	}
	public function showDetail($itemCode, $itemId){
		$user = Sentry::getUser();
		$item = EqItem::find($itemId);
		$userRegion = $user->supplyNode->region();
		if ($item == null) {
			return App::abort(404);
		}
		$types = $item->types;
		$data['domainId'] = $item->code->category->domain->id;
		$data['category'] = $item->code->category;
		$data['item'] = $item;
		$data['types'] = $types;
		$data['discardSets'] = EqItemDiscardSet::where('item_id','=',$data['item']->id)->whereHas('node', function($q) use ($user){
			$q->where('full_path','like',$user->supplyNode->full_path.'%')->where('is_selectable','=',1);
		})->count();

		$invSet = EqInventorySet::where('item_id','=',$itemId)->where('node_id','=',$user->supplyNode->id)->first();
		$modifiable = true;
		$now = Carbon::now();
		$includingToday = EqQuantityCheckPeriod::where('node_id','=',$userRegion->id)->where('item_id','=',$itemId)->where('check_end','<',$now)->get();
		if (sizeof($includingToday) !== 0) {
			$modifiable = false;
		}
		$data['modifiable'] = $modifiable;

		if (!$invSet) {

			DB::beginTransaction();

			$invSet = new EqInventorySet;

			$invSet->item_id = $item->id;
			$invSet->node_id = $user->supplyNode->id;

			if (!$invSet->save()) {
				return App::abort(500);
			}

			foreach ($types as $t) {
				$invData = new EqInventoryData;

				$invData->inventory_set_id = $invSet->id;
				$invData->item_type_id = $t->id;

				if (!$invData->save()) {
					return App::abort(500);
				}
			}

			DB::commit();
		}
		$data['inventorySet'] = $invSet;
		$inventory = EqInventorySet::where('item_id','=',$data['item']->id)->where('node_id','=',$user->supplyNode->id)->first();
		foreach ($data['item']->types as $t) {
			$holding[$t->id] = EqInventoryData::where('item_type_id','=',$t->id)->where('inventory_set_id','=',$inventory->id)->first()->count;
			$wrecked[$t->id] = EqInventoryData::where('item_type_id','=',$t->id)->where('inventory_set_id','=',$inventory->id)->first()->wrecked;
		}
		$data['holding'] = $holding;
		$data['wrecked'] = $wrecked;
		return View::make('equip.items-show', $data);
	}

	public function getItemsInCode(){
		$code = EqItemCode::find(Input::get('id'));
		$items = EqItem::where('item_code','=',$code->code)->where('is_active','=',1)->get();
		return $items;
	}

	public function getItemTypeSet($itemId) {
		$itemTypes = EqItemType::where("item_id","=",$itemId)->get();
		return $itemTypes;
	}

	public function getItemsInCategory() {
		$categoryId = Input::get('id');
		$items = EqItemCode::where('category_id','=',$categoryId)->get();
		return $items;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Sentry::getUser();
		$userNode = $user->supplyNode;

		$data['domains'] = $this->service->getVisibleDomains($user);

		if (count($data['domains']) == 0) {
			return App::abort(403);
		};

		$domainId = Input::get('domain');

		if (!$domainId) {
			$domainId = $data['domains'][0]->id;
		}

		if (!$user->hasAccess(EqDomain::find($domainId)->permission)) {
			return App::abort(403);
		}

		$data['user'] = $user;
		$data['node'] = $userNode;

		$data['itemCodes'] =  EqItemCode::whereHas('category', function($q) use ($domainId) {
									$q->where('domain_id', '=', $domainId);
								})->orderBy('sort_order', 'asc')->get();
		$data['categories'] = EqCategory::all();
		$data['domainId'] = $domainId;

		foreach ($data['itemCodes'] as $c) {

			// $data['acquiredSum'][$c->id]=0;
			$data['wreckedSum'][$c->id]=0;
			$data['availSum'][$c->id]=0;
			$data['countSum'][$c->id] = 0;
			$data['subWreckedSum'][$c->id]=0;
			$data['subAvailSum'][$c->id]=0;
			$data['subDiscardSets'][$c->id]=0;
			$data['discardSets'][$c->id]=0;
			$data['subDiscardSum'][$c->id]=0;
			$data['discardSum'][$c->id]=0;

			foreach ($c->items as $i) {

				$wreckedSum = Cache::get('wrecked_sum_'.$userNode->id.'_'.$i->id);
				$availSum = Cache::get('avail_sum_'.$userNode->id.'_'.$i->id);
				// $acquiredSum = Cache::get('acquired_sum_'.$userNode->id.'_'.$i->id);
				$subWreckedSum = Cache::get('sub_wrecked_sum_'.$userNode->id.'_'.$i->id);
				$subAvailSum = Cache::get('sub_avail_sum_'.$userNode->id.'_'.$i->id);

				$data['wreckedSum'][$c->id] += $wreckedSum;
				$data['availSum'][$c->id] += $availSum;
				// $data['acquiredSum'][$c->id] += $acquiredSum;
				$data['subWreckedSum'][$c->id] += $subWreckedSum;
				$data['subAvailSum'][$c->id] += $subAvailSum;

				# 본인 node 를 포함하여 그 아래에 있는 node 들의 분실/폐기 건수 총합
				$data['subDiscardSets'][$c->id] += EqItemDiscardSet::where('item_id','=',$i->id)->whereHas('node', function($q) use ($userNode){
					$q->where('full_path','like',$userNode->full_path.'%')->where('is_selectable','=',1);
				})->count();
				# 본인 node 의 분실/폐기  건수총합
				$data['discardSets'][$c->id] += EqItemDiscardSet::where('item_id','=',$i->id)->where('node_id','=',$userNode->id)->count();
				# 본인 node 를 포함하여 그 아래에 있는 node 들의 분실/폐기 갯수 총합
				$data['subDiscardSum'][$c->id] += EqItemDiscardData::whereHas('discardSet',function($q) use ($i,$userNode){
					$q->where('item_id','=',$i->id)->whereHas('node', function($qq) use ($userNode){
						$qq->where('full_path','like',$userNode->full_path.'%')->where('is_selectable','=',1);
					});
				})->sum('count');
				# 본인 node 의 분실/폐기 갯수 총합
				$data['discardSum'][$c->id] += EqItemDiscardData::whereHas('discardSet',function($q) use ($i,$userNode){
					$q->where('item_id','=',$i->id)->where('node_id','=',$userNode->id);
				})->sum('count');

			}
		}
		//Excel로 총괄표 export
		$node = $user->supplyNode;
		if (Input::get('export')) {
			if (Input::get('supply_node_id')) {
				$node = EqSupplyManagerNode::find(Input::get('supply_node_id'));
			}
			$this->service->exportGeneralTable($node);
			return;
		}

        return View::make('equip.inventories-index', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$data['categories'] = EqCategory::all();
        return View::make('equip.inventories-add', $data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$data = Input::all();
		$user = Sentry::getUser();

		$ids = $data['type_ids'];
		$counts = $data['type_counts'];

		DB::beginTransaction();

		//자기가 자신에게 물품 지급함.
		$invSet = EqInventorySet::where('node_id','=',$user->supplyNode->id)->where('item_id','=',$data['classification'])->first();
		// Inventory에 해당 물품이 존재한다면 불러오고 없으면 만든다.
		if ($invSet == null) {
			$invSet = new EqInventorySet;
			$invSet->item_id = $data['classification'];
			$invSet->node_id = $user->supplyNode->id;
			if (!$invSet->save()) {
				return App::abort(400);
			}

			for ($i=0; $i < sizeof($ids); $i++) {
				$acq = new EqItemAcquire;
				$acq->item_id = $data['classification'];
				$acq->item_type_id = $ids[$i];
				$acq->count = $counts[$i];
				$acq->acquired_date = $data['acquired_date'];
				if (!$acq->save()) {
					return App::abort(400);
				}

				$invData = new EqInventoryData;
				$invData->inventory_set_id = $invSet->id;
				$invData->item_type_id = $ids[$i];
				$invData->count = $counts[$i];
				if (!$invData->save()) {
					return App::abort(400);
				}
			}
		} else {
			for ($i=0; $i < sizeof($ids); $i++) {
				if ($counts[$i] !== '') {
					$acq = new EqItemAcquire;
					$acq->item_id = $data['item'];
					$acq->item_type_id = $ids[$i];
					$acq->count = $counts[$i];
					$acq->acquired_date = $data['acquired_date'];
					if (!$acq->save()) {
						return App::abort(400);
					}

					$invData = EqInventoryData::where('inventory_set_id','=',$invSet->id);
					$invData->count += $counts[$i];
					$invData->save();
				}
			}
		}
		DB::commit();

		Session::flash('message', '저장되었습니다');
		return Redirect::action('EqInventoryController@index');
	}



	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{

	}

}
