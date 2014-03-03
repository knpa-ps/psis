<?php

class BgMealPayController extends BaseController {

	/**
	 * @var BgMealPayService
	 */
	private $service;

	public function __construct() {
		$this->service = new BgMealPayService;
	}

	public function show()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mealpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$mobCodes = Code::withCategory('B002');
		$configs = PSConfig::category('budget.mealpay');

		$editableStart = $this->service->getEditableDateStart();

		return View::make('budget.meal.payroll', get_defined_vars());
	}

	public function getPayrollData()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mealpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$start = Input::get('q_date_start');
		$end = Input::get('q_date_end');
		$deptId = Input::get('q_dept_id');
		$mobCode = Input::get("q_mob_code");
		$query = $this->service->getPayrollQuery($start, $end, $deptId, $mobCode);
		
		$output = Datatables::of($query)->make(FALSE, FALSE);
		return Response::json($output);
	}

	public function insertPayroll()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mealpay.create'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$input = Input::all();

		$data = array();

		$n = count($input['i_dept_id']);

		for ($i=0; $i<$n; $i++)
		{
			$data[] = array(
					'dept_id'=>$input['i_dept_id'][$i],
					'mob_date'=>$input['i_date'][$i],
					'mob_code'=>$input['i_mob_code'][$i],
					'event_name'=>$input['i_event_name'][$i],
					'count_officer'=>$input['i_officer'][$i],
					'count_officer_troop'=>$input['i_officer_troop'][$i],
					'count_troop'=>$input['i_troop'][$i],
					'creator_id'=>$user->id
				);
		}

		return $this->service->insertPayroll($data);
	}

	public function deletePayroll()
	{
		if (!Sentry::getUser()->hasAccess('budget.mealpay.delete'))
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
		if (!$user->hasAccess('budget.mealpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$start = Input::get('q_date_start');
		$end = Input::get('q_date_end');
		$deptId = Input::get('q_dept_id');
		$mobCode = Input::get("q_mob_code");
		$result = $this->service->getPayrollQuery($start, $end, $deptId, $mobCode)->get();

		$objPHPExcel = PHPExcel_IOFactory::load(public_path().'/static/misc/export/mealpay.xlsx');

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

		// meta data
		$activeSheet->setCellValue('B2', $start)
					->setCellValue('C2', $end)
					->setCellValue('F2', $deptName);

		$rowNum = 6;
		foreach ($result as $row) {
			$col = 0;
			foreach ($row as $key=>$data) {
				if ($key == 'dept_name') {
					$data = trim(str_replace(':', ' ', $data));
				}

				$colString = PHPExcel_Cell::stringFromColumnIndex($col);
				$activeSheet->setCellValue($colString.$rowNum, $data);
				$col++;
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

	public function showSitStat()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mob.read'))
		{
			return App::abort(404, 'unauthorized action');
		}
		$mobCodes = Code::withCategory('B002');

		return View::make('budget.meal.sit_stat', get_defined_vars());
	}

	public function getSitStatData()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mealpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$startMonth = Input::get('q_month_start');
		$endMonth = Input::get('q_month_end');

		$deptId = Input::get('q_dept_id');

		$groupByRegion = Input::get('q_group_by_region');

		$query = $this->service->getSitStatQuery($startMonth, $endMonth, $deptId, $groupByRegion);

		$output = Datatables::of($query)->make(FALSE, FALSE);

		return Response::json($output);
	}

	public function exportSitStat()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mealpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$startMonth = Input::get('q_month_start');
		$endMonth = Input::get('q_month_end');

		$deptId = Input::get('q_dept_id');

		$groupByRegion = Input::get('q_group_by_region');

		$result = $this->service->getSitStatQuery($startMonth, $endMonth, $deptId, $groupByRegion)->get();

		$objPHPExcel = PHPExcel_IOFactory::load(public_path().'/static/misc/export/meal_sit_stat.xlsx');

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

		$activeSheet->setCellValue('B2', $startMonth)
					->setCellValue('C2', $endMonth)
					->setCellValue('F2', $deptName)
					->setCellValue('I2', $groupByRegion?'지방청별 합계':'관서별');

		$mobCodes = Code::withCategory('B002');
		$colIdx=2;
		foreach ($mobCodes as $code)
		{
			$colStr = PHPExcel_Cell::stringFromColumnIndex($colIdx);
			$activeSheet->setCellValue($colStr.'5', $code->title);
			$colIdx++;
		}

		$colStr = PHPExcel_Cell::stringFromColumnIndex($colIdx);

		$activeSheet->setCellValue($colStr.'4', '급식인원 (명)');
		$s = $colStr;

		$activeSheet->setCellValue($colStr.'5', '경찰관');
		$colIdx++;

		$colStr = PHPExcel_Cell::stringFromColumnIndex($colIdx);
		$activeSheet->setCellValue($colStr.'5', '경찰관 기동대');
		$colIdx++;

		$colStr = PHPExcel_Cell::stringFromColumnIndex($colIdx);
		$activeSheet->setCellValue($colStr.'5', '전의경부대');
		$colIdx++;

		$activeSheet->mergeCells($s.'4:'.$colStr.'4');

		$colStr = PHPExcel_Cell::stringFromColumnIndex($colIdx);
		$activeSheet->setCellValue($colStr.'4', '소요액 (천원)');
		$colIdx++;

		$rowNum = 6;
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
