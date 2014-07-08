<?php

class EqSupplyController extends BaseController {

	public function getClassifiers(){
		$itemId = Input::get('item_id');
		$inventories = EqInventory::where('item_id','=',$itemId)->get();
		$res = array();
		if(sizeof($inventories)!==0){
			$options = '';
			foreach ($inventories as $i) {
				$options = $options.'<option value="'.$i->id.'">'.$i->model_name.' ('.$i->acq_date.')</option>';
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

		$query = EqSupply::where('supply_date', '>=', $start)->where('supply_date', '<=', $end);

		if ($itemName) {
			$query->whereHas('item', function($q) use ($itemName) {
				$q->where('name', 'like', "%$itemName%");
			});
		}

		$data = $query->paginate(15);

        return View::make('equip.supplies-index', get_defined_vars());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$items = EqItem::has('inventories')->get();
		$data = compact('items');
		$data['mode'] = 'create';
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

		$supply = new EqSupply;

		$supply->supply_dept_id = $user->dept_id;
		$supply->creator_id = $user->id;
		$supply->item_id = $data['item'];
		$supply->title = $data['title'];
		$supply->supply_date = $data['supply_date'];
		$supply->inventory_id = $data['classifier'];

		if(!$supply->save()){
			App::abort(500);
		}

		return Redirect::to('equips/supplies/'.$supply->id);

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$supply = EqSupply::find($id);
		if (!$supply) {
			return App::abort(404);
		}



        return View::make('equip.supplies-show', get_defined_vars());
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
