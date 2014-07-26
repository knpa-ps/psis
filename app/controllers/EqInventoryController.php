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
		$data['acquired_count'] = array();
		$data['supplied_count'] = array();
		$data['remaining_count'] = array();

		$itemIds = DB::table('eq_item_acquires')->distinct()->select('item_id')->get();

		foreach ($itemIds as $i) {
			$item = EqItem::find($i->item_id);
			array_push($data['items'], $item);

			//item별 취득 수량 합계 계산
			$acquires = $item->acquires;
			$acqSum = 0;
			foreach ($acquires as $a) {
				$acqSum += $a->count;
			}
			$data['acquired_count'][$i->item_id] = $acqSum;

			//item별 보급 수량 합계 계산
			$supplySets = EqItemSupplySet::where('item_id','=',$item->id)->get();

			$supSum = 0;
			foreach ($supplySets as $set) {
				$supSum += EqItemSupply::where('supply_set_id','=',$set->id)->sum('count');
			}
			$data['supplied_count'][$i->item_id] = $supSum;
			$data['remaining_count'][$i->item_id] = $acqSum - $supSum;
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
		$data['types'] = EqItemType::where('item_id','=','22')->get();
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
		$data['categories'] = EqCategory::all();
		$data['inventory'] = EqInventory::find($id);
        return View::make('equip.inventories-edit',$data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$data = Input::all();

		$inventory = EqInventory::find($id);
		$inventory->item_id = $data['item'];
		$inventory->count = $data['count'];
		$inventory->model_name = $data['model_name'];
		$inventory->acq_date = $data['acquired_date'];
		$inventory->acq_route = $data['acquired_route'];
		if(!$inventory->update()){
			return App::abort(400);
		}
		Session::flash('message', '수정되었습니다.');
		return Redirect::action('EqInventoryController@index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$inventory = EqInventory::find($id);
		if(sizeof($inventory->supplies)!==0) {
			return '보급내역이 존재하는 장비는 취득내역에서 삭제할 수 없습니다.';
		}
		if($inventory->delete()){
			return '삭제되었습니다.';
		} else {
			App::abort(500);
		}
	}

}
