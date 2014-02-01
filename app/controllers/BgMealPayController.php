<?php

class BgMealPayController extends BaseController {

	public function show()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('mealpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		if ($user->hasAccess('budget.admin'))
		{
			$region = Department::regions()->toArray();
		}
		else
		{
			$region = Department::region($user->dept_id);
		}

		$configs = PSConfig::category('mealpay');


		$closeDate = $this->getCloseDate(date('Y-m-01'));

		return View::make('budget.mealpay', array(
				'region'=>$region,
				'user'=>$user,
				'configs' => $configs
			));
	}

	public function read()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('mealpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$configs = PSConfig::category('mealpay');
		$sumQuery = DB::table('bg_meal_pays')->select(array(
					DB::raw('0 as id'),
					DB::raw('"" as use_date'),
					DB::raw('"합계" as dept_name'),
					DB::raw('"" as event_name'),
					DB::raw('sum( demo_cnt+escort_cnt+crowd_cnt+rescue_cnt+etc_cnt ) as sum1'),
					DB::raw('sum(demo_cnt) as dc'),
					DB::raw('sum(escort_cnt) as ec'),
					DB::raw('sum(crowd_cnt) as cc '),
					DB::raw('sum(rescue_cnt) as rc '),
					DB::raw('sum(etc_cnt) as etc '),
					DB::raw('sum(officer_cnt+officer2_cnt+troop_cnt) as sum2'),
					DB::raw('sum(officer_cnt) as oc'),
					DB::raw('sum(officer2_cnt) as o2c'),
					DB::raw('sum(troop_cnt) as tcn'),
					DB::raw("format(sum(officer_cnt*{$configs['mealpay.officer_amt']} + officer2_cnt*{$configs['mealpay.officer2_amt']}+
					troop_cnt*{$configs['mealpay.troop_amt']}),0) as amt")
				));

		$dataQuery = DB::table('bg_meal_pays')->leftJoin('departments', 'departments.id','=','bg_meal_pays.dept_id');
		if (Input::get('monthly_sum'))
		{
			$dataQuery->select(array(
					DB::raw('"" as id'),
					DB::raw('date_format(bg_meal_pays.use_date, "%y-%m")'),
					DB::raw('departments.dept_name'),
					DB::raw('"" as event_name'),
					DB::raw('sum( demo_cnt+escort_cnt+crowd_cnt+rescue_cnt+etc_cnt ) as sum1'),
					DB::raw('sum(demo_cnt) as dc'),
					DB::raw('sum(escort_cnt) as ec'),
					DB::raw('sum(crowd_cnt) as cc '),
					DB::raw('sum(rescue_cnt) as rc '),
					DB::raw('sum(etc_cnt) as etc '),
					DB::raw('sum(officer_cnt+officer2_cnt+troop_cnt) as sum2'),
					DB::raw('sum(officer_cnt) as oc'),
					DB::raw('sum(officer2_cnt) as o2c'),
					DB::raw('sum(troop_cnt) as tcn'),
					DB::raw("format(sum(officer_cnt*{$configs['mealpay.officer_amt']} + officer2_cnt*{$configs['mealpay.officer2_amt']}+
					troop_cnt*{$configs['mealpay.troop_amt']}),0) as amt")
				))->groupBy('DATE_FORMAT(bg_meal_pays.use_date, "%y-%m")')
				->groupBy('bg_meal_pays.dept_id');
		}
		else
		{
			$dataQuery->select(array(
					'bg_meal_pays.id',
					'bg_meal_pays.use_date',
					'departments.dept_name',
					'bg_meal_pays.event_name',
					DB::raw('demo_cnt+escort_cnt+crowd_cnt+rescue_cnt+etc_cnt as sum1'),
					'demo_cnt',
					'escort_cnt',
					'crowd_cnt',
					'rescue_cnt',
					'etc_cnt',
					DB::raw('officer_cnt+officer2_cnt+troop_cnt as sum2'),
					'officer_cnt',
					'officer2_cnt',
					'troop_cnt',
					DB::raw("format(officer_cnt*{$configs['mealpay.officer_amt']} + officer2_cnt*{$configs['mealpay.officer2_amt']}+
					troop_cnt*{$configs['mealpay.troop_amt']},0) as amt")
				));
		}

		$start = Input::get('q_date_start');

		if ($start)
		{
			$dataQuery->where('bg_meal_pays.use_date', '>=', $start);
			$sumQuery->where('bg_meal_pays.use_date', '>=', $start);
		}

		$end = Input::get('q_date_end');

		if ($end)
		{
			$dataQuery->where('bg_meal_pays.created_at', '<=', $end);
			$sumQuery->where('bg_meal_pays.created_at', '<=', $end);
		}

		$region = Input::get('q_region');
		if ($region)
		{
			$dataQuery->where('bg_meal_pays.dept_id', '=', $region);
			$sumQuery->where('bg_meal_pays.dept_id', '=', $region);
		}

		$dataQuery->orderBy('bg_meal_pays.use_date','desc')->orderBy('departments.sort_order');

		return Datatables::of($sumQuery->unionAll($dataQuery))->make();
	}

	public function create()
	{

	}

	public function delete()
	{

	}

	public function update()
	{

	}

	public function setClosed()
	{

	}

	private function getCloseDate($belongMonth)
	{
		$closeDate = DB::table('bg_meal_pay_close_date')->where('belong_month', '=', $belongMonth)->first();
		if (!$closeDate)
		{
			$configs = PSConfig::category('mealpay');
			$dateNum = isset($configs['mealpay.close_date']) ? $configs['mealpay.close_date'] : '-1';
			$time = isset($configs['mealpay.close_time']) ? $configs['mealpay.close_time'] : '00:00';

			if ($dateNum == -1)
			{
				$closeDate = date('Y-m-t '.$time, strtotime('+1 month', strtotime($belongMonth)));
			}
			else
			{
				$closeDate = date('Y-m-'.$dateNum.' '.$time, strtotime('+1 month', strtotime($belongMonth)));
			}
			
			DB::table('bg_meal_pay_close_date')->insert(array(
						'belong_month'=>$belongMonth,
						'close_date'=>$closeDate
					));
		}
		return $closeDate;
	}
}
