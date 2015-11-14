<?php
use Carbon\Carbon;

class EqCapsaicinController extends EquipController {
	/**
	 * @param $nodeId
	 * @return mixed
     */
	public function showRegionConfirm($nodeId) {
		$user = Sentry::getUser();
		$node = EqSupplyManagerNode::find($nodeId);

		$data['node'] = $node;

		$requests = EqDeleteRequest::where('confirmed','=','0')->where('type','=','cap')->get();
		$rows = array();

		foreach ($requests as $r) {
			$usage = EqCapsaicinUsage::find($r->usage_id);
			$event = $usage->event;

			$row = new stdClass;
			$row->id = $r->id;
			$row->date = $event->date;
			$row->node = EqSupplyManagerNode::find($event->node_id);
			$row->user_node = EqSupplyManagerNode::find($usage->user_node_id);
			$row->type = $this->service->getEventType($event->type_code);
			$row->location = $usage->location;
			$row->event_name = $event->event_name;
			$row->amount = $usage->amount;
			$row->fileName = $usage->attached_file_name;
			array_push($rows, $row);
		}

		$currentPage = Input::get('page')== null ? 0 : Input::get('page') - 1;
		$pagedRows = array_slice($rows, $currentPage * 15, 15);

		$data['rows'] = Paginator::make($pagedRows, count($rows), 15);

		return View::make('equip.capsaicin.capsaicin-region-confirm', $data);
	}

	public function addEvent($nodeId) {
		$node = EqSupplyManagerNode::find($nodeId);
		$today = Carbon::today()->toDateString();
		$selectedDate = Input::get('date');
		if (!$selectedDate) {
			$selectedDate = $today;
		}
		$events = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','=',$selectedDate)->get();
		return View::make('equip.capsaicin.event-list',get_defined_vars());
	}

	public function storeNewEvent($nodeId) {
		$input = Input::all();
		$event = new EqCapsaicinEvent;
		$event->type_code = "assembly";
		$event->event_name = $input['event_name'];
		$event->node_id = $nodeId;
		$event->date = $input['date'];
		if (!$event->save()) {
			return App::abort(500);
		}
		return "저장되었습니다";

	}

	public function deleteEvent($eventId) {
		$event = EqCapsaicinEvent::find($eventId);
		if (!$event->delete()) {
			return App::abort(500);
		}
		return "삭제되었습니다.";
	}

	public function getEvents(){
		$now = Carbon::now();
		$regionId = Input::get('regionId');
		$events = EqCapsaicinEvent::where('node_id','=',$regionId)->where('type_code','=','assembly')->where('date','>',$now->subDays(4000))->orderBy('date', 'desc')->get();
		// 최신순으로 n일 전 집회까지 리턴한다.
		return $events;
	}

	public function addCrossEvent() {
		$user = Sentry::getUser();
		$node = $user->supplyNode;
		$today = Carbon::today()->toDateString();
		$selectedDate = Input::get('date');
		if (!$selectedDate) {
			$selectedDate = $today;
		}
		$events = EqCapsaicinEvent::where('type_code','=','cross')->where('date','=',$selectedDate)->get();
		return View::make('equip.capsaicin.cross-event-list',get_defined_vars());
	}

	public function storeNewCrossEvent() {
		$user = Sentry::getUser();
		$node = $user->supplyNode;

		$input = Input::all();
		$event = new EqCapsaicinEvent;
		$event->type_code = "cross";
		$event->event_name = $input['event_name'];
		$event->node_id = $node->id;
		$event->date = $input['date'];
		if (!$event->save()) {
			return App::abort(500);
		}
		return "저장되었습니다";
	}

	public function deleteCrossEvent($eventId) {
		$event = EqCapsaicinEvent::find($eventId);
		if (!$event->delete()) {
			return App::abort(500);
		}
		return "삭제되었습니다.";
	}

	public function getCrossEvents(){
		$now = Carbon::now();
		$regionId = Input::get('regionId');
		$events = EqCapsaicinEvent::where('type_code','=','cross')->where('date','>',$now->subDays(4000))->orderBy('date', 'desc')->get();
		// 최신순으로 n일 전 집회까지 리턴한다.
		return $events;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Sentry::getUser();
		$node = $user->supplyNode;


		if ($node->type_code != "D001") {
			if ($node->type_code == "D002") {
				return Redirect::action('EqCapsaicinController@nodeHolding', $node->id);
			} else {
				return Redirect::to("equips/capsaicin/node/".$node->id."/events");
			}
		}

		$agencies = EqSupplyManagerNode::where('type_code','=','D002')->get();
		$data['nodes'] = $agencies;
		$tabId = Input::get('tab_id');
		$data['tabId'] = $tabId;
		$start = Input::get('start');
		$end = Input::get('end');
		$eventName = Input::get('event_name');
		$data['eventName'] = $eventName;
		$eventType = Input::get('event_type');
		$data['eventType'] = $eventType;
		$regionId = Input::get('region');
		$data['region'] = $regionId;
		$year = Carbon::now()->year;
		$data['year'] = $year;
		$now = Carbon::now();

		switch ($tabId) {
			case '1':
				//지방쳥별 보기
				$stock = array();
				$usageSum = array();
				$usageT = array();
				$usageA = array();
				$timesSum = array();
				$timesT = array();
				$timesA = array();
				$stockSum = 0;


				//선택한 연도에 해당하는 자료만 가져와 보여준다.
				foreach ($agencies as $n) {
					// 훈련 내역 추가시 훈련 내역을 등록한 node의 id가 입력되므로 지방청의 child id에 대해서 사용량, 사용횟수 합을 구해줘야
					$children = EqSupplyManagerNode::where('full_path','like',$n->full_path.'%')->where('is_selectable','=',1)->get();

					// 사용량 충합
					$usageT[$n->id] = 0;
					foreach($children as $child){
						$usageT[$n->id] += EqCapsaicinUsage::whereHas('event', function($q) use($child,$year) {
							$q->where('node_id','=',$child->id)->where('date','like',$year.'%')->where('type_code','=','drill');
						})->sum('amount');
					}
					$usageA[$n->id] = EqCapsaicinUsage::whereHas('event', function($q) use($n,$year) {
						$q->where('node_id','=',$n->id)->where('date','like',$year.'%')->where('type_code','=','assembly');
					})->sum('amount');

					$usageSum[$n->id] = $usageT[$n->id] + $usageA[$n->id];

					// 훈련시 사용횟수 총합
					$timesT[$n->id] = 0;
					foreach($children as $child){
						$timesT[$n->id] += EqCapsaicinEvent::where('node_id','=',$child->id)->where('date','>',$year)->where('type_code','=','drill')->count();
					}
					$timesA[$n->id] = EqCapsaicinEvent::where('node_id','=',$n->id)->where('date','>',$year)->where('type_code','=','assembly')->count();
					$timesSum[$n->id] = $timesA[$n->id] + $timesT[$n->id];

					/**
					 * 당해 최초보유량 - 당해 사용량 + 당해 보급량 -당해 불용량 = 현재보유량임.
					 */
					$initHolding = EqCapsaicinFirstday::where('node_id','=',$n->id)->where('year','=',$year)->first()->amount;
					$acquiredThisYear = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$n->id)->where('acquired_date','like',$year.'%')->sum('amount');
					$discardedThisYear = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$n->id)->where('acquired_date','like',$year.'%')->sum('amount');
					$crossUsedThisYear = EqCapsaicinCrossRegion::where('node_id','=',$n->id)->where('used_date','like',$year.'%')->sum('amount');
					// 보유량 = 초기 보유량 - 사용량 - 추가량 - 불용량 - 타청지원
					$stock[$n->id] = $initHolding - $usageSum[$n->id] + $acquiredThisYear - $discardedThisYear - $crossUsedThisYear;
					$stockSum += $stock[$n->id];
		 		}
		 		$data['stock'] = $stock;
		 		$data['usageSum'] = $usageSum;
		 		$data['usageT'] = $usageT;
		 		$data['usageA'] = $usageA;
		 		$data['timesSum'] = $timesSum;
		 		$data['timesT'] = $timesT;
		 		$data['timesA'] = $timesA;

