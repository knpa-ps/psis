<?php

class MenuController extends \BaseController {

	private $service;

	public function __construct() {
		$this->service = new MenuService;
	}

	/**
	 * Display a listing of the resource.
	 * GET /menu
	 *
	 * @return Response
	 */
	public function index()
	{
		$type = Input::get('type');

		$menus = Menu::orderBy('sort_order', 'asc')->get();

		if ($type === 'raw') {
			return $menus;
		}

		$nodes = array(
			array('id'=>Menu::ID_VISIBLE_ROOT, 'parent'=>'#', 'text'=>'visible', 'state'=>array('opened'=>true, 'disabled'=>true)),
			array('id'=>Menu::ID_HIDDEN_ROOT, 'parent'=>'#', 'text'=>'hidden', 'state'=>array('opened'=>true, 'disabled'=>true))
		);

		foreach ($menus as $menu) {
			$nodes[] = array(
					'id' => $menu->id,
					'parent' => $menu->parent_id,
					'text' => $menu->name,
					'state'=>array('opened'=>true)
				);
		}

		return $nodes;
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /menu
	 *
	 * @return Response
	 */
	public function store()
	{
		try {
			$menu = $this->service->create(Input::get('text'), Input::get('parent_id'), Input::get('position'), Input::get('type'));
			return $menu;
		} catch (Exception $e) {
			return App::abort(500, $e->getMessage());
		}
	}

	/**
	 * Display the specified resource.
	 * GET /menu/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$menu = Menu::find($id);

		if ($menu == null) {
			return App::abort(500, 'menu does not exists with id='.$id);
		}

		return $menu;		
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /menu/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$menu = Menu::find($id);

		if ($menu == null) {
			return App::abort(500, 'menu does not exists with id='.$id);
		}

		switch (Input::get('operation')) {
			case 'rename':
				$menu->name = Input::get('text');
				if (!$menu->save()) {
					return App::abort(500, 'db failed');
				}
			break;
			case 'move':
				try {
					$this->service->move($id, Input::get('parent_id'), Input::get('position'), Input::get('type'));
				} catch (Exception $e) {
					Log::error($e->getMessage());
					return App::abort(500, $e->getMessage());
				}
			break;
			case 'edit':
				$groupIds = Input::get('group_ids')?Input::get('group_ids'):array(); 

				$menu->browser_title = Input::get('browser_title');
				$menu->url = Input::get('url');
				$menu->group_ids = implode(',', $groupIds);
				if (!$menu->save()) {
					return App::abort(500, 'db failed');
				}
				return Lang::get('global.done');
		}

	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /menu/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->service->remove($id);
	}

}