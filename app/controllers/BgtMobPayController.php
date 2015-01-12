<?php

class BgtMobPayController extends BaseController {

	private $service;

	public function __construct() {
		$this->service = new BgtMobPayService;
	}

	/**
	 * Display a listing of the resource.
	 * GET /budgets/mob-pay
	 *
	 * @return Response
	 */
	public function download() {

		//다운로드할 자료 가져오기
		$user = Sentry::getUser();
		$now = date("Y-m-d H:i:s");

		$rows = $this->service->getMasterDataQuery($user, Input::all())->get();

		$objPHPExcel = new PHPExcel();
		$fileName = '경비동원수당'.$now; 
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
		$sheet->setCellValue('d1','동원상황구분');
		$sheet->setCellValue('e1','행사명');
		$sheet->setCellValue('f1','집행액(원)');
		//양식 부분 끝
		
		//이제 사용내역 나옴
		for ($i=1; $i <= sizeof($rows); $i++) { 
			$sheet->setCellValue('a'.($i+1),$rows[$i-1]->id);
			$sheet->setCellValue('b'.($i+1),$rows[$i-1]->use_date);
			$sheet->setCellValue('c'.($i+1),$rows[$i-1]->department->full_name);
			$sheet->setCellValue('d'.($i+1),$rows[$i-1]->situation->title);
			$sheet->setCellValue('e'.($i+1),$rows[$i-1]->event_name);
			$sheet->setCellValue('f'.($i+1),$rows[$i-1]->details()->sum('amount'));
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

		return View::make('budget.mob-pay.data-'.$type, $data);
	}

	public function read() {

		$type = Input::get('type');
		if (!$type) {
			return App::abort(400);
		}

		$user = Sentry::getUser();

		$query = $this->service->getMasterDataQuery($user, Input::all());

		switch ($type) {
			case 'raw':
				return Datatable::query($query)
		        ->showColumns('id', 'use_date')
		        ->addColumn('dept_name', function($model) {
		        	return $model->department->full_name;
		        })
		        ->addColumn('situation', function($model) {
	        		return $model->situation->title;
		        })
		        ->addColumn('event_name', function($model) {
		        	return '<a href="'.url('budgets/mob-pay').'/'.$model->id.'">'.str_limit($model->event_name, 35).'</a>';
		        })
		        ->addColumn('amount', function($model) {
		        	return number_format($model->details()->sum('amount'));
		        })
		        ->make();
			case 'stat-sit':

				break;
			default:
				return App::abort(400);
		}
	}


	/**
	 * GET /budgets/mob-pay/$id
	 *
	 * @return Response
	 */
	public function show($id) {

		$type = 'raw';
		$data = BgtMobPayMaster::find($id);

		if ($data === null) {
			return App::abort(404);
		}
		$user = Sentry::getUser();
		$permissions = $this->service->getPermissions($user, $data);

		if (!$permissions['read']) {
			return App::abort(403);
		}

		return View::make('budget.mob-pay.detail', compact('type', 'data', 'user'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /budgets/mob-pay/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('budget.mob-pay.insert');
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /budgets/mob-pay/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$master = BgtMobPayMaster::find($id);
		if ($master == null) {
			return App::abort(404);
		}

		$user = Sentry::getUser();
		
		$permissions = $this->service->getPermissions($user, $master);
		
		if (!$permissions['update']) {
			return App::abort(403);
		}

		return View::make('budget.mob-pay.edit', compact('user', 'master'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /budgets/mob-pay
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
			return App::abort($e->getCode());
		}
		return array(
				'result'=>0,
				'message'=>'입력되었습니다.',
				'url'=>url('budgets/mob-pay')
			);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /budgets/mob-pay/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		$user = Sentry::getUser();
		try {
			$this->service->update($user, $id, $input);
		} catch (Exception $e) {
			Log::error($e->getMessage());
			return App::abort($e->getCode());
		}
		return array(
				'result'=>0,
				'message'=>'수정되었습니다.',
				'url'=>url('budgets/mob-pay/'.$id)
			);
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /budgets/mob-pay/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$user = Sentry::getUser();

		try {
			$this->service->delete($user, $id);
		} catch (Exception $e) {
			return App::abort($e->getCode());
		}

		return array(
				'message'=>'삭제되었습니다',
				'result' => 0,
				'url' => action('BgtMobPayController@index')
			);
	}

}