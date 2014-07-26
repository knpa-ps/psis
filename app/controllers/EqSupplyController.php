<?php

class EqSupplyController extends BaseController {

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

	public function removeSupply($id,$detailId){
		$detail = EqSupplyDetail::find($detailId);
		$supply = EqSupply::find($id);

		if(!$detail->delete()){
			return App::abort(500);
		}
		$data['sum'] = number_format($supply->details->sum('count'));
		
		return $data;
	}
	public function addSupply($id){
		$formData = Input::all();
		$supply = EqSupply::find($id);

		$detail = new EqSupplyDetail;
		$detail->count = $formData['count'];
		$detail->supply_id = $id;
		$detail->target_dept_id = $formData['dept_id'];
		if(!$detail->save()){
			return App::abort(500);
		}
		$toTableRow = '<tr> <td>'.$detail->department->full_name.'</td><td>'.number_format($detail->count)."</td><td><a href='#' id='".$detail->id."' class='remove label label-danger'>삭제</a></td></tr>";
		$data['row'] = $toTableRow;
		$data['sum'] = number_format($supply->details->sum('count'));
		return $data;
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

		$query = EqItemSupplySet::where('supplied_date', '>=', $start)->where('supplied_date', '<=', $end);

		if ($itemName) {
			$query->whereHas('item', function($q) use ($itemName) {
				$q->where('name', 'like', "%$itemName%");
			});
		}

		$supplies = $query->paginate(15);

		$items = EqItem::where('is_active','=',1)->get();

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
		$user = Sentry::getUser();
		$userNode = $user->supplyNode;
		$data = array();

		$types = EqItemType::where('item_id','=',$itemId)->get();
		
		$data['types'] = $types;
		$data['mode'] = 'create';
		$data['item'] = EqItem::find($itemId);
		$data['userNode'] = $userNode;
		$data['lowerNodes'] = EqSupplyManagerNode::where('parent_id','=',$userNode->id)->get();
		
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
		$nodes = EqSupplyManagerNode::where('parent_id','=',$user->supplyNode->id)->get();
		$types = EqItemType::where('item_id','=',$data['item_id'])->get();


		$acquiredSum = EqItemAcquire::where('item_id','=',$data['item_id'])->get()->sum('count');
		$supplied = EqItemSupplySet::where('item_id','=',$data['item_id'])->get();
		$suppliedSum = 0;
		foreach ($supplied as $s) {
			$suppliedSum += $s->children->sum('count');
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
			}
		}

		$countSum = $supplySet->children->sum('count');

		

		$havingSum = $acquiredSum - $suppliedSum;
		$lack = $countSum - $havingSum;
		if($countSum > $havingSum){
			Session::flash('message', '보유수량이 부족합니다. (현재 보유수량: '.$havingSum.', '.$lack.'개 부족)');
			return Redirect::back();
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
		$data = array();
		$supply = EqItemSupplySet::find($id);

		$types = EqItemType::where('item_id','=',$supply->item->id)->get();
		$lowerNodes = EqSupplyManagerNode::where('parent_id','=',1)->get();
		$count = array();

		$data['types'] = $types;
		$data['supply'] = $supply;
		$data['item'] = $supply->item;
		$data['lowerNodes'] = $lowerNodes;
		
		foreach ($lowerNodes as $n) {
			$nodeSupplies = EqItemSupply::where('to_node_id','=',$n->id)->get();
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
        return View::make('equip.supplies-create');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$s = EqSupply::find($id);
		if (!$s) {
			return App::abort(404);
		}

		$s->details()->delete();
		$s->delete();
		return Redirect::to('equips/supplies');
	}

}
