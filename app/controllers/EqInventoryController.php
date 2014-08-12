<?php

class EqInventoryController extends BaseController {

	private $service;

	public function __construct() {
		$this->service = new EqService;
	}

	public function getItemTypeSet($itemId) {
		$itemTypes = EqItemType::where("item_id","=",$itemId)->get();
		return $itemTypes;
	}

	public function getItemsInCategory() {
		$categoryId = Input::get('id');
		$items = EqItem::where('category_id','=',$categoryId)->get();
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
		$invSet = new EqInventorySet;	
		$invSet->item_id = $data['item'];
		$invSet->node_id = $user->supplyNode->id;
		$invSet->is_confirmed = 1;
		if (!$invSet->save()) {
			return App::abort(400);
		}

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

				$invData = new EqInventoryData;
				$invData->inventory_set_id = $invSet->id;
				$invData->item_type_id = $ids[$i];
				$invData->count = $counts[$i];
				if (!$invData->save()) {
					return App::abort(400);
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
        return View::make('eqinventories.show');
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
