<?php

class BgConfigController extends BaseController {

	public function __construct()
	{
		if (!Sentry::getUser()->hasAccess('budget.admin'))
		{
			return App::abort(404,'unauthorized action');
		}

	}

	public function show()
	{

		$configs = PSConfig::category('budget');
        return View::make('budget.config', get_defined_vars());
	}

	public function createCloseDate()
	{
		$bm = Input::get('bm');
		$cd = Input::get('cd');

		DB::table('bg_meal_pays_close_date')->insert(array(
				'belong_month'=>$bm.'-01',
				'close_date'=>$cd
			));
		return 0;
	}

	public function readCloseDates()
	{
		$query = DB::table('bg_meal_pays_close_date')->select(array('id','belong_month', 'close_date'))->orderBy('belong_month','desc');
		return Datatables::of($query)->make();
	}

	public function deleteCloseDates()
	{
		$ids = Input::all();

		if (count($ids) == 0) {
			return -2;
		}

		$forbiddens = DB::table('bg_meal_pays_close_date')->whereIn('id', $ids)->where('close_date','<',DB::raw('NOW()'))->get();
		if (count($forbiddens)>0)
		{
			return -1;
		}

		DB::table('bg_meal_pays_close_date')->whereIn('id', $ids)->delete();
		return 0;
	}
}
