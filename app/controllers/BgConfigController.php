<?php

class BgConfigController extends BaseController {

	public function __construct()
	{
		if (!Sentry::getUser()->hasAccess('budget.admin'))
		{
			return App::abort(404,'unauthorized action');
		}

	}

	public function showMob()
	{
		$configs = PSConfig::category('budget');
		$costs = DB::table('bg_mob_cost')->get();
		return View::make('budget.config.mob', get_defined_vars());
	}

	public function show()
	{
		$configs = PSConfig::category('budget');
		return View::make('budget.config.mealpay', get_defined_vars());
	}

	public function createCloseDate()
	{
		$bm = Input::get('bm');
		$cd = Input::get('cd');

		if (Input::get('type') == 'mob')
		{
			$table = 'bg_mob_close_date';
		}
		else
		{
			$table = 'bg_meal_pays_close_date';
		}

		DB::table($table)->insert(array(
				'belong_month'=>$bm.'-01',
				'close_date'=>$cd
			));
		return 0;
	}

	public function readCloseDates()
	{

		if (Input::get('type') == 'mob')
		{
			$table = 'bg_mob_close_date';
		}
		else
		{
			$table = 'bg_meal_pays_close_date';
		}

		$query = DB::table($table)
		->select(array('id',
			DB::raw('DATE_FORMAT(belong_month,"%Y-%m") as belong_month'), 
			'close_date'))
		->orderBy('belong_month','desc');
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

		if (Input::get('type') == 'mob')
		{
			$table = 'bg_mob_close_date';
		}
		else
		{
			$table = 'bg_meal_pays_close_date';
		}

		DB::table($table)->whereIn('id', $ids)->delete();
		return 0;
	}

	public function updateMobCost()
	{
		$data = array();
		foreach (Input::all() as $key=>$cost)
		{
			$explodes = explode('_', $key);
			if (count($explodes)!=2){
				continue;
			}
			$id = $explodes[1];
			DB::table('bg_mob_cost')
				->where('id','=',$id)
				->update(
					array(
						'cost'=>$cost
						)
				);
		}

		return 0;
	}
}
