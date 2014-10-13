<?php

class EqCategoryController extends EquipController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Sentry::getUser();
		
		$data['categories'] = $this->service->getVisibleCategoriesQuery($user)->paginate(10);
		$data['user'] = $user;
		
        return View::make('equip.categories', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$user = Sentry::getUser();
		$domains = $this->service->getVisibleDomains($user);
		$type = 'create';

        return View::make('equip.categories-detail', compact('domains', 'type'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();

		$validator = Validator::make($input, array(
				'domain_id'=>'required|exists:eq_domains,id',
				'category_name'=>'required|max:255'
			));

		if ($validator->fails()) {
			Session::flash('message', '입력 값이 올바르지 않습니다.');
			return Redirect::action('EqCategoryController@create');
		}
		
		$c = new EqCategory;
		$c->domain_id = Input::get('domain_id');
		$c->name = Input::get('category_name');
		$c->save();

		Session::flash('message', '입력되었습니다');
		return Redirect::action('EqCategoryController@index');
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
		$domains = $this->service->getVisibleDomains($user);

		$category = EqCategory::find($id);
		$type = 'edit';
        return View::make('equip.categories-detail', compact('domains', 'user', 'category', 'type'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$c = EqCategory::find($id);
		$c->domain_id = Input::get('domain_id');
		$c->name = Input::get('category_name');
		$c->save();
		Session::flash('message', '수정되었습니다.');
		return Redirect::action('EqCategoryController@index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$c = EqCategory::find($id);	
		if ($c->items->count() > 0) {
			return array('result'=>-1, 'message'=>'귀속된 장비 항목이 있는 분류는 삭제할 수 없습니다.');
		}
		$c->delete();
		return array('result'=>0,'message'=>'삭제되었습니다.');
	}

}
