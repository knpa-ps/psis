<?php

class EqSupplyController extends BaseController {

	public function getSupplyTreeNodes() {
		$parentId = Input::get('id');

		$supplyNodes = EqSupplyManagerNode::find($parentId === '#' ? 1 : $parentId)->children;

		$nodes = array();

		foreach ($supplyNodes as $supNode) {
			if ($supNode->is_selectable == 1) {
				$nodes[] = array(
					'id' => $supNode->id,
					'text' => $supNode->node_name,
					'children' => $supNode->is_terminal?array():true,
					'li_attr' => array( 
						'data-full-name' => $supNode->full_name,
						'data-selectable' => $supNode->is_selectable
						)
				);
			} else {
				$nodes[] = array(
					'id' => $supNode->id,
					'text' => $supNode->node_name,
					'children' => $supNode->is_terminal?array():true,
					'li_attr' => array( 
						'data-full-name' => $supNode->full_name,
						'data-selectable' => $supNode->is_selectable
						),
					'state' => array(
							'disabled'=> true
						)
				);
			}
			
		}
		return $nodes;
	}

	public function getClassifiers(){
		$itemId = Input::get('item_id');
		$inventories = EqInventory::where('item_id','=',$itemId)->get();
		$res = array();
		if(sizeof($inventories)!==0){
			$options = '';
			foreach ($inventories as $i) {
				$options = $options.'<option value="'.$i->id.'">'.$i->manufacturer.' ('.$i->acq_date.')</option>';
			}
			$res['body'] = $options;
			$res['code'] = 1;
			return $res;
		} else {
			$res['code'] = 0;
			$res['body'] = "보유한 장비가 없어 보급할 수 없습니다.";
			return $res;
		}
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$start = Input::get('start');
		$end = Input::get('end');
		$user = Sentry::getUser();
		$nodeId = $user->supplyNode->id;

		$validator = Validator::make(Input::all(), array(
				'start'=>'date',
				'end'=>'date'
			));

		if ($validator->fails()) {
			return App::abort(400);
		}

		if (!$start) {
			$start = date('Y-m-d', strtotime('-1 year'));
		}

		if (!$end) {
			$end = date('Y-m-d');
		}

		$itemName = Input::get('item_name');

		$query = EqItemSupplySet::where('supplied_date', '>=', $start)->where('supplied_date', '<=', $end)->where('is_closed','=',0)->where('from_node_id','=',$nodeId);

		if ($itemName) {
			$query->whereHas('item', function($q) use($itemName) {
				$q->whereHas('code', function($qry) use($itemName) {
					$qry->where('title','like',"%$itemName%");
				});
			});
		}

		$supplies = $query->paginate(15);

		$items = EqItem::where('is_active','=',1)->whereHas('inventories', function($q) use ($nodeId) {
			$q->where('node_id','=',$nodeId);
		})->get();

        return View::make('equip.supplies-index', get_defined_vars());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$itemId = Input::get('item');
		if($itemId==0){
			Session::flash('message', '보유중인 장비가 없어 보급할 수 없습니다.');
			return Redirect::back();
		}
		$user = Sentry::getUser();
		$userNode = $user->supplyNode;
		$data = array();

		$types = EqItemType::where('item_id','=',$itemId)->get();
		
		$data['types'] = $types;
		$data['mode'] = 'create';
		$data['item'] = EqItem::find($itemId);
		$data['userNode'] = $userNode;
		$data['lowerNodes'] = $userNode->managedChildren;
		
        return View::make('equip.supplies-create',$data);
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
		$nodes = $user->supplyNode->managedChildren;
		$types = EqItemType::where('item_id','=',$data['item_id'])->get();


		$acquiredSum = EqItemAcquire::where('item_id','=',$data['item_id'])->get()->sum('count');
		$supplied = EqItemSupplySet::where('item_id','=',$data['item_id'])->get();


		//현재 보유중인 사이즈별 수량을 holdingNum[type_id]에 저장한다.
		foreach ($types as $t) {
			$holdingNum[$t->id] = EqInventoryData::whereHas('parentSet', function($q) use ($user) {
				$q->where('node_id','=',$user->supplyNode->id);
			})->where('item_type_id','=',$t->id)->first();
		}

		DB::beginTransaction();

		$supplySet = new EqItemSupplySet;
		$supplySet->item_id = $data['item_id'];
		$supplySet->creator_id = $user->id;
		$supplySet->from_node_id = $user->supplyNode->id;
		$supplySet->supplied_date = $data['supply_date'];

		if (!$supplySet->save()) {
			return App::abort(500);
		}

		foreach ($nodes as $node) {
			$countName = 'count_';
			$countNameNode = $countName.$node->id.'_';

			// 보급하는 노드의 인벤토리 - $supplyInvSet
			$supplyInvSet = EqInventorySet::where('item_id','=',$data['item_id'])->where('node_id','=',$user->supplyNode->id)->first();

			// 보급받는 노드의 인벤토리 - $receiveInvSet
			$receiveInvSet = EqInventorySet::where('item_id','=',$data['item_id'])->where('node_id','=',$node->id)->first();

			if($receiveInvSet == null) {
				// 보급받는 노드에서 이 아이템을 기존에 보유한 적이 없는 경우
				$receiveInvSet = new EqInventorySet;
				$receiveInvSet->item_id = $data['item_id'];
				$receiveInvSet->node_id = $node->id;
				if (!$receiveInvSet->save()) {
					return App::abort(500);
				}
				foreach ($types as $type) {
					$typeId = $type->id;
					$countName = $countNameNode.$typeId;

					$supply = new EqItemSupply;
					$supply->supply_set_id = $supplySet->id;
					$supply->item_type_id = $type->id;
					$supply->count = $data[$countName];
					$supply->to_node_id = $node->id;

					if (!$supply->save()) {
						return App::abort(500);
					}

					// 보급하는 노드에서 보유수량을 줄인다
					$supplyInvData = EqInventoryData::where('inventory_set_id','=',$supplyInvSet->id)->where('item_type_id','=',$typeId)->first();
					$supplyInvData->count -= $data[$countName];
					$supplyInvData->save();

					// 보급받는 노드에서 보유수량을 늘인다

					$invData = new EqInventoryData;
					$invData->inventory_set_id = $receiveInvSet->id;
					$invData->item_type_id = $type->id;
					$invData->count = $data[$countName];

					if (!$invData->save()) {
						return App::abort(500);
					}
				}
			} else {
				// 보급받는 노드에서 기존에 그 아이템을 보유한 경우
				foreach ($types as $type) {
					$typeId = $type->id;
					$countName = $countNameNode.$typeId;

					$supply = new EqItemSupply;
					$supply->supply_set_id = $supplySet->id;
					$supply->item_type_id = $type->id;
					$supply->count = $data[$countName];
					$supply->to_node_id = $node->id;

					if (!$supply->save()) {
						return App::abort(500);
					}

					// 보급하는 노드에서 보유수량을 줄인다
					$supplyInvData = EqInventoryData::where('inventory_set_id','=',$supplyInvSet->id)->where('item_type_id','=',$typeId)->first();
					$supplyInvData->count -= $data[$countName];
					$supplyInvData->save();

					// 보급받는 노드에서 보유수량을 늘린다.
					$receiveInvData = EqInventoryData::where('inventory_set_id','=',$receiveInvSet->id)->where('item_type_id','=',$typeId)->first();
					$receiveInvData->count += $data[$countName];
					$receiveInvData->save();
				}
			}
		}

		//사이즈별 보급 수량을 계산하여 보유수량보다 적으면 빠꾸먹인다.

		foreach ($types as $t) {
			$supplyNum[$t->id] = EqItemSupply::where('supply_set_id','=',$supplySet->id)->where('item_type_id','=',$t->id)->sum('count');

			if ($supplyNum[$t->id] > $holdingNum[$t->id]) {
				$lack = $supplyNum[$t->id] - $holdingNum[$t->id];
				Session::flash('message', '해당 사이즈의 보유수량이 부족합니다. \n(현재 '.$t->type_name.' 보유수량: '.$holdingNum[$t->id].', '.$lack.'개 부족)');
				return Redirect::back();
			}
		}

		DB::commit();

		Session::flash('message', '저장되었습니다.');	
		return Redirect::to('equips/supplies');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$user = Sentry::getUser();
		$userNode = $user->supplyNode;
		$data = array();
		$supply = EqItemSupplySet::find($id);

		$types = EqItemType::where('item_id','=',$supply->item->id)->get();
		$lowerNodes = $userNode->managedChildren;
		$count = array();

		$data['types'] = $types;
		$data['supply'] = $supply;
		$data['item'] = $supply->item;
		$data['lowerNodes'] = $lowerNodes;
		
		foreach ($lowerNodes as $n) {
			$nodeSupplies = EqItemSupply::where('to_node_id','=',$n->id)->where('supply_set_id','=',$supply->id)->get();
			foreach ($nodeSupplies as $s) {
				if(!$s->count == 0){
					$count[$n->id][$s->item_type_id] = $s->count;
				} else {
					$count[$n->id][$s->item_type_id] = '';
				}
			}
		}

		$data['count'] = $count;
        return View::make('equip.supplies-show', $data);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$user = Sentry::getUser();
		$supply = EqItemSupplySet::find($id);
		$item = $supply->item;
		$types = $item->types;
		$userNode = $user->supplyNode;
		$lowerNodes = $userNode->children;
		$mode = 'update';

		foreach ($lowerNodes as $n) {
			foreach ($types as $t) {
				$count[$n->id][$t->id] = EqItemSupply::where('supply_set_id','=',$id)->where('to_node_id','=',$n->id)->where('item_type_id','=',$t->id)->first()->count;
			}
		}
		
        return View::make('equip.supplies-create',get_defined_vars());
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// $data = Input::all();
		// $user = Sentry::getUser();
		// $nodes = EqSupplyManagerNode::where('parent_id','=',$user->supplyNode->id)->get();
		// $types = EqItemType::where('item_id','=',$data['item_id'])->get();


		// $acquiredSum = EqItemAcquire::where('item_id','=',$data['item_id'])->get()->sum('count');
		// $supplied = EqItemSupplySet::where('item_id','=',$data['item_id'])->where('id','!=',$id)->get();
		// $suppliedSum = 0;
		// foreach ($supplied as $s) {
		// 	$suppliedSum += $s->children->sum('count');
		// }


		// DB::beginTransaction();

		// $supplySet = EqItemSupplySet::find($id);
		// $supplySet->supplied_date = $data['supply_date'];

		// $supplySet->update();

		// foreach ($nodes as $node) {
		// 	$countName = 'count_';
		// 	$countNameNode = $countName.$node->id.'_';

		// 	foreach ($types as $type) {
		// 		$typeId = $type->id;
		// 		$countName = $countNameNode.$typeId;

		// 		$supply = EqItemSupply::where('supply_set_id','=',$id)->where('to_node_id','=',$node->id)->where('item_type_id','=',$type->id)->first();
		// 		$supply->count = $data[$countName];

		// 		$supply->update();
		// 	}
		// }

		// $countSum = $supplySet->children->sum('count');

		// $havingSum = $acquiredSum - $suppliedSum;
		// $lack = $countSum - $havingSum;
		// if($countSum > $havingSum){
		// 	Session::flash('message', '보유수량이 부족합니다. (현재 보유수량: '.$havingSum.', '.$lack.'개 부족)');
		// 	return Redirect::back();
		// }

		// DB::commit();

		// Session::flash('message', '수정되었습니다.');	
		// return Redirect::to('equips/supplies');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$s = EqItemSupplySet::find($id);
		if (!$s) {
			return App::abort(500);
		}
		$datas = $s->children;

		$item = EqItem::find($s->item_id);

		// 보급을 삭제할 경우 보급 내역을 인벤토리에서 롤백한다.
		
		DB::beginTransaction();

		// 1. 보급한 관서의 인벤토리 수량 더하기
		$supplierNodeId = $s->from_node_id;

		$supplierInvSet = EqInventorySet::where('node_id','=',$supplierNodeId)->where('item_id','=',$item->id)->first();

		foreach ($item->types as $t) {
			$suppliedCount = EqItemSupply::where('supply_set_id','=',$s->id)->where('item_type_id','=',$t->id)->sum('count');
			$invData = EqInventoryData::where('inventory_set_id','=',$supplierInvSet->id)->where('item_type_id','=',$t->id)->first();
			$invData->count += $suppliedCount;
			if (!$invData->save()) {
				return App::abort(500);
			}
		} 

		// 2. 보급받은 관서의 인벤토리 수량 빼기
		foreach ($datas as $d) {
			$itemTypeId = $d->item_type_id;
			$toNodeId = $d->to_node_id;
			$invSet = EqInventorySet::where('node_id','=',$toNodeId)->where('item_id','=',$s->item_id)->first();
			$invData = EqInventoryData::where('inventory_set_id','=',$invSet->id)->where('item_type_id','=',$d->item_type_id)->first();
			
			$invData->count -= $d->count;
			if (!$invData->save()) {
				return App::abort(500);
			}

			if (!$d->delete()) {
				return App::abort(500);
			}
		}

		if (!$s->delete()) {
			return App::abort(500);
		}

		DB::commit();

		return Redirect::back()->with('message', '해당 보급이 취소되었습니다');
	}

}
