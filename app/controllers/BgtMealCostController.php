<?php

class BgtMealCostController extends \BaseController {

	private $service;

	public function __construct() {
		$this->service = new BgtMealCostService;
	}

	/**
	 * Display a listing of the resource.
	 * GET /budgets/meal-cost
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

		return View::make('budget.meal-cost.data-'.$type, $data);
	}

	public function read() {

		$user = Sentry::getUser();

		$query = $this->service->getDataQuery($user, Input::all());

		return Datatable::query($query)
        ->showColumns('id', 'use_date')
        ->addColumn('dept_name', function($model) {
        	return $model->department->full_name;
        })
        ->addColumn('situation', function($model) {
    		return $model->situation->title;
        })
        ->showColumns('event_name')
        ->addColumn('use_type', function($model) {
    		return $model->useType->title;
        })
        ->addColumn('count', function($model) {
        	return number_format($model->meal_count);
        })
        ->addColumn('amount', function($model) {
        	return number_format($model->meal_amount);
        })
        ->make();
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /budgets/meal-cost/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('budget.meal-cost.insert');
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /budgets/meal-cost
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
			Log::error($e->getMessage());
			return App::abort($e->getCode());
		}
		return array(
				'result'=>0,
				'message'=>'입력되었습니다.',
				'url'=>url('budgets/meal-cost')
			);
	}

	/**
	 * Display the specified resource.
	 * GET /budgets/meal-cost/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /budgets/meal-cost/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /budgets/meal-cost/{id}
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
	 * DELETE /budgets/meal-cost/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
}