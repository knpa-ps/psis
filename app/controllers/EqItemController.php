<?php

class EqItemController extends EquipController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        $user = Sentry::getUser();
		$code = EqItemCode::where('code','=',Input::get('code'))->first();
		
		$data['code'] = $code;
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
		$data = Input::all();
		$item = new EqItem;

		DB::beginTransaction();
		$item->classification = $data['item_classification'];
		$item->supplier = $data['supplier'];
		$item->item_code = $data['item_code'];
		$item->maker_name = $data['item_maker_name'];
		$item->maker_phone = $data['item_maker_phone'];
		$item->acquired_date = $data['item_acquired_date'];
		$item->persist_years = $data['item_persist_years'];
		$item->is_active = 1;
		if (!$item->save()) {
			return App::abort(400);
		}

		// 이미지 동기화
		$item->images()->delete();

		$images = Input::get('item_images');
		if($images){
			foreach ($images as $url) {
				$img = new EqItemImage;
				$img->item_id = $item->id;
				$img->url = $url;
				if (!$img->save()) {
					return App::abort(400);
				}
			}
		}

		$types = $data['type'];
		for ($i=0; $i < sizeof($types); $i++) { 
			$itemType = new EqItemType;
			$itemType->type_name = strtoupper($types[$i]);
			$itemType->item_id = $item->id;
			if(!$itemType->save()){
				return App::abort(400);
			}
		}


		DB::commit();

		Session::flash('message', '저장되었습니다.');
		return Redirect::action('EqItemCodeController@show', array('code'=>$data['item_code']));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{

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


		$code = EqItemCode::where('code','=',$item->code->code)->first();
		
		$data['code'] = $code;
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
		$data = Input::all();
		$item = EqItem::find($id);
		if (!$item) {
			return App::abort(404);
		}

		DB::beginTransaction();
		$item->classification = $data['item_classification'];
		$item->supplier = $data['supplier'];
		$item->item_code = $data['item_code'];
		$item->maker_name = $data['item_maker_name'];
		$item->maker_phone = $data['item_maker_phone'];
		$item->acquired_date = $data['item_acquired_date'];
		$item->persist_years = $data['item_persist_years'];
		$item->is_active = 1;
		if (!$item->save()) {
			return App::abort(400);
		}

		// 이미지 동기화
		$item->images()->delete();

		$images = Input::get('item_images');
		if($images){
			foreach ($images as $url) {
				$img = new EqItemImage;
				$img->item_id = $item->id;
				$img->url = $url;
				if (!$img->save()) {
					return App::abort(400);
				}
			}
		}

		DB::commit();

		Session::flash('message', '수정되었습니다.');
		return Redirect::action('EqItemCodeController@index');
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

		$item->is_active = 0;
		if (!$item->save()) {
			return App::abort(500);
		}

		return array('message'=>'일괄 폐기되었습니다.', 'result'=>0);
	}

}
