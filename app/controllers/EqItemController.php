<?php

class EqItemController extends EquipController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function deletePost($itemId, $id){
		$detail = EqItemDetail::find($id);
		if($detail->delete()){
			return array(
				'result'=>0,
				'message'=>'삭제되었습니다.',
				'url'=>url('equips/items/'.$itemId.'/details')
			);
		}
	}
	public function displayUpdatePostForm($itemId,$id){
		$detail = EqItemDetail::find($id);
		$data['creator_name'] = $detail->creator->user_name;
		$data['itemId'] = $itemId;
		$data['id'] = $id;
		$data['title'] = $detail->title;
		$data['content'] = $detail->content;
		return View::make('equip.item-detail-update', $data);
	}
	public function UpdatePost($itemId,$id){
		$input = Input::all();
		$detail = EqItemDetail::find($id);
		$detail->title = $input['title'];
		$detail->content = $input['input_body'];
		if(!$detail->update()){
			return App::abort(400);
		}
		Session::flash('message', '수정되었습니다');
		return Redirect::action('EqItemController@displayExtraInfo', array('itemId'=>$itemId, 'id'=>$id));
	}
	public function displayExtraInfo($itemId,$id){
		$detail = EqItemDetail::find($id);
		$data = compact('detail');
		$data['itemId'] = $detail->item_id;
		$data['id'] = $id;
		return View::make('equip.item-detail',$data);
	}
	public function displayDetailsList($itemId){
		$details = EqItemDetail::where('item_id','=',$itemId)->get();
		$data = compact('details');
		$data['itemId'] = $itemId;
		return View::make('equip.items-details-list',$data);
	}
	public function displayDetailForm($itemId){
		$user = Sentry::getUser();
		$data = compact('user');
		$data['itemId'] = $itemId;
		return View::make('equip.item-detail-new', $data);
	}

	public function doPost($itemId){
		$input = Input::all();
		$user = Sentry::getUser();

		$detail = new EqItemDetail;
		$detail->title = $input['title'];
		$detail->content = $input['input_body'];
		$detail->item_id = $itemId;
		$detail->creator_id = $user->id;
		if(!$detail->save()){
			return App::abort(400);
		}

		Session::flash('message', '저장되었습니다.');
		return Redirect::action('EqItemController@displayDetailsList', $itemId);
	}

	public function index()
	{
		$user = Sentry::getUser();

		$data['domains'] = $this->service->getVisibleDomains($user);

		if (count($data['domains']) == 0) {
			return App::abort(403);
		}

		$domainId = Input::get('domain');

		if (!$domainId) {
			$domainId = $data['domains'][0]->id;
		}

		if (!$user->hasAccess(EqDomain::find($domainId)->permission)) {
			return App::abort(403);
		}

		$data['user'] = $user;

		$data['items'] =  EqItem::whereHas('category', function($q) use ($domainId) {
									$q->where('domain_id', '=', $domainId);
								})->orderBy('category_id', 'asc')->orderBy('name', 'asc')->get();

		$data['domainId'] = $domainId;
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

		if ($images) {
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

	/**http://localhost/psis/equips/inventories
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

	public function getData($id) {

		$parentId = Input::get('parent');

		if (!$parentId) {
			$depts = Department::regions()->get();
		} else {
			$parent = Department::find($parentId);
			if (!$parent) {
				return App::abort(400);
			}
			$depts = $parent->children()->get();
		}

		$data = array();
		foreach ($depts as $dept) {
			$row[0] = $dept->id;
			$row[1] = $dept->full_name;
			$row[2] = 0;//TODO
			$row[3] = EqInventory::whereHas('department', function($q) use ($dept) {
							$q->where('full_path', 'like', $dept->full_path.'%');
						})->sum('count');
			$row[4] = 0;
			$data[] = $row;
		}

		return array('data'=>$data);		
	}
}
