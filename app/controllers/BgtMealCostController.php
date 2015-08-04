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
	public function download() {
		//다운로드할 자료 가져오기
		$user = Sentry::getUser();
		$now = date("Y-m-d H:i:s");

		$rows = $this->service->getDataQuery($user, Input::all())->get();

		$objPHPExcel = new PHPExcel();
		$fileName = '동원급식비'.$now; 
		//obj 속성
		$objPHPExcel->getProperties()
			->setTitle($fileName)
			->setSubject($fileName);
		//셀 정렬(가운데)
		//
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		$sheet->setCellValue('a1','번호');
		$sheet->setCellValue('b1','집행일자');
		$sheet->setCellValue('c1','집행관서');
		$sheet->setCellValue('d1','상황구분');
		$sheet->setCellValue('e1','행사명');
		$sheet->setCellValue('f1','지급구분');
		$sheet->setCellValue('g1','식수인원');
		$sheet->setCellValue('h1','집행액(원)');
		//양식 부분 끝
		
		//이제 사용내역 나옴
		for ($i=1; $i <= sizeof($rows); $i++) { 
			$sheet->setCellValue('a'.($i+1),$rows[$i-1]->id);
			$sheet->setCellValue('b'.($i+1),$rows[$i-1]->use_date);
			$sheet->setCellValue('c'.($i+1),$rows[$i-1]->department->full_name);
			$sheet->setCellValue('d'.($i+1),$rows[$i-1]->situation->title);
			$sheet->setCellValue('e'.($i+1),$rows[$i-1]->event_name);
			$sheet->setCellValue('f'.($i+1),$rows[$i-1]->useType->title);
			$sheet->setCellValue('g'.($i+1),$rows[$i-1]->meal_count);
			$sheet->setCellValue('h'.($i+1),$rows[$i-1]->meal_amount);
		}

		//파일로 저장하기
		$writer = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header('Content-type: application/vnd.ms-excel');
		header('Content-Encoding: UTF-8');
		header('Content-Disposition: attachment; filename="'.$fileName.'.xlsx"');
		header("Content-Transfer-Encoding: binary ");
		$writer->save('php://output');
		return;
	}

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
        ->addColumn('revise_delete', function($model) {
        	return '<a href="'.url('budgets/meal-cost').'/'.$model->id.'/edit">'."수정".'</a>';
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
		$master = BgtMealCost::find($id);
		if ($master == null) {
			return App::abort(404);
		}

		$user = Sentry::getUser();

		$permissions = $this->service->getPermissions($user, $master);

		if(!$permissions['update']) {
			return App::abort(403);
		}

		return View::make('budget.meal-cost.edit', compact('user', 'master'));
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
		$input = Input::all();
		try {
			$this->service->update($id, $input);
		} catch (Exception $e) {
			Log::error($e->getMessage());
			return App::abort($e->getCode());
		}
		return array(
				'result'=>0,
				'message'=>'수정되었습니다.',
				'url'=>url('budgets/meal-cost')
		);
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
		$todelete = BgtMealCost::find($id);
		if($todelete->delete()){
			return array(
				'result'=>0,
				'message'=>'삭제되었습니다.',
				'url'=>url('budgets/meal-cost')
			);
		}
	}
}