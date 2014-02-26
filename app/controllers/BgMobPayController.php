<?php

class BgMobPayController extends BaseController {

	private $service;

	public function __construct() {
		$this->service = new BgMobPayService;
	}

	public function show()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mobpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$startMonth = Input::get('q_bm_start') ? Input::get('q_bm_start') : date('Y-m');
		$endMonth = Input::get('q_bm_end') ? Input::get('q_bm_end') : date('Y-m');
		$regionId = Input::get('q_region');
		$regionName = "전체";


		if ($user->hasAccess('budget.admin'))
		{
			$region = Department::regions()->toArray();
			if ($regionId) {
				foreach ($region as $r)
				{
					if ($r['id'] == $regionId)
					{
						$regionName = $r['dept_name'];
					}
				}
			}
		}
		else
		{
			$region = Department::region($user->dept_id);
			
			if ($regionId != $region['id']) {
				return App::abort(404, 'unauthorized action');
			}

			$regionName = $region['dept_name'];

		}

		$data = $this->service->getData($startMonth, $endMonth, $regionId);

		$intervals = $this->service->getIntervals();


        return View::make('budget.mobpay', get_defined_vars());
	}

	public function insert() {
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mobpay.create'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$input = Input::all();

		$res = DB::table('bg_mob_pays_master')->where('belong_month','=',Input::get('bm').'-01')->where('dept_id','=',Input::get('q_region'))->get();
		if (count($res) > 0) {
			return -1;
		}

		$this->service->insertMobPay($input);
		return 0;
	}

	public function edit() {
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mobpay.edit'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$this->service->editMobPay(Input::all());
		return 0;
	}

	public function export()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mobpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$startMonth = Input::get('q_bm_start') ? Input::get('q_bm_start') : date('Y-m');
		$endMonth = Input::get('q_bm_end') ? Input::get('q_bm_end') : date('Y-m');
		$regionId = Input::get('q_region');
		$intervals = $this->service->getIntervals();

		$end = $endMonth.'-01';
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->removeSheetByIndex(0);
		$regions = array();
		if ($regionId) {
			$regions[] = Department::region($regionId);
		} else {
			$regions = Department::regions();
		}

		$fill = array(
	        'fill' => array(
	            'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => 'ADD8E6')
	        ),
    		'font'=>array(
    				'bold'=>true
    			)
	    );
	    $fill2 = array(
    		'fill'=>array(
    				'type'=>PHPExcel_Style_Fill::FILL_SOLID,
    				'color'=>array('rgb'=>'AFEEEE')
    			),
    		'font'=>array(
    				'bold'=>true
    			)
	    	);
	    $border = array(
	    	'borders' => array(
		        'left' => array(
		          'style' => PHPExcel_Style_Border::BORDER_THIN,
		        ),
		        'right' => array(
		          'style' => PHPExcel_Style_Border::BORDER_THIN,
		        ),
		        'bottom' => array(
		          'style' => PHPExcel_Style_Border::BORDER_THIN,
		        ),
		        'top' => array(
		          'style' => PHPExcel_Style_Border::BORDER_THIN,
		        )
		      )
	    	);

		$sheetIdx = 0;
		foreach ($regions as $r) {
			$objPHPExcel->createSheet(null, $sheetIdx)
						->setTitle($r->dept_name);

			$activeSheet = $objPHPExcel->setActiveSheetIndex($sheetIdx);
			
			$activeSheet->getColumnDimension('A')->setWidth(15);
			$activeSheet->getColumnDimension('B')->setWidth(15);
			$activeSheet->getColumnDimension('C')->setWidth(15);
			$activeSheet->getColumnDimension('D')->setWidth(15);
			$activeSheet->getColumnDimension('E')->setWidth(15);
			$activeSheet->getColumnDimension('F')->setWidth(15);
			$activeSheet->getColumnDimension('G')->setWidth(15);

			$activeSheet->setCellValue("A1", "경비동원수당 집행내역")
						->setCellValue("A2", '조회기간')
						->setCellValue("B2", $startMonth)
						->setCellValue("C2", $endMonth)
						->setCellValue("E2", "지방청")
						->setCellValue("F2", $r->dept_name)
						->mergeCells("A1:F1");
			$activeSheet->getStyle("A1")
						->applyFromArray($fill);

			$activeSheet->getStyle('A2')->applyFromArray($fill);
			$activeSheet->getStyle('E2')->applyFromArray($fill);


			$rowNum = 4;
			$current = $startMonth.'-01';
			while(strtotime($current) <= strtotime($end)) {
				$monthStartRow = $rowNum;
				// print
				$currentMonth = substr_replace($current, '', -3, 3);
				$data = $this->service->getData($currentMonth, $currentMonth, $r->id);

				$activeSheet->setCellValue("A$rowNum", '귀속월')
							->setCellValue("B$rowNum", $currentMonth);

				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->getFont()
							->setBold(true)
							->setSize(14);
				$rowNum++;
				
				if (count($data['detail']) == 0) {
					$activeSheet->setCellValue("A$rowNum", "자료 없음");
					$monthEndRow = $rowNum;

					$activeSheet->getStyle("A$monthStartRow:G$monthEndRow")
								->applyFromArray($border);

					$rowNum+=2;
					// go to next month
					$current = date('Y-m-01', strtotime('next month', strtotime($current)));

					continue;
				}

				$activeSheet->setCellValue("A$rowNum", '상황별 동원인원(휴일, 비번, 휴무일 동원)');
				$activeSheet->mergeCells("A$rowNum:G$rowNum");
				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->applyFromArray($fill);

				$rowNum++;
				$activeSheet->setCellValue("A$rowNum", '구분')
							->setCellValue("B$rowNum", '합계')
							->setCellValue("C$rowNum", '집회시위관리')
							->setCellValue("D$rowNum", '경호행사')
							->setCellValue("E$rowNum", '혼잡경비')
							->setCellValue("F$rowNum", '수색재난구조')
							->setCellValue("G$rowNum", '훈련 등 기타');

				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->getFont()
							->setBold(true);

				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->applyFromArray($fill);

				$rowNum++;

				$activeSheet->setCellValue("A$rowNum", '인원 (명)')
							->setCellValue("B$rowNum", $data['master']->sit_sum)
							->setCellValue("C$rowNum", $data['master']->sit_demo)
							->setCellValue("D$rowNum", $data['master']->sit_escort)
							->setCellValue("E$rowNum", $data['master']->sit_crowd)
							->setCellValue("F$rowNum", $data['master']->sit_rescue)
							->setCellValue("G$rowNum", $data['master']->sit_etc);
				$rowNum+=2;

				$activeSheet->setCellValue("A$rowNum", '기능별 동원인원')
							->mergeCells("A$rowNum:G$rowNum");
				$activeSheet->getStyle("A$rowNum:G$rowNum")
				->applyFromArray($fill);
				$rowNum++;

				$activeSheet->setCellValue("A$rowNum", '경비동원수당 지급 대상(일반대상자)')
							->mergeCells("A$rowNum:G$rowNum");
				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->applyFromArray($fill);
				$rowNum++;

				$activeSheet->setCellValue("A$rowNum", '구분')
							->setCellValue("B$rowNum", '합계')
							->setCellValue("C$rowNum", '지방청')
							->setCellValue("D$rowNum", '경찰서')
							->setCellValue("E$rowNum", '지구대')
							->setCellValue("F$rowNum", '경찰관기동대')
							->setCellValue("G$rowNum", '전의경부대');

				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->getFont()
							->setBold(true);
				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->applyFromArray($fill);
				$rowNum++;

				$startRow = $rowNum;
				foreach ($data['detail'] as $d) {
					$activeSheet->setCellValue("A$rowNum", $intervals[$d->interval_id]->start.'~'.$intervals[$d->interval_id]->end.'시간')
								->setCellValue("B$rowNum", $d->sum)
								->setCellValue("C$rowNum", $d->region)
								->setCellValue("D$rowNum", $d->office)
								->setCellValue("E$rowNum", $d->local)
								->setCellValue("F$rowNum", $d->officer_troop)
								->setCellValue("G$rowNum", $d->troop);

					$rowNum++;
				}
				$endRow = $rowNum-1;

				$activeSheet->setCellValue("A$rowNum", "인원 (명)")
							->setCellValue("B$rowNum", "=SUM(B$startRow:B$endRow)")
							->setCellValue("C$rowNum", "=SUM(C$startRow:C$endRow)")
							->setCellValue("D$rowNum", "=SUM(D$startRow:D$endRow)")
							->setCellValue("E$rowNum", "=SUM(E$startRow:E$endRow)")
							->setCellValue("F$rowNum", "=SUM(F$startRow:F$endRow)")
							->setCellValue("G$rowNum", "=SUM(G$startRow:G$endRow)");
				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->applyFromArray($fill2);
				$rowNum++;

				$activeSheet->setCellValue("A$rowNum", "지급액 (천원)")
							->setCellValue("B$rowNum", $data['master']->ftn_sum)
							->setCellValue("C$rowNum", $data['master']->ftn_region)
							->setCellValue("D$rowNum", $data['master']->ftn_office)
							->setCellValue("E$rowNum", $data['master']->ftn_local)
							->setCellValue("F$rowNum", $data['master']->ftn_officer_troop)
							->setCellValue("G$rowNum", $data['master']->ftn_troop);
				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->applyFromArray($fill2);
				$rowNum+=2;

				$activeSheet->setCellValue("A$rowNum", '시간외수당 지급 대상(현업대상자)');
				$activeSheet->mergeCells("A$rowNum:G$rowNum");
				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->applyFromArray($fill);
				$rowNum++;

				$activeSheet->setCellValue("A$rowNum", '구분')
							->setCellValue("B$rowNum", '합계')
							->setCellValue("C$rowNum", '지방청')
							->setCellValue("D$rowNum", '경찰서')
							->setCellValue("E$rowNum", '지구대')
							->setCellValue("F$rowNum", '경찰관기동대')
							->setCellValue("G$rowNum", '전의경부대');
				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->getFont()
							->setBold(true);
				$activeSheet->getStyle("A$rowNum:G$rowNum")
							->applyFromArray($fill);
				$rowNum++;

				$activeSheet->setCellValue("A$rowNum", '인원 (명)')
							->setCellValue("B$rowNum", $data['master']->extra_sum)
							->setCellValue("C$rowNum", $data['master']->extra_region)
							->setCellValue("D$rowNum", $data['master']->extra_office)
							->setCellValue("E$rowNum", $data['master']->extra_local)
							->setCellValue("F$rowNum", $data['master']->extra_officer_troop)
							->setCellValue("G$rowNum", $data['master']->extra_troop);

				$monthEndRow = $rowNum;
				$activeSheet->getStyle("A$monthStartRow:G$monthEndRow")
							->applyFromArray($border);
				$rowNum+=2;

				// go to next month
				$current = date('Y-m-01', strtotime('next month', strtotime($current)));
			}

			$sheetIdx++;
		}
		$objPHPExcel->setActiveSheetIndex(0);
		$fileName = date('ymd')."_export";

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
}
