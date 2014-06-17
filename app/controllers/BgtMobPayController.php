<?php

class BgtMobPayController extends \BaseController {

	private $service;

	public function __construct() {
		$this->service = new BgtMobPayService;
	}

	/**
	 * Display a listing of the resource.
	 * GET /budgets/mob-pay
	 *
	 * @return Response
	 */
	public function index()
	{
		$type = Input::get('type');
		if (!$type) {
			$type = 'raw';
		}

		// common variables
		$data = compact('type');

		return View::make('budget.mob-pay.data-'.$type, $data);
	}

	public function read() {

		$type = Input::get('type');
		if (!$type) {
			return App::abort(400);
		}

		$user = Sentry::getUser();

		$query = $this->service->getMasterDataQuery($user, Input::all());

		switch ($type) {
			case 'raw':
				return Datatable::query($query)
		        ->showColumns('id', 'use_date')
		        ->addColumn('dept_name', function($model) {
		        	return $model->department->full_name;
		        })
		        ->addColumn('situation', function($model) {
	        		return $model->situation->title;
		        })
		        ->addColumn('event_name', function($model) {
		        	return '<a href="'.url('budgets/mob-pay').'/'.$model->id.'">'.str_limit($model->event_name, 35).'</a>';
		        })
		        ->addColumn('amount', function($model) {
		        	return number_format($model->details()->sum('amount'));
		        })
		        ->make();
			case 'stat-sit':

				break;
			default:
				return App::abort(400);
		}
	}


	/**
	 * GET /budgets/mob-pay/$id
	 *
	 * @return Response
	 */
	public function show($id) {

		$type = 'raw';
		$data = BgtMobPayMaster::find($id);

		if ($data === null) {
			return App::abort(404);
		}
		$user = Sentry::getUser();
		$permissions = $this->service->getPermissions($user, $data);

		if (!$permissions['read']) {
			return App::abort(403);
		}

		return View::make('budget.mob-pay.detail', compact('type', 'data', 'user'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /budgets/mob-pay/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('budget.mob-pay.insert');
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /budgets/mob-pay/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$master = BgtMobPayMaster::find($id);
		if ($master == null) {
			return App::abort(404);
		}

		$user = Sentry::getUser();
		
		$permissions = $this->service->getPermissions($user, $master);
		
		if (!$permissions['update']) {
			return App::abort(403);
		}

		return View::make('budget.mob-pay.edit', compact('user', 'master'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /budgets/mob-pay
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$user = Sentry::getUser();
		try {
			$this->service->insert($user, $input);
		} catch (Exception $e) {
			return App::abort($e->getCode());
		}
		return array(
				'result'=>0,
				'message'=>'입력되었습니다.',
				'url'=>url('budgets/mob-pay')
			);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /budgets/mob-pay/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		$user = Sentry::getUser();
		try {
			$this->service->update($user, $id, $input);
		} catch (Exception $e) {
			Log::error($e->getMessage());
			return App::abort($e->getCode());
		}
		return array(
				'result'=>0,
				'message'=>'수정되었습니다.',
				'url'=>url('budgets/mob-pay/'.$id)
			);
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /budgets/mob-pay/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$user = Sentry::getUser();

		try {
			$this->service->delete($user, $id);
		} catch (Exception $e) {
			return App::abort($e->getCode());
		}

		return array(
				'message'=>'삭제되었습니다',
				'result' => 0,
				'url' => action('BgtMobPayController@index')
			);
	}

}