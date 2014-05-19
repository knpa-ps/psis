<?php

class EqItemController extends EquipController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Sentry::getUser();
		
		$data['categories'] = $this->service->getVisibleCategoriesQuery($user)->get();
		$data['user'] = $user;
		$data['items'] = $this->service->getVisibleItemsQuery($user)->paginate(15);
		
        return View::make('equip.items', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$user = Sentry::getUser();
		$data['mode'] = 'create';
		$data['categories'] = $this->service->getVisibleCategoriesQuery($user)->get();
		$data['user'] = $user;
        return View::make('equip.items-basic-form', $data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$item = new EqItem;

		DB::beginTransaction();
		$item->name = Input::get('item_name');
		$item->category_id = Input::get('item_category_id');
		$item->standard = Input::get('item_standard');
		$item->unit = Input::get('item_unit');
		$item->persist_years = Input::get('item_persist_years');
		if (!$item->save()) {
			return App::abort(400);
		}

		$images = Input::get('item_images');
		foreach ($images as $url) {
			$img = new EqItemImage;
			$img->item_id = $item->id;
			$img->url = $url;
			if (!$img->save()) {
				return App::abort(400);
			}
		}

		DB::commit();

		Session::flash('message', '추가되었습니다.');
		return Redirect::to('equips/items/'.$item->id);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$item = EqItem::find($id);
		if ($item == null) {
			return App::abort(404);
		}


		$data = compact('item');
		return View::make('equip.items-show', $data);
	}

	public function showInventories($id) {
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$item = EqItem::find($id);
		if (!$item) {
			return App::abort(404);
		}

		$user = Sentry::getUser();
		$data['mode'] = 'edit';
		$data['item'] = $item;
		$data['categories'] = $this->service->getVisibleCategoriesQuery($user)->get();
		$data['user'] = $user;
        return View::make('equip.items-basic-form', $data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$item = EqItem::find($id);
		if (!$item) {
			return App::abort(404);
		}

		DB::beginTransaction();
		$item->name = Input::get('item_name');
		$item->category_id = Input::get('item_category_id');
		$item->standard = Input::get('item_standard');
		$item->unit = Input::get('item_unit');
		$item->persist_years = Input::get('item_persist_years');
		if (!$item->save()) {
			return App::abort(400);
		}

		// 이미지 동기화
		$item->images()->delete();

		$images = Input::get('item_images');
		foreach ($images as $url) {
			$img = new EqItemImage;
			$img->item_id = $item->id;
			$img->url = $url;
			if (!$img->save()) {
				return App::abort(400);
			}
		}

		DB::commit();

		Session::flash('message', '수정되었습니다.');
		return Redirect::to('equips/items/'.$id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$item = EqItem::find($id);
		if (!$item) {
			return App::abort(404);
		}
		
		if ($item->inventories()->count() > 0) {
			return array('message'=>'보유내역이 있는 장비는 삭제할 수 없습니다.', 'result'=>-1);
		}

		DB::beginTransaction();
		$item->details()->delete();
		$item->images()->delete();
		$item->delete();
		DB::commit();

		return array('message'=>'삭제되었습니다.', 'result'=>0);
	}


}
