<?php

class BgMobController extends BaseController {

	private $service;

	public function __construct() {
		$this->service = new BgMobService;
	}

	public function show()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mob.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$mobCodes = Code::withCategory('B002');
		$rankCodes = Code::withCategory('H001');
		$editableStart = $this->service->getEditableDateStart();

        return View::make('budget.mob.payroll', get_defined_vars());
	}

	public function showSitStat()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mob.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		return View::make('budget.mob.sit_stat');
	}

	public function showDeptStat()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mob.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		return View::make('budget.mob.dept_stat');
	}

	public function getPayrollData() 
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mob.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$start = Input::get('q_date_start');
		$end = Input::get('q_date_end');
		$deptId = Input::get('q_dept_id');
		$mobCode = Input::get('q_mob_code');
		$actual = Input::get('q_actual');

		$query = $this->service->getPayrollQuery($start, $end, $deptId, $mobCode, $actual);

		$output = Datatables::of($query)->make(FALSE, FALSE);

		foreach ($output['aaData'] as $key=>$row)
		{
			$output['aaData'][$key][9] = number_format($row[9]) ;
		}

		return Response::json($output);
	}

	public function insertPayroll()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mob.create'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$input = Input::all();

		$data = array();

		$n = count($input['i_dept_id']);

		for ($i=0; $i<$n; $i++)
		{
			$startTime = $input['i_mob_start'][$i];
			$endTime = $input['i_mob_end'][$i];

			$data[] = array(
					'dept_id'=>$input['i_dept_id'][$i],
					'rank_code'=>$input['i_rank'][$i],
					'receiver_name'=>$input['i_name'][$i],
					'mob_date'=>$input['i_date'][$i],
					'mob_code'=>$input['i_mob_code'][$i],
					'mob_summary'=>$input['i_mob_summary'][$i],
					'start_time'=>$startTime,
					'end_time'=>$endTime,
					'actual'=>$input['i_actual'][$i],
					'creator_id'=>$user->id
				);
		}

		return $this->service->insertPayroll($data);
	}

	public function deletePayroll()
	{
		if (!Sentry::getUser()->hasAccess('budget.mob.delete'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$ids = Input::all();
		if (count($ids) == 0)
		{
			return 0;
		}

		return $this->service->delete($ids);
	}

	public function exportPayroll()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mob.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$start = Input::get('q_date_start');
		$end = Input::get('q_date_end');
		$deptId = Input::get('q_dept_id');
		$mobCode = Input::get('q_mob_code');
		$actual = Input::get('q_actual');

		$result = $this->service->getPayrollQuery($start, $end, $deptId, $mobCode, $actual)->get();

		$objPHPExcel = PHPExcel_IOFactory::load(public_path().'/static/misc/export/mob.xlsx');

		$activeSheet = $objPHPExcel->setActiveSheetIndex(0);

		if ($deptId)
		{
			$dept = Department::where('id','=',$deptId)->first();
			$deptName = $dept->full_name;
		}
		else
		{
			$deptName = '전체';
		}

		$activeSheet->setCellValue('B2', $start)
					->setCellValue('C2', $end)
					->setCellValue('F2', $deptName)
					->setCellValue('I2', $actual?'O':':');

		$rowNum = 5;
		foreach ($result as $row)
		{
			$colNum = 0;
			foreach ($row as $key=>$col)
			{
				$colString = PHPExcel_Cell::stringFromColumnIndex($colNum);
				$activeSheet->setCellValue($colString.$rowNum, $col);
				$colNum++;
			}
			$rowNum++;
		}

		$fileName = date('ymd')."_export";

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
}
