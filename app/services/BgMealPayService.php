<?php 

class BgMealPayService {

	public function buildSumQuery($start, $end, $region, $event)
	{
		$sumQuery = DB::table('bg_meal_pays')->select(array(
					DB::raw('0 as type'),
					DB::raw('0 as sort_order'),
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
					DB::raw("format(sum(officer_amt+officer2_amt+troop_amt),0) as amt")
				));

		if ($start)
		{
			$sumQuery->where('bg_meal_pays.use_date', '>=', $start);
		}

		if ($end)
		{
			$sumQuery->where('bg_meal_pays.use_date', '<=', $end);
		}

		if ($region)
		{
			$sumQuery->where('bg_meal_pays.dept_id', '=', $region);
		}

		if ($event)
		{
			$sumQuery->where('bg_meal_pays.event_name', 'like', $event);
		}

		return $sumQuery;
	}

	public function buildQuery($start, $end, $region, $event, $groupByMonth)
	{
		$dataQuery = DB::table('bg_meal_pays')->leftJoin('departments', 'departments.id','=','bg_meal_pays.dept_id');
		if ($groupByMonth)
		{
			$dataQuery->select(array(
					DB::raw('1 as type'),
					'departments.sort_order',
					DB::raw('"" as id'),
					DB::raw('date_format(bg_meal_pays.use_date, "%Y-%m")'),
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
					DB::raw("format(sum(officer_amt+officer2_amt+troop_amt),0) as amt")
				))->groupBy(DB::raw('DATE_FORMAT(bg_meal_pays.use_date, "%y-%m")'))
				->groupBy('bg_meal_pays.dept_id');
		}
		else
		{
			$dataQuery->select(array(
					DB::raw('1 as type'),
					'departments.sort_order',
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
					DB::raw("format(officer_amt+officer2_amt+troop_amt,0) as amt")
				));
		}

		if ($start)
		{
			$dataQuery->where('bg_meal_pays.use_date', '>=', $start);
		}

		if ($end)
		{
			$dataQuery->where('bg_meal_pays.use_date', '<=', $end);
		}

		if ($region)
		{
			$dataQuery->where('bg_meal_pays.dept_id', '=', $region);
		}

		if ($event)
		{
			$dataQuery->where('bg_meal_pays.event_name', 'like', $event);
		}

		$sumQuery = $this->buildSumQuery($start, $end, $region, $event);
		return DB::table(DB::raw('( ('.$sumQuery->toSql().') UNION ALL ('.$dataQuery->toSql().') ) AS tb'))
		->setBindings(array_merge($sumQuery->getBindings(), $dataQuery->getBindings()))
		->orderBy('type', 'asc')
		->orderBy('use_date', 'desc')
		->orderBy('sort_order', 'asc')
		->select(array(
				'id',
				'use_date',
				'dept_name',
				'event_name',
				'sum1',
				'dc',
				'ec',
				'cc',
				'rc',
				'etc',
				'sum2',
				'oc',
				'o2c',
				'tcn',
				'amt'
			));
	}

	public function getEditableDateStart()
	{
		$belongMonth = date('Y-m-01', strtotime('-1 month'));
		$closeDate = $this->getCloseDate($belongMonth);

		if (time() < strtotime($closeDate))
		{
			$closeDate = $this->getCloseDate(date('Y-m-01', strtotime('-1 month', strtotime($belongMonth))));
		}

		return date('Y-m-d', strtotime('+1 day', strtotime($closeDate)));	
	}

	public function getCloseDate($belongMonth)
	{
		$result = DB::table('bg_meal_pays_close_date')->where('belong_month', '=', $belongMonth)->first();
		if (!$result)
		{
			$configs = PSConfig::category('budget.mealpay');
			$dateNum = isset($configs['budget.mealpay.close_date']) ? $configs['budget.mealpay.close_date'] : '-1';
			$time = isset($configs['budget.mealpay.close_time']) ? $configs['budget.mealpay.close_time'] : '00:00';

			if ($dateNum == -1)
			{
				$closeDate = date('Y-m-t '.$time, strtotime('+1 month', strtotime($belongMonth)));
			}
			else
			{
				$closeDate = date('Y-m-'.$dateNum.' '.$time, strtotime('+1 month', strtotime($belongMonth)));
			}
			
			DB::table('bg_meal_pays_close_date')->insert(array(
						'belong_month'=>$belongMonth,
						'close_date'=>$closeDate
					));
		}
		else
		{
			$closeDate = $result->close_date;
		}
		return $closeDate;
	}

	public function delete($ids)
	{
		$editableStart = $this->getEditableDateStart();
		$forbiddens = DB::table('bg_meal_pays')->whereIn('id', $ids)->where('use_date', '<', $editableStart)->get();
		if (!Sentry::getUser()->isSuperUser() && count($forbiddens) > 0) 
		{
			return -1;
		}

		DB::table('bg_meal_pays')->whereIn('id', $ids)->delete();
		return 0;
	}

	public function create($data)
	{
		$editableStart = $this->getEditableDateStart();
		if (!Sentry::getUser()->isSuperUser() && strtotime($editableStart) > strtotime($data['use_date']))
		{
			return -1;
		}
		$configs = PSConfig::category('budget.mealpay');
		$data['officer_amt'] = $configs['budget.mealpay.officer_amt']*$data['officer_cnt'];
		$data['officer2_amt'] = $configs['budget.mealpay.officer2_amt']*$data['officer2_cnt'];
		$data['troop_amt'] = $configs['budget.mealpay.troop_amt']*$data['troop_cnt'];
		DB::table('bg_meal_pays')->insert($data);
		return 0;
	}
}