		 		$data['stockSum'] = $stockSum;
		 		$data['usageSumSum'] = array_sum($usageSum);
		 		$data['usageTSum'] = array_sum($usageT);
		 		$data['usageASum'] = array_sum($usageA);
		 		$data['timesSumSum'] = array_sum($timesSum);
		 		$data['timesTSum'] = array_sum($timesT);
		 		$data['timesASum'] = array_sum($timesA);

		 		if (Input::get('export')) {
					//xls obj 생성
					$objPHPExcel = new PHPExcel();
					$fileName = $year.'년 지방청별 캡사이신 희석액 현황';
					//obj 속성
					$objPHPExcel->getProperties()
						->setCreator($user->user_name)
						->setTitle($fileName)
						->setSubject($fileName);
					//셀 정렬(가운데)
					$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sheet = $objPHPExcel->setActiveSheetIndex(0);
					$sheet->mergeCells('a1:a2');
					$sheet->mergeCells('b1:b2');
					$sheet->mergeCells('c1:e1');
					$sheet->mergeCells('f1:h1');

					$sheet->setCellValue('a1','구분');
					$sheet->setCellValue('b1','현재보유량(ℓ)');
					$sheet->setCellValue('c1','사용량(ℓ)');
					$sheet->setCellValue('f1','사용횟수');
					$sheet->setCellValue('c2','계');
					$sheet->setCellValue('d2','훈련시');
					$sheet->setCellValue('e2','집회시위시');
					$sheet->setCellValue('f2','계');
					$sheet->setCellValue('g2','훈련시');
					$sheet->setCellValue('h2','집회시위시');

					$sheet->setCellValue('a3','계');
					$sheet->setCellValue('b3',round($data['stockSum'],2));
					$sheet->setCellValue('c3',round($data['usageSumSum'],2));
					$sheet->setCellValue('d3',round($data['usageTSum'],2));
					$sheet->setCellValue('e3',round($data['usageASum'],2));
					$sheet->setCellValue('f3',$data['timesSumSum']);
					$sheet->setCellValue('g3',$data['timesTSum'] );
					$sheet->setCellValue('h3',$data['timesASum']);


					//양식 부분 끝
					//지방청별 자료
					for ($i=0; $i < sizeof($data['nodes']); $i++) {
						$sheet->setCellValue('a'.($i+4), $data['nodes'][$i]->node_name);
						$sheet->setCellValue('b'.($i+4), round($stock[$data['nodes'][$i]->id],2) );
						$sheet->setCellValue('c'.($i+4), round($usageSum[$data['nodes'][$i]->id],2) );
						$sheet->setCellValue('d'.($i+4), round($usageT[$data['nodes'][$i]->id], 2) );
						$sheet->setCellValue('e'.($i+4), round($usageA[$data['nodes'][$i]->id], 2) );
						$sheet->setCellValue('f'.($i+4), $timesSum[$data['nodes'][$i]->id] );
						$sheet->setCellValue('g'.($i+4), $timesT[$data['nodes'][$i]->id] );
						$sheet->setCellValue('h'.($i+4), $timesA[$data['nodes'][$i]->id] );
					}


					//파일로 저장하기
					$writer = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
					header("Pragma: public");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Content-Type: application/force-download");
					header('Content-type: application/vnd.ms-excel');
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Encoding: UTF-8');
					header('Content-Disposition: attachment; filename="'.$fileName.' '.$now.'.xlsx"');
					header("Content-Transfer-Encoding: binary ");
					$writer->save('php://output');
					return;
				}
				break;
			case '2':
				//월별 보기
				//조회 조건에 관련된 변수들 정의
				$year = Input::get('year');
				$selectedRegionId = Input::get('region');

				$now = Carbon::now();
				if ($year == null) {
					$year = $now->year;
				}
				$data['year'] = $year;
				$data['initYears'] = EqCapsaicinFirstday::select('year')->distinct()->get();
				$regions = EqSupplyManagerNode::where('type_code','=','D002')->get();
				$data['regions'] = $regions;

