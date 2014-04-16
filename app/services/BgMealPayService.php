<?php 

class BgMealPayService {
	
	public function getSitStatQuery($startMonth, $endMonth, $deptId, $groupByRegion)
	{
		$startDate = date('Y-m-01', strtotime($startMonth.'-01'));
		$endDate = date('Y-m-t', strtotime($endMonth.'-01'));

		$query = DB::table('bg_meal')
					->leftJoin('departments','departments.id','=','bg_meal.dept_id');

		$mobCodes = Code::withCategory('B002');

		$selects = array(
				DB::raw('DATE_FORMAT(mob_date, "%Y-%m") AS belong_month')
			);

		if ($groupByRegion)
		{
			$selects[] = 
				DB::raw('LEFT(departments.full_name, LOCATE(" ", departments.full_name, 2) ) AS dept_name');
		}
		else
		{
			$selects[] = DB::raw('TRIM(REPLACE(departments.full_name, ":", " ")) AS dept_name');
		}

		foreach ($mobCodes as $key=>$code)
		{
			if (!$groupByRegion) 
			{
				$sql = 'SELECT SUM(count_officer+count_officer_troop+count_troop) FROM bg_meal AS mob'.$key.'
					WHERE mob'.$key.'.mob_code = "'.$code->code.'" AND 
						mob'.$key.'.mob_date BETWEEN DATE_FORMAT(bg_meal.mob_date, "%Y-%m-01") AND LAST_DAY(bg_meal.mob_date) AND
						mob'.$key.'.dept_id = bg_meal.dept_id';
			}
			else
			{
				$sql = 'SELECT SUM(count_officer+count_officer_troop+count_troop) FROM bg_meal AS mob'.$key.'
					LEFT JOIN departments AS d ON d.id = mob'.$key.'.dept_id
					WHERE mob'.$key.'.mob_code = "'.$code->code.'" AND 
						mob'.$key.'.mob_date BETWEEN DATE_FORMAT(bg_meal.mob_date, "%Y-%m-01") AND LAST_DAY(bg_meal.mob_date) AND
						d.full_path LIKE CONCAT("%",LEFT(departments.full_path, LOCATE(":",departments.full_path,2)),"%")';
			}

			$selects[] = DB::raw('('.$sql.') AS c'.$key);
		}

		$selects[] = DB::raw('SUM(count_officer) AS count_officer');
		$selects[] = DB::raw('SUM(count_officer_troop) AS count_officer_troop');
		$selects[] = DB::raw('SUM(count_troop) AS count_troop');
		$selects[] = DB::raw('SUM(amount) AS amount');

		$query->select($selects)
				->where('mob_date', '>=', $startDate)
				->where('mob_date', '<=', $endDate)
				->groupBy(DB::raw('DATE_FORMAT(mob_date, "%Y-%m")'));

		if ($deptId)
		{
			$query->where('departments.full_path', 'like', "%:$deptId:%");
		}

		if ($groupByRegion)
		{
			// $query->groupBy('dept_id');
			$query->groupBy(DB::raw('LEFT(full_path, LOCATE(":",full_path,2))'));
		}
		else
		{
			$query->groupBy('dept_id');
		}

		$query->orderBy('departments.sort_order', 'asc');

		return $query;
	}

	public function getPayrollQuery($start, $end, $deptId, $mobCode)
	{
		$query = DB::table('bg_meal')
					->leftJoin('departments', 'departments.id','=','bg_meal.dept_id')
					->leftJoin('codes AS mobSit', function($query){
						$query->on('mobSit.code','=','bg_meal.mob_code')
							->where('mobSit.category_code', '=', 'B002');
					});

		$query->select(array(
				'bg_meal.id',
				'mob_date',
				DB::raw('TRIM(REPLACE(departments.full_name, ":", " ")) AS dept_name'),
				'mobSit.title',
				'event_name',
				DB::raw('count_officer+count_officer_troop+count_troop as total'),
				'count_officer',
				'count_officer_troop',
				'count_troop',
				'amount'
			));

		$query->where('mob_date', '>=', $start)->where('mob_date', '<=', $end);

		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.admin'))
		{
			$userDeptId = $user->dept_id;
			$query->where('departments.full_path', 'like', "%$userDeptId%");
		}

		if ($deptId)
		{
			$query->where('departments.full_path', 'like', '%:'.$deptId.':%');
		}

		if ($mobCode)
		{
			$query->where('mob_code','=',$mobCode);
		}

		return $query;
	}

	public function insertPayroll($data)
	{
		$configs = PSConfig::category('budget.mealpay');
		$editableStart = strtotime($this->getEditableDateStart());

		$user = Sentry::getUser();

		foreach ($data as $key=>$row)
		{
			if (!$user->hasAccess('budget.admin') && 
				strtotime($row['mob_date']) < $editableStart) 
			{
				return -1;
			}

			if (!$user->hasAccess('budget.admin') &&
				!Department::isAncestor($row['dept_id'], $user->dept_id))
			{
				return -2;
			}

			$data[$key]['amount'] = 
				($row['count_officer']?$row['count_officer']:0)*$configs['budget.mealpay.officer_amt']+
				($row['count_officer_troop']?$row['count_officer_troop']:0)*$configs['budget.mealpay.officer2_amt']+
				($row['count_troop']?$row['count_troop']:0)*$configs['budget.mealpay.troop_amt'];
		}

		DB::table('bg_meal')->insert($data);
		return 0;
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
		
		$forbiddens = DB::table('bg_meal')->whereIn('id', $ids)->where('mob_date', '<', $editableStart)->get();

		if (!Sentry::getUser()->hasAccess('budget.admin') && count($forbiddens) > 0) 
		{
			return -1;
		}

		DB::table('bg_meal')->whereIn('id', $ids)->delete();
		return 0;
	}
}