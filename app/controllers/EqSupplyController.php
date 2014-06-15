<?php

class EqSupplyController extends BaseController {

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
		
        return View::make('equip.supplies-create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
        return View::make('eqsupplies.edit');
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