				if ($selectedRegionId == null) {
					$selectedRegionId = 1;
				}
				$data['selectedRegionId'] = $selectedRegionId;

				if ($selectedRegionId == 1) {
					//지방청 필터 안 건 경우 전체자료 다 가져옴
					$firstDayHolding = EqCapsaicinFirstday::where('year','=',$year)->sum('amount');

					$acquiredThisYear = EqCapsaicinIo::where('io','=',1)->where('acquired_date','>',$year)->where('caption','<>','CR')->sum('amount');
					$discardsThisYear = EqCapsaicinIo::where('io','=',0)->where('acquired_date','>',$year)->sum('amount');

					$data['firstDayHolding'] = $firstDayHolding;

					$data['usageTSum'] = EqCapsaicinUsage::whereHas('event', function($q) use($year){
						$q->where('type_code','=','drill')->where('date','like',$year.'%');
					})->sum('amount');
					$data['usageASum'] = EqCapsaicinUsage::whereHas('event', function($q) use($year){
						$q->where('type_code','=','assembly')->where('date','like',$year.'%');
					})->sum('amount');
					$data['usageSumSum'] = $data['usageTSum'] + $data['usageASum'];

					$data['timesTSum'] = EqCapsaicinEvent::where('date','like',$year.'%')->where('type_code','=','drill')->count();
					$data['timesASum'] = EqCapsaicinEvent::where('date','like',$year.'%')->where('type_code','=','assembly')->count();
					$data['timesSumSum'] = $data['timesTSum'] + $data['timesASum'];

					$data['crossUsageSum'] = 0;
					$data['additionSum'] = $acquiredThisYear;
					$data['discardSum'] = $discardsThisYear;

					$stock = array();
					$usageSum = array();
					$usageT = array();
					$usageA = array();
					$timesSum = array();
					$timesT = array();
					$timesA = array();
					$crossUsage = array();
					$addition = array();
					$discard = array();

					$now = Carbon::now();
					$consumedUntilThisMonth = 0;
					//올해면 아직 안 온 달은 비워둔다.
					$data['presentStock'] = null;
					for ($i=1; $i <= 12; $i++) {
						$firstDayofMonth = Carbon::createFromDate($year, $i, 1, 'Asia/Seoul')->subDay();
						if ($i != 12) {
							$lastDayofMonth = Carbon::createFromDate($year, $i+1, 1, 'Asia/Seoul')->subDay();
						} else {
							$lastDayofMonth = Carbon::createFromDate($year, $i, 31, 'Asia/Seoul');
						}

						$usageT[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($firstDayofMonth, $lastDayofMonth){
											$q->where('type_code','=','drill')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
										})->sum('amount');
						$usageA[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($firstDayofMonth, $lastDayofMonth){
											$q->where('type_code','=','assembly')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
										})->sum('amount');
						$usageSum[$i] = $usageT[$i] + $usageA[$i];
						$consumedUntilThisMonth += $usageSum[$i];

						$timesT[$i] = EqCapsaicinEvent::where('type_code','=','drill')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
						$timesA[$i] = EqCapsaicinEvent::where('type_code','=','assembly')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
						$timesSum[$i] = $timesT[$i] + $timesA[$i];

						$crossUsage[$i] = EqCapsaicinCrossRegion::where('used_date','>',$firstDayofMonth)->where('used_date','<=',$lastDayofMonth)->sum('amount');
						$addition[$i] = EqCapsaicinIo::where('io','=',1)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');
						$discard[$i]  = EqCapsaicinIo::where('io','=',0)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');

						$acquiredUntilThisMonth = EqCapsaicinIo::where('io','=',1)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
						$discardedUntilThisMonth = EqCapsaicinIo::where('io','=',0)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
						$crossUsedUntilThisMonth = EqCapsaicinCrossRegion::where('used_date','<=',$lastDayofMonth)->where('used_date','like',$year.'%')->sum('amount');

						$stock[$i] = $firstDayHolding - $consumedUntilThisMonth + $acquiredUntilThisMonth - $discardedUntilThisMonth - $crossUsedUntilThisMonth;

						$month = 12;
						if ($year == $now->year) {
							$month = $now->month;
							if ($month == $i) {
								$data['presentStock'] = $stock[$i];
							}
						} elseif ($year > $now->year) {
							$stock[$i] = null;
							$usageSum[$i] = null;
							$usageT[$i] = null;
							$usageA[$i] = null;
							$timesSum[$i] = null;
							$timesT[$i] = null;
							$timesA[$i] = null;
							$crossUsage[$i] = null;
							$addition[$i] = null;
							$discard[$i] = null;
							continue;
						}

						if ($month < $i) {
							$stock[$i] = null;
							$usageSum[$i] = null;
							$usageT[$i] = null;
							$usageA[$i] = null;
							$timesSum[$i] = null;
							$timesT[$i] = null;
							$timesA[$i] = null;
							$crossUsage[$i] = null;
							$addition[$i] = null;
							$discard[$i] = null;
							continue;
						}
					}
					$data['stock'] = $stock;
					$data['usageSum'] = $usageSum;
					$data['usageT'] = $usageT;
					$data['usageA'] = $usageA;
					$data['timesSum'] = $timesSum;
					$data['timesT'] = $timesT;
					$data['timesA'] = $timesA;
					$data['crossUsage'] = $crossUsage;
					$data['addition'] = $addition;
					$data['discard'] = $discard;

				} else {
					//지방청 필터 건 경우
					$nodeId = $selectedRegionId;
					$node = EqSupplyManagerNode::find($nodeId);
					// 훈련 내역 추가시 훈련 내역을 등록한 node의 id가 입력되므로 지방청의 child id에 대해서 사용량, 사용횟수 합을 구해줘야
					$children = EqSupplyManagerNode::where('full_path','like',$node->full_path.'%')->where('is_selectable','=',1)->get();

					$firstDayHolding = EqCapsaicinFirstday::where('year','=',$year)->where('node_id','=',$nodeId)->first()->amount;

					$acquiredThisYear = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','>',$year)->sum('amount');
					$discardedThisYear = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','>',$year)->sum('amount');
					$crossUsageThisYear = EqCapsaicinCrossRegion::where('node_id','=',$nodeId)->where('used_date','>',$year)->sum('amount');

					$data['firstDayHolding'] = $firstDayHolding;
					$data['usageTSum'] = 0;
					foreach($children as $child){
						$data['usageTSum'] += EqCapsaicinUsage::whereHas('event', function($q) use($child, $year){
							$q->where('type_code','=','drill')->where('node_id','=',$child->id)->where('date','like',$year.'%');
						})->sum('amount');
					}
					$data['usageASum'] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$year){
						$q->where('type_code','=','assembly')->where('node_id','=',$nodeId)->where('date','like',$year.'%');
					})->sum('amount');
					$data['usageSumSum'] = $data['usageTSum'] + $data['usageASum'];

					$data['timesTSum'] = 0;
					foreach($children as $child){
						$data['timesTSum'] += EqCapsaicinEvent::where('node_id','=',$child->id)->where('date','like',$year.'%')->where('type_code','=','drill')->count();
					}
					$data['timesASum'] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('type_code','=','assembly')->count();
					$data['timesSumSum'] = $data['timesTSum'] + $data['timesASum'];

					$data['crossUsageSum'] = $crossUsageThisYear;
					$data['additionSum'] = $acquiredThisYear;
					$data['discardSum'] = $discardedThisYear;

					$stock = array();
					$usageSum = array();
					$usageT = array();
					$usageA = array();
					$timesSum = array();
					$timesT = array();
					$timesA = array();
					$crossUsage = array();
					$addition = array();
					$discard = array();

					$now = Carbon::now();
					//올해면 아직 안 온 달은 비워둔다.
					$data['presentStock'] = null;
					$consumedUntilThisMonth = 0;
					for ($i=1; $i <= 12; $i++) {

						$firstDayofMonth = Carbon::createFromDate($year, $i, 1, 'Asia/Seoul')->subDay();
						if ($i != 12) {
							$lastDayofMonth = Carbon::createFromDate($year, $i+1, 1, 'Asia/Seoul')->subDay();
						} else {
							$lastDayofMonth = Carbon::createFromDate($year, $i, 31, 'Asia/Seoul');
						}

						// 사용량
						$usageT[$i] = 0;
						foreach($children as $child){
							$usageT[$i] += EqCapsaicinUsage::whereHas('event', function($q) use($child,$firstDayofMonth, $lastDayofMonth){
											$q->where('type_code','=','drill')->where('node_id','=',$child->id)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
										})->sum('amount');
						}
						$usageA[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$firstDayofMonth, $lastDayofMonth){
											$q->where('type_code','=','assembly')->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
										})->sum('amount');
						$usageSum[$i] = $usageT[$i] + $usageA[$i];
						$consumedUntilThisMonth += $usageSum[$i];
						// 사용횟수
						$timesT[$i] = 0;
						foreach($children as $child){
							$timesT[$i] += EqCapsaicinEvent::where('node_id','=',$child->id)->where('type_code','=','drill')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
						}
						$timesA[$i] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('type_code','=','assembly')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
						$timesSum[$i] = $timesT[$i] + $timesA[$i];

						$crossUsage[$i] = EqCapsaicinCrossRegion::where('node_id','=',$nodeId)->where('used_date','>',$firstDayofMonth)->where('used_date','<=',$lastDayofMonth)->sum('amount');
						$addition[$i] = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');
						$discard[$i]  = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');

						$acquireUntilThisMonth = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
						$discardUntilThisMonth = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
						$crossUsedUntilThisMonth = EqCapsaicinCrossRegion::where('node_id','=',$nodeId)->where('used_date','<=',$lastDayofMonth)->where('used_date','like',$year.'%')->sum('amount');
						$stock[$i] = $firstDayHolding - $consumedUntilThisMonth + $acquireUntilThisMonth - $discardUntilThisMonth - $crossUsedUntilThisMonth;

						$month = 12;
						if ($year == $now->year) {
							$month = $now->month;
							if ($month == $i) {
								$data['presentStock'] = $stock[$i];
							}
						} elseif ($year > $now->year) {
							$stock[$i] = null;
							$usageSum[$i] = null;
							$usageT[$i] = null;
							$usageA[$i] = null;
							$timesSum[$i] = null;
							$timesT[$i] = null;
							$timesA[$i] = null;
							$crossUsage[$i] = null;
							$addition[$i] = null;
							$discard[$i] = null;
							continue;
						}

						if ($month < $i) {
							$stock[$i] = null;
							$usageSum[$i] = null;
							$usageT[$i] = null;
							$usageA[$i] = null;
							$timesSum[$i] = null;
							$timesT[$i] = null;
							$timesA[$i] = null;
							$crossUsage[$i] = null;
							$addition[$i] = null;
							$discard[$i] = null;
							continue;
						}
					}

					$data['stock'] = $stock;
					$data['usageSum'] = $usageSum;
					$data['usageT'] = $usageT;
					$data['usageA'] = $usageA;
					$data['timesSum'] = $timesSum;
					$data['timesT'] = $timesT;
					$data['timesA'] = $timesA;
					$data['crossUsage'] = $crossUsage;
					$data['addition'] = $addition;
					$data['discard'] = $discard;
				}

				//엑셀로 파일 다운로드
				if (Input::get('export')) {
					$this->service->exportCapsaicinByMonth($data, EqSupplyManagerNode::find($selectedRegionId), $now, $year);
					return;
				}
				break;
			case '3':
				//행사별 보기 탭인 경우
				$validator = Validator::make(Input::all(), array(
					'start'=>'date',
					'end'=>'date'
				));

				if ($validator->fails()) {
					return App::abort(400);
				}

				if (!$start) {
					$start = date('Y-m-d', strtotime('first day of January this year'));
				}

				if (!$end) {
					$end = date('Y-m-d', strtotime('last day of December this year'));
				}

				$data['start'] = $start;
				$data['end'] = $end;
				//날짜 필터 걸었다
				$query = EqCapsaicinEvent::where('date', '>=', $start)->where('date', '<=', $end);
				//행사명 필터 걸었다
				if ($eventName) {
					$query->where('event_name','like',"%$eventName%");
				}
				//행사구분 필터 검
				if ($eventType) {
					$query->where('type_code','=',$eventType);
				}
				//지방청 필터 검
				$data['regions'] = EqSupplyManagerNode::where('type_code','=','D002')->get();
				if ($regionId) {
					$query->where('node_id','=',$regionId);
				}

				$events = $query->orderBy('date','DESC')->get();
				$totalUsage = 0;
				$rows = array();
				foreach ($events as $e) {
					$usages = $e->children;
					foreach ($usages as $u) {
						$row = new stdClass;
						$row->id = $u->id;
						$row->date = $e->date;
						$row->node = EqSupplyManagerNode::find($e->node_id);
						$row->user_node = EqSupplyManagerNode::find($u->user_node_id);
						$row->type = $this->service->getEventType($e->type_code);
						$row->location = $u->location;
						$row->event_name = $e->event_name;
						$row->fileName = $e->attached_file_name;
						$row->amount = $u->amount;
						$row->crossHeadNode = $u->cross? EqSupplyManagerNode::find($u->cross->io->node_id) : '';
						$totalUsage += $u->amount;
						array_push($rows, $row);
					}
				}

				$currentPage = Input::get('page')== null ? 0 : Input::get('page') - 1;
				$pagedRows = array_slice($rows, $currentPage * 15, 15);

				$data['rows'] = Paginator::make($pagedRows, count($rows), 15);

				$data['totalUsage'] = $totalUsage;

				if (Input::get('export')) {
					$this->service->exportCapsaicinByEvent($rows, EqSupplyManagerNode::find($regionId), $now);
					return;
				}

				break;
			default:
				//지방쳥별 보기
				$stock = array();
				$usageSum = array();
				$usageT = array();
				$usageA = array();
				$timesSum = array();
				$timesT = array();
				$timesA = array();
				$stockSum = 0;


				//선택한 연도에 해당하는 자료만 가져와 보여준다.
				foreach ($agencies as $n) {
					// 훈련 내역 추가시 훈련 내역을 등록한 node의 id가 입력되므로 지방청의 child id에 대해서 사용량, 사용횟수 합을 구해줘야
					$children = EqSupplyManagerNode::where('full_path','like',$n->full_path.'%')->where('is_selectable','=',1)->get();

					// 사용량 충합
					$usageT[$n->id] = 0;
					foreach($children as $child){
						$usageT[$n->id] += EqCapsaicinUsage::whereHas('event', function($q) use($child,$year) {
							$q->where('node_id','=',$child->id)->where('date','like',$year.'%')->where('type_code','=','drill');
						})->sum('amount');
					}
					$usageA[$n->id] = EqCapsaicinUsage::whereHas('event', function($q) use($n,$year) {
						$q->where('node_id','=',$n->id)->where('date','like',$year.'%')->where('type_code','=','assembly');
					})->sum('amount');

					$usageSum[$n->id] = $usageT[$n->id] + $usageA[$n->id];

					// 훈련시 사용횟수 총합
					$timesT[$n->id] = 0;
					foreach($children as $child){
						$timesT[$n->id] += EqCapsaicinEvent::where('node_id','=',$child->id)->where('date','>',$year)->where('type_code','=','drill')->count();
					}
					$timesA[$n->id] = EqCapsaicinEvent::where('node_id','=',$n->id)->where('date','>',$year)->where('type_code','=','assembly')->count();
					$timesSum[$n->id] = $timesA[$n->id] + $timesT[$n->id];

					/**
					 * 당해 최초보유량 - 당해 사용량 + 당해 보급량 -당해 불용량 = 현재보유량임.
					 */
					$initHolding = EqCapsaicinFirstday::where('node_id','=',$n->id)->where('year','=',$year)->first()->amount;
					$acquiredThisYear = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$n->id)->where('acquired_date','like',$year.'%')->sum('amount');
					$discardedThisYear = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$n->id)->where('acquired_date','like',$year.'%')->sum('amount');
					$crossUsedThisYear = EqCapsaicinCrossRegion::where('node_id','=',$n->id)->where('used_date','like',$year.'%')->sum('amount');
					// 보유량 = 초기 보유량 - 사용량 - 추가량 - 불용량 - 타청지원
					$stock[$n->id] = $initHolding - $usageSum[$n->id] + $acquiredThisYear - $discardedThisYear - $crossUsedThisYear;
					$stockSum += $stock[$n->id];
				}
				$data['stock'] = $stock;
				$data['usageSum'] = $usageSum;
				$data['usageT'] = $usageT;
				$data['usageA'] = $usageA;
				$data['timesSum'] = $timesSum;
				$data['timesT'] = $timesT;
				$data['timesA'] = $timesA;

				$data['stockSum'] = $stockSum;
				$data['usageSumSum'] = array_sum($usageSum);
				$data['usageTSum'] = array_sum($usageT);
				$data['usageASum'] = array_sum($usageA);
				$data['timesSumSum'] = array_sum($timesSum);
				$data['timesTSum'] = array_sum($timesT);
				$data['timesASum'] = array_sum($timesA);
		}

		return View::make('equip.capsaicin_head.capsaicin-index', $data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{

		switch (Input::get('type')) {
			case 'cross':

				$data['node'] = EqSupplyManagerNode::find(Input::get('nodeId'));
				$data['mode'] = 'create';
				// 해당 지방청을 제외한 나머지 지방청만 보여줌
				$data['regions'] = EqSupplyManagerNode::where('type_code','=','D002')->where('id','!=',$data['node']->id)->get();
				$data['region'] = Input::get('region_id');
				return View::make('equip.capsaicin.capsaicin-cross-form',$data);

				break;

			case 'event':
				$data['node'] = EqSupplyManagerNode::find(Input::get('nodeId'));
				$data['mode'] = 'create';
				$data['regions'] = EqSupplyManagerNode::where('type_code','=','D002')->get();
				$data['region'] = Input::get('region_id');

				return View::make('equip.capsaicin.capsaicin-usage-form',$data);

				break;

			case 'drill':

				$data['node'] = EqSupplyManagerNode::find(Input::get('nodeId'));
				$data['region'] = Input::get('region_id');

				return View::make('equip.capsaicin.capsaicin-drill-form', $data);

				break;

			default:
				# code...
				break;
		}
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */

	// dummy 훈련내역 임의로 추가
	public function drillstore($nodeId, $count, $month) {
		//eq_capsaicin_event table에 event 추가
		DB::beginTransaction();
		for($i = 2; $i <= $count; $i++){
			$event = new EqCapsaicinEvent;
			$event->type_code = "drill";
			$event->event_name = "훈련".$i."(본청입력)";
			$event->node_id = $nodeId;
			$event->date = "2015-0".$month."-01";

			if (!$event->save()) {
				return App::abort(500);
			}
			//eq_capsaicin_usage table에 drill usage 추가
			$usage = new EqCapsaicinUsage;
			$usage->event_id = $event->id;
			$usage->amount = 0;
			$usage->user_node_id = $nodeId;
			$usage->location = "장소".$i."(본청입력)";

			if (!$usage->save()) {
				return App::abort(500);
			}
		}
		DB::commit();
	}

	public function store()
	{
		$input = Input::all();
		$node = EqSupplyManagerNode::find(Input::get('nodeId'));

		// return var_dump($input);

		DB::beginTransaction();


		switch(Input::get('type')) {
			case 'cross':
				$event = EqCapsaicinEvent::find($input['event']);
				$usage = new EqCapsaicinUsage;

				$usage->event_id = $event->id;
				$usage->amount = $input['amount'];
				$usage->user_node_id = $node->id;
				$usage->attached_file_name = $input['file_name'];
				if (!$usage->save()) {
					return App::abort(500);
				}

				$addition = new EqCapsaicinIo;
				$addition->node_id = $input['region'];
				$addition->amount = $input['amount'];
				$addition->acquired_date = $event->date;
				$addition->caption = 'cross';
				$addition->io = 1;

				if (!$addition->save()) {
					return App::abort(500);
				}

				$crossUsage = new EqCapsaicinCrossRegion;
				$crossUsage->node_id = $node->id;
				$crossUsage->usage_id = $usage->id;
				$crossUsage->io_id = $addition->id;
				$crossUsage->amount = $input['amount'];
				$crossUsage->used_date = $event->date;

				if (!$crossUsage->save()) {
					return App::abort(500);
				}
			break;

			case 'event':
				$event = EqCapsaicinEvent::find($input['event']);
				$usage = new EqCapsaicinUsage;

				$usage->event_id = $event->id;
				$usage->amount = $input['amount'];
				$usage->location = $input['location'];
				$usage->user_node_id = $node->id;
				$usage->attached_file_name = $input['file_name'];

				if (!$usage->save()) {
					return App::abort(500);
				}

				// 타 청에서 동원된 경우
				// 1. 해당 청에 추가량 등록
				if ($node->region()->id != $input['region']) {
					// 원래는 $node->region()->id 였으나 그렇게 되면 본청이 입력을 못하게됨.
					$addition = new EqCapsaicinIo;
					$addition->node_id = $input['region'];
					$addition->amount = $input['amount'];
					$addition->acquired_date = $event->date;
					$addition->caption = 'addition';
					$addition->io = 1;

					if (!$addition->save()) {
						return App::abort(500);
					}
					// 2. 동원한 지방청에서 타청사용량 추가
					$crossUsage = new EqCapsaicinCrossRegion;
					$crossUsage->node_id = $node->id;
					$crossUsage->usage_id = $usage->id;
					$crossUsage->io_id = $addition->id;
					$crossUsage->amount = $input['amount'];
					$crossUsage->used_date = $event->date;

					if (!$crossUsage->save()) {
						return App::abort(500);
					}
				}
				break;

			case 'drill':
				//eq_capsaicin_event table에 event 추가
				$event = new EqCapsaicinEvent;
				$event->type_code = "drill";
				$event->event_name = $input['event'];
				// 본청이 일반 중대 것 입력하더라도 제대로 잘 들어가게
				$event->node_id = EqSupplyManagerNode::find(Input::get('nodeId'))->region()->id;
				$event->date = $input['date'];

				if (!$event->save()) {
					return App::abort(500);
				}
//				eq_capsaicin_usage table에 drill usage 추가
				$usage = new EqCapsaicinUsage;
				$usage->event_id = $event->id;
				$usage->amount = $input['amount'];
				$usage->user_node_id = Input::get('nodeId');
				$usage->location = $input['location'];


				if (!$usage->save()) {
					return App::abort(500);
				}
				break;

			default:
				return Redirect::action('EqCapsaicinController@nodeEvents', Input::get('nodeId'))->with('message', '예상치 못한 오류가 발생했습니다. 관리자에게 문의하세요.');
				break;
		}

		DB::commit();
		return Redirect::action('EqCapsaicinController@nodeEvents', Input::get('nodeId'))->with('message', '저장되었습니다.');

	}


	/**
	 * Display the specified resource.
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
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function nodeEventList($nodeId) {

	}

	public function nodeEvents($nodeId) {
		$user = Sentry::getUser();
		$userNode = $user->supplyNode;
		$data['userNode'] = $userNode;

		$node = EqSupplyManagerNode::find($nodeId);
		$data['node'] = $node;

		$year = Input::get('year');
		$start = Input::get('start');
		$end = Input::get('end');
		$eventName = Input::get('event_name');
		$data['eventName'] = $eventName;
		$eventType = Input::get('event_type');
		$rows = array();
		$data['year'] = $year;

		$now = Carbon::now();
		//필터 관련
		//날짜
		$validator = Validator::make(Input::all(), array(
				'start'=>'date',
				'end'=>'date'
			));

		if ($validator->fails()) {
			return App::abort(400);
		}

		if (!$start) {
			$start = date('Y-m-d', strtotime('first day of January this year'));
		}

		if (!$end) {
			$end = date('Y-m-d', strtotime('last day of December this year'));
		}
		$data['start'] = $start;
		$data['end'] = $end;
		//해당 지방청 관내에서 일어난 사건만 보이게
		$query = EqCapsaicinEvent::where('node_id','=',$node->id);
		//날짜 필터 걸었다
		$query->where('date', '>=', $start)->where('date', '<=', $end);
		//행사명 필터 걸었다
		if ($eventName) {
			$query->where('event_name','like',"%$eventName%");
		}
		//행사구분 필터 검
		if ($eventType) {
			$query->where('type_code','=',$eventType);
		}
		$data['eventType'] = $eventType;
		$events = $query->orderBy('date','DESC')->get();
		$totalUsage = 0;

		$nodeFullPath = $node->full_path;
		foreach ($events as $e) {
			$usages = EqCapsaicinUsage::where('event_id','=',$e->id)->get();
			foreach ($usages as $u) {
				$row = new stdClass;
				$row->id = $u->id;
				$row->date = $e->date;
				$row->node = EqSupplyManagerNode::find($e->node_id);
				$row->user_node = EqSupplyManagerNode::find($u->user_node_id);
				$row->type = $this->service->getEventType($e->type_code);
				$row->location = $u->location;
				$row->event_name = $e->event_name;
				$row->amount = $u->amount;
				$row->crossHeadNode = $u->cross? EqSupplyManagerNode::find($u->cross->io->node_id) : '';
				$row->fileName = $u->attached_file_name;
				array_push($rows, $row);
				$totalUsage += $u->amount;
			}
		}
		$currentPage = Input::get('page')== null ? 0 : Input::get('page') - 1;
		$pagedRows = array_slice($rows, $currentPage * 15, 15);

		$data['rows'] = Paginator::make($pagedRows, count($rows), 15);

		$data['totalUsage'] = $totalUsage;

		//사용내역 엑셀로 다운로드
		if (Input::get('export')) {
			$this->service->exportCapsaicinByEvent($rows, $node, $now);
			return;
		}

		return View::make('equip.capsaicin.capsaicin-node-events',$data);
	}

	public function nodeHolding($nodeId) {

		$node = EqSupplyManagerNode::find($nodeId);
		// 훈련 내역 추가시 훈련 내역을 등록한 node의 id가 입력되므로 지방청의 child id에 대해서 사용량, 사용횟수 합을 구해줘야
		$children = EqSupplyManagerNode::where('full_path','like',$node->full_path.'%')->where('is_selectable','=',1)->get();

		$data['node'] = $node;
		$year = Input::get('year');
		if ($year == null) {
			$year = Carbon::now()->year;
		}

		$now = Carbon::now();
		$data['year'] = $year;
		$data['initYears'] = EqCapsaicinFirstday::where('node_id','=',$nodeId)->get();

		// 월별보기 탭 선택한 경우
		$firstDayHolding = EqCapsaicinFirstday::where('year','=',$year)->where('node_id','=',$nodeId)->first()->amount;

		$acquiresThisYear = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','>',$year)->sum('amount');
		$discardsThisYear = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','>',$year)->sum('amount');
		$crossUsageThisYear = EqCapsaicinCrossRegion::where('node_id','=',$nodeId)->where('used_date','>',$year)->sum('amount');

		$data['firstDayHolding'] = $firstDayHolding;

		// 훈련시 사용량 총합
		$data['usageTSum'] = 0;
		foreach($children as $child){
			$data['usageTSum'] += EqCapsaicinUsage::whereHas('event', function($q) use($child, $year){
				$q->where('type_code','=','drill')->where('node_id','=',$child->id)->where('date','like',$year.'%');
			})->sum('amount');
		}
		// 훈련시 사용량 총합
		$data['usageASum'] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$year){
			$q->where('type_code','=','assembly')->where('node_id','=',$nodeId)->where('date','like',$year.'%');
		})->sum('amount');
		// 사용량 총합
		$data['usageSumSum'] = $data['usageTSum']+$data['usageASum'];

		// 훈련시 사용횟수 총합
		$data['timesTSum'] = 0;
		foreach($children as $child){
			$data['timesTSum'] += EqCapsaicinEvent::where('node_id','=',$child->id)->where('date','like',$year.'%')->where('type_code','=','drill')->count();
		}
		// 집회 시위시 사용횟수 총합
		$data['timesASum'] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('type_code','=','assembly')->count();
		// 사용횟수 총합
		$data['timesSumSum'] = $data['timesTSum'] + $data['timesASum'];

		$data['crossUsageSum'] = $crossUsageThisYear;
		$data['additionSum'] = $acquiresThisYear;
		$data['discardSum'] = $discardsThisYear;

		$stock = array();
		$usageSum = array();
		$usageT = array();
		$usageA = array();
		$timesSum = array();
		$timesT = array();
		$timesA = array();
		$crossUsage = array();
		$addition = array();
		$discard = array();

		$consumedUntilThisMonth = 0;
		//올해면 아직 안 온 달은 비워둔다.
		$data['presentStock'] = null;
		for ($i=1; $i <= 12; $i++) {

			$firstDayofMonth = Carbon::createFromDate($year, $i, 1, 'Asia/Seoul')->subDay();
			if ($i != 12) {
				$lastDayofMonth = Carbon::createFromDate($year, $i+1, 1, 'Asia/Seoul')->subDay();
			} else {
				$lastDayofMonth = Carbon::createFromDate($year, $i, 31, 'Asia/Seoul');
			}

			// 훈련시 사용량
			$usageT[$i] = 0;
			foreach($children as $child){
				$usageT[$i] += EqCapsaicinUsage::whereHas('event', function($q) use($child,$firstDayofMonth, $lastDayofMonth){
									$q->where('type_code','=','drill')->where('node_id','=',$child->id)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
								})->sum('amount');
			}
			$usageA[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$firstDayofMonth, $lastDayofMonth){
								$q->where('type_code','=','assembly')->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
							})->sum('amount');
			$usageSum[$i] = $usageT[$i] + $usageA[$i];
			$consumedUntilThisMonth += $usageSum[$i];

			// 훈련시 사용횟수
			$timesT[$i] = 0;
			foreach($children as $child){
				$timesT[$i] += EqCapsaicinEvent::where('node_id','=',$child->id)->where('type_code','=','drill')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
			}
			$timesA[$i] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('type_code','=','assembly')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
			$timesSum[$i] = $timesT[$i] + $timesA[$i];

			$crossUsage[$i] = EqCapsaicinCrossRegion::where('node_id','=',$nodeId)->where('used_date','>',$firstDayofMonth)->where('used_date','<=',$lastDayofMonth)->sum('amount');
			$addition[$i] = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');
			$discard[$i] = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');

			$acquiredUntilThisMonth = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
			$discardedUntilThisMonth = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
			$crossUsedUntilThisMonth = EqCapsaicinCrossRegion::where('node_id','=',$nodeId)->where('used_date','<=',$lastDayofMonth)->where('used_date','like',$year.'%')->sum('amount');
			$stock[$i] = $firstDayHolding - $consumedUntilThisMonth + $acquiredUntilThisMonth - $discardedUntilThisMonth - $crossUsedUntilThisMonth;

			$month = 12;
			if ($year == $now->year) {
				$month = $now->month;
				if ($month == $i) {
					$data['presentStock'] = $stock[$i];
				}
			} elseif ($year > $now->year) {
				$stock[$i] = null;
				$usageSum[$i] = null;
				$usageT[$i] = null;
				$usageA[$i] = null;
				$timesSum[$i] = null;
				$timesT[$i] = null;
				$timesA[$i] = null;
				$crossUsage[$i] = null;
				$addition[$i] = null;
				$discard[$i] = null;
				continue;
			}

			if ($month < $i) {
				$stock[$i] = null;
				$usageSum[$i] = null;
				$usageT[$i] = null;
				$usageA[$i] = null;
				$timesSum[$i] = null;
				$timesT[$i] = null;
				$timesA[$i] = null;
				$crossUsage[$i] = null;
				$addition[$i] = null;
				$discard[$i] = null;
				continue;
			}
		}

		$data['stock'] = $stock;
		$data['usageSum'] = $usageSum;
		$data['usageT'] = $usageT;
		$data['usageA'] = $usageA;
		$data['timesSum'] = $timesSum;
		$data['timesT'] = $timesT;
		$data['timesA'] = $timesA;
		$data['crossUsage'] = $crossUsage;
		$data['addition'] = $addition;
		$data['discard'] = $discard;

		if (Input::get('export')) {
			$this->service->exportCapsaicinByMonth($data, $node, $now, $year);
			return;
		}

		return View::make('equip.capsaicin.capsaicin-node-holding',$data);
	}

	public function editUsage($usageId) {

		$usage = EqCapsaicinUsage::find($usageId);
		$event = $usage->event;

		return View::make('equip.capsaicin.capsaicin-usage-edit', get_defined_vars());
	}

	public function updateUsage($usageId) {

		$input = Input::all();
		$usage = EqCapsaicinUsage::find($usageId);
		$event = $usage->event;

		DB::beginTransaction();

		$event->date = $input['event_date'];
		$event->type_code = $input['event_type'];
		$event->event_name = $input['event_name'];
		$event->location = $input['location'];
		if (!$event->save()) {
			return App::abort(500);
		}

		// 기존 사용내역을 삭제한다.
		// 기존 사용내역이 타청지원이었다면 그거도 지워주고
		if ($usage->cross) {
			$cross = $usage->cross;
			$io = $cross->io;
			if (!$io->delete()) {
				return Redirect::back()->with('message','타청지원 추가량 삭제 중 오류가 발생했습니다');
			}
			if (!$cross->delete()) {
				return Redirect::back()->with('message','타청지원내역 삭제 중 오류가 발생했습니다.');
			}
		}
		// 이제 사용내역 지운다
		if (!$usage->delete()) {
			return Redirect::back()->with('message','캡사이신 희석액 사용내역 삭제 중 오류가 발생했습니다');
		}

		// 이제 새로 사용내역을 등록한다.
		$usage = new EqCapsaicinUsage;
		$usage->event_id = $event->id;
		$usage->user_node_id = $input['user_node_id'];
		$usage->amount = $input['amount'];

		if (!$usage->save()) {
			return App::abort(500);
		}

		$userNode = EqSupplyManagerNode::find($input['user_node_id']);

		if ($event->node_id !== $userNode->region()->id) {
		// 타 청에서 동원된 경우
		// 1. 해당 청에 추가량 등록
			$addition = new EqCapsaicinIo;
			$addition->node_id = $event->node_id;
			$addition->amount = $input['amount'];
			$addition->acquired_date = $input['event_date'];
			$addition->caption = 'CR';
			$addition->io = 1;

			if (!$addition->save()) {
				return App::abort(500);
			}
		// 2. 동원한 지방청에서 타청사용량 추가
			$crossUsage = new EqCapsaicinCrossRegion;
			$crossUsage->node_id = $event->node_id;
			$crossUsage->usage_id = $usage->id;
			$crossUsage->io_id = $addition->id;
			$crossUsage->amount = $input['amount'];
			$crossUsage->used_date = $input['event_date'];

			if (!$crossUsage->save()) {
				return App::abort(500);
			}
		}

		DB::commit();

		return Redirect::action('EqCapsaicinController@nodeEvents', $node->id )->with('message', '수정되었습니다.');
	}

	public function deleteUsageRequest($usageId) {

		$delReq = EqDeleteRequest::where('usage_id','=',$usageId)->first();

		if (!$delReq) {
			$delReq = new EqDeleteRequest;
			$delReq->usage_id = $usageId;
			$delReq->type = "cap";
			$delReq->confirmed = 0;

			if (!$delReq->save()) {
				return App::abort(500);
			}

			return "지방청 관리자 승인 후 삭제됩니다.";

		} else {
			return "삭제 대기중입니다.";
		}

	}

}
