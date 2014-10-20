<?php

class EqInventoryController extends BaseController {

	private $service;

	public function __construct() {
		$this->service = new EqService;
	}

	public function wreckedUpdate($itemId) {
		$item = EqItem::find($itemId);
		$user = Sentry::getUser();
		$wrecked = Input::get('wrecked');

		$inventory = EqInventorySet::where('node_id','=',$user->supplyNode->id)->where('item_id','=',$item->id)->first();

		if($inventory) {

			DB::beginTransaction();

			foreach ($item->types as $t) {
				$data = EqInventoryData::where('inventory_set_id','=',$inventory->id)->where('item_type_id','=',$t->id)->first();
				$data->wrecked = $wrecked[$data->id];
				if (!$data->save()) {
					return App::abort(500);
				}
			}

			DB::commit();

			return Redirect::back()->with('message','파손수량이 수정되었습니다.');

		}

		return Redirect::back()->with('message','해당 물품을 보유하고 있지 않습니다');
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

		//날짜 입력했는지 확인
		$validator = Validator::make($input, array(
				'discard_date' => 'required'
			));
		if ($validator->fails()) {
			return Redirect::back()->with('message','폐기일자를 입력하세요');
		}



		DB::beginTransaction();

		//1. discard 테이블에 폐기 물품 등록
		//2. inventory에서 해당 수량만큼 차감

		$dSet = new EqItemDiscardSet;
		$dSet->discarded_date = $input['discard_date'];
		$dSet->item_id = $itemId;
		$dSet->category = $input['category'];
		$dSet->node_id = $user->supplyNode->id;
		$dSet->cause = $input['cause'];

		

		if (!$dSet->save()) {
			return App::abort(500);
		}
		
		foreach ($types as $t) {
			$dData = new EqItemDiscardData;
			$dData->discard_set_id = $dSet->id;
			$dData->item_type_id = $t->id;
			$dData->count = $input['type_counts'][$t->id];
			if (!$dData->save()) {
				return App::abort(500);
			}

			$iData = EqInventoryData::where('inventory_set_id','=',$invSet->id)->where('item_type_id','=',$t->id)->first();
			$iData->count -= $dData->count;

			if ($iData->count < 0) {
				return Redirect::back()->with('message', '폐기수량이 보유수량을 초과합니다.');

			}
			//파손물품 폐기하는 경우 보유수량 중 파손수량을 뺀다.
			if ($input['category']=='wrecked') {
				$iData->wrecked -= $dData->count;
				if ($iData->wrecked<0) {
					return Redirect::back()->with('message', '폐기수량이 파손수량을 초과합니다');
				}
			}

			$iData->save();
		}

		DB::commit();

		return Redirect::back()->with('message', '물품폐기 등록이 완료되었습니다.');
	}

	public function showCodeBelongs($itemCode) {
		$data['code']=EqItemCode::where('code','=',$itemCode)->first();
		$user = Sentry::getUser();
		$data['user']= $user;
		$items = EqItem::where('item_code','=',$itemCode)->get();
		$data['items']= $items;

		//TODO
		// sum of it's given
		// supplied 의 합 + 관리전환 합

		//TODO
		// holding amount

		//item 별 연한 초과 여부를 저장하는 배열
		$timeover = array();

		foreach ($items as $i) {
			
			$data['acquiredSum'][$i->id] = EqItemSupply::whereHas('supplySet', function($q) use($i) {
				$q->where('item_id','=',$i->id);
			})->where('to_node_id','=',$user->supplyNode->id)->sum('count');

			$data['holdingSum'][$i->id] = EqInventoryData::whereHas('parentSet', function($q) use ($i, $user) {
				$q->where('item_id','=',$i->id)->where('node_id','=',$user->supplyNode->id);
			})->sum('count');

			$data['wreckedSum'][$i->id] = EqInventoryData::whereHas('parentSet', function($q) use ($i, $user) {
				$q->where('item_id','=',$i->id)->where('node_id','=',$user->supplyNode->id);
			})->sum('wrecked');

			//불용연한 지났는지 여부 판단
			$acquired_date = $i->acquired_date;
			$acqDate = strtotime($acquired_date);
			$persist = $i->persist_years;
			$endDate = strtotime('+'.$persist.' years', $acqDate);
			$diff = (time() - $endDate)/31536000;


			time() > $endDate ? $timeover[$i->id] = ceil($diff) : $timeover[$i->id] = 0 ;

		}

		$data['timeover'] = $timeover;

		return View::make('equip.inventories-code', $data);
	}

	public function getItemsInCode(){
		$code = EqItemCode::find(Input::get('id'));
		$items = EqItem::where('item_code','=',$code->code)->get();
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

		$data['itemCodes'] =  EqItemCode::whereHas('category', function($q) use ($domainId) {
									$q->where('domain_id', '=', $domainId);
								})->orderBy('category_id', 'asc')->orderBy('sort_order', 'asc')->get();

		$data['domainId'] = $domainId;

		foreach ($data['itemCodes'] as $c) {

			$data['acquiredSum'][$c->id]=0;
			$data['holdingSum'][$c->id]=0;
			$data['wreckedSum'][$c->id]=0;
			foreach ($c->items as $i) {
				$itemAcquiredSum = EqItemSupply::whereHas('supplySet', function($q) use ($i) {
					$q->where('item_id','=',$i->id);
				})->where('to_node_id','=',$user->supplyNode->id)->sum('count');
				$data['acquiredSum'][$c->id] += $itemAcquiredSum;

				$itemHoldingSum = EqInventoryData::whereHas('parentSet', function($q) use ($i, $user) {
					$q->where('item_id','=',$i->id)->where('node_id','=',$user->supplyNode->id);
				})->sum('count');
				$data['holdingSum'][$c->id] += $itemHoldingSum;

				$itemWreckedSum = EqInventoryData::whereHas('parentSet', function($q) use ($i, $user) {
					$q->where('item_id','=',$i->id)->where('node_id','=',$user->supplyNode->id);
				})->sum('wrecked');
				$data['wreckedSum'][$c->id] += $itemWreckedSum;
			} 
		}
        return View::make('equip.inventories-index-real', $data);
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
				if ($counts[$i] !== '') {
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
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $user = Sentry::getUser();
		$node = $user->supplyNode;
		$data['items'] = array();
		$data['having_count'] = array();
		$data['supplied_count'] = array();
		$data['remaining_count'] = array();

		$itemIds = DB::table('eq_item_acquires')->distinct()->select('item_id')->get();

		foreach ($itemIds as $i) {
			$item = EqItem::find($i->item_id);
			array_push($data['items'], $item);

			//item별 현재 관서에서 소유하고 있는 수량 합계 계산
			$havingSum = 0;
			$havingSet = EqInventorySet::where('item_id','=',$item->id)->where('node_id','=',$node->id)->get();
			foreach ($havingSet as $s) {
				$havingSum += $s->children->sum('count'); 
			}
			$data['having_count'][$i->item_id] = $havingSum;

			//item별 현재 관서에서 보급한 수량 합계 계산
			$supplySets = EqItemSupplySet::where('item_id','=',$item->id)->where('from_node_id','=',$node->id)->get();

			$supSum = 0;
			foreach ($supplySets as $set) {
				$supSum += EqItemSupply::where('supply_set_id','=',$set->id)->sum('count');
			}
			$data['supplied_count'][$i->item_id] = $supSum;
			$data['remaining_count'][$i->item_id] = $havingSum - $supSum;
		}
		$data['user'] = $user;

        return View::make('equip.inventories-index', $data);
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
