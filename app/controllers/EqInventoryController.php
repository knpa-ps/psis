<?php

class EqInventoryController extends BaseController {

	private $service;

	public function __construct() {
		$this->service = new EqService;
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
		$inventories = $this->service->getInventoriesQuery($user)->get();
        return View::make('equip.inventories-index', get_defined_vars());
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

		$inventory = new EqInventory;
		$inventory->item_id = $data['item'];
		$inventory->dept_id = $user['dept_id'];
		$inventory->count = $data['count'];
		$inventory->model_name = $data['model_name'];
		$inventory->acq_date = $data['acquired_date'];
		$inventory->acq_route = $data['acquired_route'];
		if(!$inventory->save()){
			return App::abort(400);
		}
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
		if($inventory->delete()){
			return '삭제되었습니다.';
		} else {
			App::abort(500);
		}
	}

}
