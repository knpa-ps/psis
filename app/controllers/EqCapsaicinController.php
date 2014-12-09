<?php
use Carbon\Carbon;

class EqCapsaicinController extends EquipController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Sentry::getUser();

		if ($user->supplyNode->id !== 1) {
			if ($user->supplyNode->managedParent->id == 1) {
				return Redirect::action('EqCapsaicinController@displayNodeState', $user->supplyNode->id);
			} else {
				return Redirect::back()->with('message','해당 메뉴는 본청,지방청 관리자만 접근 가능합니다');
			}
		}

		$agencies = EqSupplyManagerNode::where('type_code','=','D002')->get();
		$data['nodes'] = $agencies;
		$tabId = Input::get('tab_id');
		$data['tabId'] = $tabId;
		$start = Input::get('start');
		$end = Input::get('end');
		$eventName = Input::get('event_name');
		$eventType = Input::get('event_type');
		$data['eventType'] = $eventType;
		$regionId = Input::get('region');
		$data['region'] = $regionId;
		$year = Carbon::now()->year;
		$data['year'] = $year;

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


					$usageT[$n->id] = EqCapsaicinUsage::whereHas('event', function($q) use($n,$year) {
						$q->where('node_id','=',$n->id)->where('date','like',$year.'%')->where('type_code','=','training');
					})->sum('amount');
					$usageA[$n->id] = EqCapsaicinUsage::whereHas('event', function($q) use($n,$year) {
						$q->where('node_id','=',$n->id)->where('date','like',$year.'%')->where('type_code','=','assembly');
					})->sum('amount');
					$usageSum[$n->id] = $usageT[$n->id] + $usageA[$n->id];

					/**
					 * 당해 최초보유량 - 당해 사용량 + 당해 보급량 -당해 불용량 = 현재보유량임.
					 */
					$initHolding = EqCapsaicinFirstday::where('node_id','=',$n->id)->where('year','=',$year)->first()->amount;
					$added = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$n->id)->where('acquired_date','like',$year.'%')->sum('amount');
					$discarded = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$n->id)->where('acquired_date','like',$year.'%')->sum('amount');
					$stock[$n->id] = $initHolding - $usageSum[$n->id] + $added;
					$stockSum += $stock[$n->id];

					$timesSum[$n->id] = EqCapsaicinEvent::where('node_id','=',$n->id)->where('date','>',$year)->count();
					$timesA[$n->id] = EqCapsaicinEvent::where('node_id','=',$n->id)->where('date','>',$year)->where('type_code','=','assembly')->count();
					$timesT[$n->id] = EqCapsaicinEvent::where('node_id','=',$n->id)->where('date','>',$year)->where('type_code','=','training')->count();
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
					$consumptionThisYear = EqCapsaicinUsage::whereHas('event', function($q) use($year){
						$q->where('date','>',$year);
					})->sum('amount');

					$acquiresThisYear = EqCapsaicinIo::where('io','=',1)->where('acquired_date','>',$year)->sum('amount');
					$discardsThisYear = EqCapsaicinIo::where('io','=',0)->where('acquired_date','>',$year)->sum('amount');

					$data['firstDayHolding'] = $firstDayHolding;

					$data['usageSumSum'] = $consumptionThisYear;
					$data['usageTSum'] = EqCapsaicinUsage::whereHas('event', function($q) use($year){
						$q->where('type_code','=','training')->where('date','like',$year.'%');
					})->sum('amount');
					$data['usageASum'] = EqCapsaicinUsage::whereHas('event', function($q) use($year){
						$q->where('type_code','=','assembly')->where('date','like',$year.'%');
					})->sum('amount');

					$data['timesSumSum'] = EqCapsaicinEvent::where('date','like',$year.'%')->count();
					$data['timesTSum'] = EqCapsaicinEvent::where('date','like',$year.'%')->where('type_code','=','training')->count();
					$data['timesASum'] = EqCapsaicinEvent::where('date','like',$year.'%')->where('type_code','=','assembly')->count();
					$data['additionSum'] = $acquiresThisYear;
					$data['discardSum'] = $discardsThisYear;

					$stock = array();
					$usageSum = array();
					$usageT = array();
					$usageA = array();
					$timesSum = array();
					$timesT = array();
					$timesA = array();
					$addition = array();
					$discard = array();

					$now = Carbon::now();
					//올해면 아직 안 온 달은 비워둔다.
					$data['presentStock'] = null;
					for ($i=1; $i <= 12; $i++) {
						
						$firstDayofMonth = Carbon::createFromDate($year, $i, 1, 'Asia/Seoul')->subDay();
						if ($i != 12) {
							$lastDayofMonth = Carbon::createFromDate($year, $i+1, 1, 'Asia/Seoul')->subDay();
						} else {
							$lastDayofMonth = Carbon::createFromDate($year, $i, 31, 'Asia/Seoul');
						}

						$consumptionUntilithMonth = EqCapsaicinUsage::whereHas('event', function($q) use($year,$lastDayofMonth){
							$q->where('date','<=',$lastDayofMonth)->where('date','like',$year.'%');
						})->sum('amount');
						$acquireUntilithMonth = EqCapsaicinIo::where('io','=',1)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
						$discardUntilithMonth = EqCapsaicinIo::where('io','=',0)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
						$stock[$i] = $firstDayHolding - $consumptionUntilithMonth + $acquireUntilithMonth - $discardUntilithMonth;


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
							$addition[$i] = null;
							$discard[$i] = null;
							continue;
						}

						
						$usageSum[$i] = EqCapsaicinUsage::whereHas('event', function($q) use( $firstDayofMonth, $lastDayofMonth){
											$q->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
										})->sum('amount');
						$usageT[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($firstDayofMonth, $lastDayofMonth){
											$q->where('type_code','=','training')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
										})->sum('amount');
						$usageA[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($firstDayofMonth, $lastDayofMonth){
											$q->where('type_code','=','assembly')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
										})->sum('amount');

						$timesSum[$i] = EqCapsaicinEvent::where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
						$timesT[$i] = EqCapsaicinEvent::where('type_code','=','training')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
						$timesA[$i] = EqCapsaicinEvent::where('type_code','=','assembly')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
						$addition[$i] = EqCapsaicinIo::where('io','=',1)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');
						$discard[$i]  = EqCapsaicinIo::where('io','=',0)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');
					}
					$data['stock'] = $stock;
					$data['usageSum'] = $usageSum;
					$data['usageT'] = $usageT;
					$data['usageA'] = $usageA;
					$data['timesSum'] = $timesSum;
					$data['timesT'] = $timesT;
					$data['timesA'] = $timesA;
					$data['addition'] = $addition;
					$data['discard'] = $discard;
				} else {
					//지방청 필터 건 경우
					$nodeId = $selectedRegionId;

					$firstDayHolding = EqCapsaicinFirstday::where('year','=',$year)->where('node_id','=',$nodeId)->first()->amount;
					$consumptionThisYear = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$year){
						$q->where('node_id','=',$nodeId)->where('date','>',$year);
					})->sum('amount');

					$acquiresThisYear = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','>',$year)->sum('amount');
					$discardsThisYear = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','>',$year)->sum('amount');

					$data['firstDayHolding'] = $firstDayHolding;

					$data['usageSumSum'] = $consumptionThisYear;
					$data['usageTSum'] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId, $year){
						$q->where('type_code','=','training')->where('node_id','=',$nodeId)->where('date','like',$year.'%');
					})->sum('amount');
					$data['usageASum'] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$year){
						$q->where('type_code','=','assembly')->where('node_id','=',$nodeId)->where('date','like',$year.'%');
					})->sum('amount');
						
					$data['timesSumSum'] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','like',$year.'%')->count();
					$data['timesTSum'] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('type_code','=','training')->count();
					$data['timesASum'] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('type_code','=','assembly')->count();
					$data['additionSum'] = $acquiresThisYear;
					$data['discardSum'] = $discardsThisYear;

					$stock = array();
					$usageSum = array();
					$usageT = array();
					$usageA = array();
					$timesSum = array();
					$timesT = array();
					$timesA = array();
					$addition = array();
					$discard = array();

					$now = Carbon::now();
					//올해면 아직 안 온 달은 비워둔다.
					$data['presentStock'] = null;
					for ($i=1; $i <= 12; $i++) {
						
						$firstDayofMonth = Carbon::createFromDate($year, $i, 1, 'Asia/Seoul')->subDay();
						if ($i != 12) {
							$lastDayofMonth = Carbon::createFromDate($year, $i+1, 1, 'Asia/Seoul')->subDay();
						} else {
							$lastDayofMonth = Carbon::createFromDate($year, $i, 31, 'Asia/Seoul');
						}

						$consumptionUntilithMonth = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$year,$lastDayofMonth){
							$q->where('node_id','=',$nodeId)->where('date','<=',$lastDayofMonth)->where('date','like',$year.'%');
						})->sum('amount');
						$acquireUntilithMonth = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
						$discardUntilithMonth = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
						$stock[$i] = $firstDayHolding - $consumptionUntilithMonth + $acquireUntilithMonth - $discardUntilithMonth;


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
							$addition[$i] = null;
							$discard[$i] = null;
							continue;
						}

						
						$usageSum[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId, $firstDayofMonth, $lastDayofMonth){
											$q->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
										})->sum('amount');
						$usageT[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$firstDayofMonth, $lastDayofMonth){
											$q->where('type_code','=','training')->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
										})->sum('amount');
						$usageA[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$firstDayofMonth, $lastDayofMonth){
											$q->where('type_code','=','assembly')->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
										})->sum('amount');

						$timesSum[$i] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
						$timesT[$i] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('type_code','=','training')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
						$timesA[$i] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('type_code','=','assembly')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
						$addition[$i] = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');
						$discard[$i]  = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');
					}
					$data['stock'] = $stock;
					$data['usageSum'] = $usageSum;
					$data['usageT'] = $usageT;
					$data['usageA'] = $usageA;
					$data['timesSum'] = $timesSum;
					$data['timesT'] = $timesT;
					$data['timesA'] = $timesA;
					$data['addition'] = $addition;
					$data['discard'] = $discard;
				}
				break;
			case '3':
			//전체보기 탭인 경우
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
						$row->date = $e->date;
						$row->node = EqSupplyManagerNode::find($e->node_id);
						$row->user_node = EqSupplyManagerNode::find($u->user_node_id);
						$row->type = $this->service->getEventType($e->type_code);
						$row->location = $e->location;
						$row->event_name = $e->event_name;
						$row->fileName = $e->attached_file_name;
						$row->amount = $u->amount;
						$totalUsage += $u->amount;
						array_push($rows, $row);
					}
				}
				$pagedRows = array_chunk($rows, 15);
				$page = Input::get('page')== null ? 0 : Input::get('page') - 1;

				$data['rows'] = Paginator::make($pagedRows[$page], count($rows), 15);

				$data['totalUsage'] = $totalUsage;
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


					$usageT[$n->id] = EqCapsaicinUsage::whereHas('event', function($q) use($n,$year) {
						$q->where('node_id','=',$n->id)->where('date','like',$year.'%')->where('type_code','=','training');
					})->sum('amount');
					$usageA[$n->id] = EqCapsaicinUsage::whereHas('event', function($q) use($n,$year) {
						$q->where('node_id','=',$n->id)->where('date','like',$year.'%')->where('type_code','=','assembly');
					})->sum('amount');
					$usageSum[$n->id] = $usageT[$n->id] + $usageA[$n->id];

					/**
					 * 당해 최초보유량 - 당해 사용량 + 당해 보급량 -당해 불용량 = 현재보유량임.
					 */
					$initHolding = EqCapsaicinFirstday::where('node_id','=',$n->id)->where('year','=',$year)->first()->amount;
					$added = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$n->id)->where('acquired_date','like',$year.'%')->sum('amount');
					$discarded = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$n->id)->where('acquired_date','like',$year.'%')->sum('amount');
					$stock[$n->id] = $initHolding - $usageSum[$n->id] + $added;
					$stockSum += $stock[$n->id];

					$timesSum[$n->id] = EqCapsaicinEvent::where('node_id','=',$n->id)->where('date','>',$year)->count();
					$timesA[$n->id] = EqCapsaicinEvent::where('node_id','=',$n->id)->where('date','>',$year)->where('type_code','=','assembly')->count();
					$timesT[$n->id] = EqCapsaicinEvent::where('node_id','=',$n->id)->where('date','>',$year)->where('type_code','=','training')->count();
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
				break;
		}

		return View::make('equip.capsaicin-index', $data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$data['node'] = EqSupplyManagerNode::find(Input::get('nodeId'));
		$data['mode'] = 'create';
		
		return View::make('equip.capsaicin-usage-form',$data);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$usageLength = sizeof($input['nodeId']);
		DB::beginTransaction();

		$event = new EqCapsaicinEvent;
		$event->type_code = $input['classification'];
		$event->event_name = $input['event_name'];
		$event->location = $input['location'];
		$event->node_id = $input['node'];
		$event->date = $input['date'];
		$event->attached_file_name = $input['file_name'];
		if (!$event->save()) {
			return App::abort(500);
		}

		for ($i=0; $i < $usageLength; $i++) { 
			$usage = new EqCapsaicinUsage;
			$usage->event_id = $event->id;
			$usage->amount = $input['amount'][$i];
			$usage->user_node_id = $input['nodeId'][$i];

			if (!$usage->save()) {
				return App::abort(500);
			}
		}

		DB::commit();

		return Redirect::action('EqCapsaicinController@displayNodeState', $input['node'])->with('message', '저장되었습니다.');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
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

	public function displayNodeState($nodeId)
	{
		$user = Sentry::getUser();
		$isState = Input::get('is_state');
		$data['isState'] = $isState;
		$node = EqSupplyManagerNode::find($nodeId);
		$data['node'] = $node;
		$start = Input::get('start');
		$end = Input::get('end');
		$eventName = Input::get('event_name');
		$data['eventName'] = $eventName;
		$eventType = Input::get('event_type');
		$rows = array();
		$year = Input::get('year');
		$now = Carbon::now();

		if ($year == null) {
			$year = Carbon::now()->year;
		}
		$data['initYears'] = EqCapsaicinFirstday::where('node_id','=',$nodeId)->get();
		

		if ($isState !== 'true') {
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
			$data['eventType'] = $eventType;
			$events = $query->where('node_id','=',$nodeId)->orderBy('date','DESC')->get();
			$totalUsage = 0;
			foreach ($events as $e) {
				$usages = $e->children;
				foreach ($usages as $u) {
					$row = new stdClass;
					$row->date = $e->date;
					$row->node = EqSupplyManagerNode::find($e->node_id);
					$row->user_node = EqSupplyManagerNode::find($u->user_node_id);
					$row->type = $this->service->getEventType($e->type_code);
					$row->location = $e->location;
					$row->event_name = $e->event_name;
					$row->amount = $u->amount;
					$row->fileName = $e->attached_file_name;
					array_push($rows, $row);
					$totalUsage += $u->amount;
				}
			}
			if (sizeof($rows)!=0) {
				$pagedRows = array_chunk($rows, 15);
				$page = Input::get('page')== null ? 0 : Input::get('page') - 1;
				$data['rows'] = Paginator::make($pagedRows[$page], count($rows), 15);
			} else {
				$data['rows'] = Paginator::make(array(),0,15);
			}
			$data['totalUsage'] = $totalUsage;

			if (Input::get('export')) {
				//xls obj 생성
				$objPHPExcel = new PHPExcel();
				$fileName = $node->node_name.' 캡사이신 희석액 사용내역'; 
				//obj 속성
				$objPHPExcel->getProperties()
					->setCreator($user->user_name)
					->setTitle($fileName)
					->setSubject($fileName);
				//셀 정렬(가운데)
				$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				
				$sheet = $objPHPExcel->setActiveSheetIndex(0);
				
				$sheet->setCellValue('a1','일자');
				$sheet->setCellValue('b1','관서명');
				$sheet->setCellValue('c1','중대');
				$sheet->setCellValue('d1','행사유형');
				$sheet->setCellValue('e1','사용장소');
				$sheet->setCellValue('f1','행사명');
				$sheet->setCellValue('g1','사용량(ℓ)');
				//양식 부분 끝
				//이제 사용내역 나옴
				for ($i=1; $i <= sizeof($rows); $i++) { 
					$sheet->setCellValue('a'.($i+1),$rows[$i-1]->date);
					$sheet->setCellValue('b'.($i+1),$rows[$i-1]->node->node_name);
					$sheet->setCellValue('c'.($i+1),$rows[$i-1]->user_node->node_name);
					$sheet->setCellValue('d'.($i+1),$rows[$i-1]->type);
					$sheet->setCellValue('e'.($i+1),$rows[$i-1]->location);
					$sheet->setCellValue('f'.($i+1),$rows[$i-1]->event_name);
					$sheet->setCellValue('g'.($i+1),round(($i+1),$rows[$i-1]->amount, 2));
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
		} else {
			// 월별보기 탭 선택한 경우
			$firstDayHolding = EqCapsaicinFirstday::where('year','=',$year)->where('node_id','=',$nodeId)->first();
			$consumptionThisYear = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$year){
				$q->where('node_id','=',$nodeId)->where('date','>',$year);
			})->sum('amount');

			$acquiresThisYear = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','>',$year)->sum('amount');
			$discardsThisYear = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','>',$year)->sum('amount');

			$data['firstDayHolding'] = $firstDayHolding;

			$data['usageSumSum'] = $consumptionThisYear;
			$data['usageTSum'] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId, $year){
				$q->where('type_code','=','training')->where('node_id','=',$nodeId)->where('date','like',$year.'%');
			})->sum('amount');
			$data['usageASum'] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$year){
				$q->where('type_code','=','assembly')->where('node_id','=',$nodeId)->where('date','like',$year.'%');
			})->sum('amount');
			$data['timesSumSum'] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','like',$year.'%')->count();
			$data['timesTSum'] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('type_code','=','training')->count();
			$data['timesASum'] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('type_code','=','assembly')->count();
			$data['additionSum'] = $acquiresThisYear;
			$data['discardSum'] = $discardsThisYear;

			$stock = array();
			$usageSum = array();
			$usageT = array();
			$usageA = array();
			$timesSum = array();
			$timesT = array();
			$timesA = array();
			$addition = array();
			$discard = array();

			//올해면 아직 안 온 달은 비워둔다.
			$data['presentStock'] = null;
			for ($i=1; $i <= 12; $i++) {
				
				$firstDayofMonth = Carbon::createFromDate($year, $i, 1, 'Asia/Seoul')->subDay();
				if ($i != 12) {
					$lastDayofMonth = Carbon::createFromDate($year, $i+1, 1, 'Asia/Seoul')->subDay();
				} else {
					$lastDayofMonth = Carbon::createFromDate($year, $i, 31, 'Asia/Seoul');
				}


				$consumptionUntilithMonth = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$year,$lastDayofMonth){
					$q->where('node_id','=',$nodeId)->where('date','<=',$lastDayofMonth)->where('date','like',$year.'%');
				})->sum('amount');
				$acquireUntilithMonth = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
				$discardUntilithMonth = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','<=',$lastDayofMonth)->where('acquired_date','like',$year.'%')->sum('amount');
				$stock[$i] = $firstDayHolding->amount - $consumptionUntilithMonth + $acquireUntilithMonth - $discardUntilithMonth;


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
					$addition[$i] = null;
					$discard[$i] = null;
					continue;
				}

				$usageSum[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId, $firstDayofMonth, $lastDayofMonth){
									$q->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
								})->sum('amount');
				$usageT[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$firstDayofMonth, $lastDayofMonth){
									$q->where('type_code','=','training')->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
								})->sum('amount');
				$usageA[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$firstDayofMonth, $lastDayofMonth){
									$q->where('type_code','=','assembly')->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth);
								})->sum('amount');

				$timesSum[$i] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
				$timesT[$i] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('type_code','=','training')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
				$timesA[$i] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('type_code','=','assembly')->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->count();
				$addition[$i] = EqCapsaicinIo::where('io','=',1)->where('node_id','=',$nodeId)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');
				$discard[$i] = EqCapsaicinIo::where('io','=',0)->where('node_id','=',$nodeId)->where('acquired_date','>',$firstDayofMonth)->where('acquired_date','<=',$lastDayofMonth)->sum('amount');

			}

			$data['stock'] = $stock;
			$data['usageSum'] = $usageSum;
			$data['usageT'] = $usageT;
			$data['usageA'] = $usageA;
			$data['timesSum'] = $timesSum;
			$data['timesT'] = $timesT;
			$data['timesA'] = $timesA;
			$data['addition'] = $addition;
			$data['discard'] = $discard;

			if (Input::get('export')) {
				//xls obj 생성
				$objPHPExcel = new PHPExcel();
				$fileName = $node->node_name.' '.$year.' 캡사이신 희석액 현황'; 
				//obj 속성
				$objPHPExcel->getProperties()
					->setCreator($user->user_name)
					->setTitle($fileName)
					->setSubject($fileName);
				//셀 정렬(가운데)
				$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				
				$sheet = $objPHPExcel->setActiveSheetIndex(0);
				$sheet->mergeCells('a1:a3');
				$sheet->mergeCells('b1:c1');
				$sheet->mergeCells('d1:f1');
				$sheet->mergeCells('g1:i1');
				$sheet->mergeCells('j1:j2');
				$sheet->mergeCells('k1:k2');

				for ($i=1; $i <=12 ; $i++) { 
					$sheet->mergeCells('b'.($i+3).':c'.($i+3));
				}

				$sheet->setCellValue('a1','구분');
				$sheet->setCellValue('b1','보유량(ℓ)');
				$sheet->setCellValue('d1','사용량(ℓ)');
				$sheet->setCellValue('g1','사용횟수');
				$sheet->setCellValue('j1','추가량(ℓ)');
				$sheet->setCellValue('k1','불용량(ℓ)');
				$sheet->setCellValue('b2','현재보유량(ℓ)');
				$sheet->setCellValue('c2','최초보유량(ℓ)');
				$sheet->setCellValue('d2','계');
				$sheet->setCellValue('e2','훈련시');
				$sheet->setCellValue('f2','집회 시위시');
				$sheet->setCellValue('g2','계');
				$sheet->setCellValue('h2','훈련시');
				$sheet->setCellValue('i2','집회 시위시');
				$sheet->setCellValue('b3',isset($data['presentStock']) ? round($data['presentStock'], 2) : '');
				$sheet->setCellValue('c3',round($data['firstDayHolding']->amount, 2));
				$sheet->setCellValue('d3',round($data['usageSumSum'], 2));
				$sheet->setCellValue('e3',round($data['usageTSum'], 2));
				$sheet->setCellValue('f3',round($data['usageASum'], 2));
				$sheet->setCellValue('g3',$data['timesSumSum']);
				$sheet->setCellValue('h3',$data['timesTSum']);
				$sheet->setCellValue('i3',$data['timesASum']);
				$sheet->setCellValue('j3',round($data['additionSum'], 2));
				$sheet->setCellValue('k3',round($data['discardSum'], 2));
				//양식 부분 끝
				//이제 월별 자료 나옴
				
				for ($i=1; $i <=12 ; $i++) { 
					$sheet->setCellValue('A'.($i+3), $i.'월');
					if (isset($data['stock'][$i])) {
						$sheet->setCellValue('B'.($i+3), round($data['stock'][$i], 2) );
						$sheet->setCellValue('D'.($i+3), round($data['usageSum'][$i], 2) );
						$sheet->setCellValue('E'.($i+3), round($usageT[$i], 2) );
						$sheet->setCellValue('F'.($i+3), round($usageA[$i], 2) );
					}
					
					$sheet->setCellValue('G'.($i+3), $data['timesSum'][$i] );
					$sheet->setCellValue('H'.($i+3), $data['timesT'][$i] );
					$sheet->setCellValue('I'.($i+3), $data['timesA'][$i] );
					$sheet->setCellValue('J'.($i+3), round($data['addition'][$i], 2) );
					$sheet->setCellValue('K'.($i+3), round($data['discard'][$i], 2) );

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
				header('Content-Disposition: attachment; filename="'.$fileName.$now.'.xlsx"');
				header("Content-Transfer-Encoding: binary ");
				$writer->save('php://output');
				return;
			}



		}

		$data['year'] = $year;
		
		return View::make('equip.capsaicin-per-node',$data);
	}

}
