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

		if ($user->hasAccess('budget.admin'))
		{
			$region = Department::regions()->toArray();
		}
		else
		{
			$region = Department::region($user->dept_id);
		}

		$configs = PSConfig::category('budget.mealpay');

		$editableStart = $this->service->getEditableDateStart();
		return View::make('budget.mealpay', array(
				'region'=>$region,
				'user'=>$user,
				'configs' => $configs,
				'editableStart'=>$editableStart
		));
	}

	public function read()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mealpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$start = Input::get('q_date_start');
		$end = Input::get('q_date_end');
		$region = Input::get('q_region');
		$groupByMonth = Input::get('q_monthly_sum');
		$event = Input::get("q_event");
		$query = $this->service->buildQuery($start, $end, $region, $event, $groupByMonth, $user->dept_id);
		
		return Datatables::of($query)->make();
	}

	public function create()
	{
		if (!Sentry::getUser()->hasAccess('budget.mealpay.create'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$input = Input::all();
		$user = Sentry::getUser();
		$input['dept_id'] = $user->dept_id;
		$input['creator_id'] = $user->id;

		return $this->service->create($input);
	}

	public function delete()
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

	public function export() 
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mealpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$start = Input::get('q_date_start');
		$end = Input::get('q_date_end');
		$region = Input::get('q_region');
		$groupByMonth = Input::get('q_monthly_sum');
		$event = Input::get("q_event");

		$query = $this->service->buildQuery($start, $end, $region, $event, $groupByMonth, $user->dept_id);
		
		$result = $query->get();
		$objPHPExcel = PHPExcel_IOFactory::load(public_path().'/static/misc/export/mealpay.xlsx');

		$activeSheet = $objPHPExcel->setActiveSheetIndex(0);

		$viewType = $groupByMonth?'월별 합계':'원본';

		$regionName = $region?Department::region($region)->dept_name:'전체';

		$event = $event?$event:'없음';

		// meta data
		$activeSheet->setCellValue('B2', $start)
					->setCellValue('C2', $end)
					->setCellValue('F2', $viewType)
					->setCellValue('I2', $regionName)
					->setCellValue('L2', $event);

		$rowNum = 6;
		foreach ($result as $row) {
			$col = 0;
			foreach ($row as $key=>$data) {
				if ($key == 'id') {
					continue;
				} else if ($key == 'dept_name') {
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
}
