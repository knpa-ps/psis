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
		$agencies = EqSupplyManagerNode::find(1)->children;
		$data['nodes'] = $agencies;
		$tabDept = Input::get('tab_dept');
		$data['tabDept'] = $tabDept;
		$start = Input::get('start');
		$end = Input::get('end');
		$eventName = Input::get('event_name');
		$eventType = Input::get('event_type');
		$data['eventType'] = $eventType;

		

		// 관서별 보유현황 탭일 경우
		if ($tabDept == 'true') {
			$stock = array();
			$usageSum = array();
			$usageT = array();
			$usageA = array();
			$timesSum = array();
			$timesT = array();
			$timesA = array();
			foreach ($agencies as $n) {
				$stock[$n->id] = EqCapsaicinInventory::where('node_id','=',$n->id)->first()->stock;
				$usageSum[$n->id] =  EqCapsaicinUsage::whereHas('event', function($q) use($n) {
					$q->where('node_id','=',$n->id);
				})->sum('amount');
				$usageT[$n->id] = EqCapsaicinUsage::whereHas('event', function($q) use($n) {
					$q->where('node_id','=',$n->id)->where('type_code','=','training');
				})->sum('amount');
				$usageA[$n->id] = EqCapsaicinUsage::whereHas('event', function($q) use($n) {
					$q->where('node_id','=',$n->id)->where('type_code','=','assembly');
				})->sum('amount');

				$timesSum[$n->id] = EqCapsaicinEvent::where('node_id','=',$n->id)->count();
				$timesA[$n->id] = EqCapsaicinEvent::where('node_id','=',$n->id)->where('type_code','=','assembly')->count();
				$timesT[$n->id] = EqCapsaicinEvent::where('node_id','=',$n->id)->where('type_code','=','training')->count();
	 		}
	 		$data['stock'] = $stock;
	 		$data['usageSum'] = $usageSum;
	 		$data['usageT'] = $usageT;
	 		$data['usageA'] = $usageA;
	 		$data['timesSum'] = $timesSum;
	 		$data['timesT'] = $timesT;
	 		$data['timesA'] = $timesA;

	 		$data['stockSum'] = EqCapsaicinInventory::sum('stock');
	 		$data['usageSumSum'] = EqCapsaicinUsage::sum('amount');
	 		$data['usageTSum'] = EqCapsaicinUsage::whereHas('event', function($q) use($n) {
	 			$q->where('type_code','=','training');
	 		})->sum('amount');
	 		$data['usageASum'] = EqCapsaicinUsage::whereHas('event', function($q) use($n) {
	 			$q->where('type_code','=','assembly');
	 		})->sum('amount');
	 		$data['timesSumSum'] = EqCapsaicinEvent::count();
	 		$data['timesTSum'] = EqCapsaicinEvent::where('type_code','=','training')->count();
	 		$data['timesASum'] = EqCapsaicinEvent::where('type_code','=','assembly')->count();
		} else {
			//전체보기 탭인 경우
			$validator = Validator::make(Input::all(), array(
				'start'=>'date',
				'end'=>'date'
			));

			if ($validator->fails()) {
				return App::abort(400);
			}

			if (!$start) {
				$start = date('Y-m-d', strtotime('-1 year'));
			}

			if (!$end) {
				$end = date('Y-m-d');
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

			$events = $query->get();

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
					$row->amount = $u->amount;
					array_push($rows, $row);
				}
			}
			$pagedRows = array_chunk($rows, 15);
			$page = Input::get('page')== null ? 0 : Input::get('page') - 1;
			$data['rows'] = Paginator::make($pagedRows[$page], count($rows), 15);
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
		$data['node'] = EqSupplyManagerNode::find($nodeId);
		$start = Input::get('start');
		$end = Input::get('end');
		$eventName = Input::get('event_name');
		$eventType = Input::get('event_type');
		$rows = array();

		

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
				$start = date('Y-m-d', strtotime('-1 year'));
			}

			if (!$end) {
				$end = date('Y-m-d');
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

			$events = $query->where('node_id','=',$nodeId)->get();
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
					array_push($rows, $row);
				}
			}
			$pagedRows = array_chunk($rows, 15);
			$page = Input::get('page')== null ? 0 : Input::get('page') - 1;
			$data['rows'] = Paginator::make($pagedRows[$page], count($rows), 15);
		} else {
			// 보유현황 탭 선택한 경우
			$presentStock = EqCapsaicinInventory::where('node_id','=',$nodeId)->first()->stock;
			$now = Carbon::now();
			$consumptionThisYear = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$now){
				$q->where('node_id','=',$nodeId)->where('date','>',$now->year);
			})->sum('amount');

			$acquiresThisYear = EqCapsaicinAcquire::where('node_id','=',$nodeId)->where('acquired_date','>',$now->year)->sum('amount');

			$data['presentStock'] = $presentStock;
			$data['origin'] = $presentStock + $consumptionThisYear - $acquiresThisYear;
			$data['usageSumSum'] = $consumptionThisYear;
			$data['usageTSum'] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$now){
				$q->where('type_code','=','training')->where('node_id','=',$nodeId)->where('date','>',$now->year);
			})->sum('amount');
			$data['usageASum'] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$now){
				$q->where('type_code','=','assembly')->where('node_id','=',$nodeId)->where('date','>',$now->year);
			})->sum('amount');
			$data['timesSumSum'] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','>',$now->year)->count();
			$data['timesTSum'] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','>',$now->year)->where('type_code','=','training')->count();
			$data['timesASum'] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','>',$now->year)->where('type_code','=','assembly')->count();
			$data['additionSum'] = $acquiresThisYear;

			$stock = array();
			$usageSum = array();
			$usageT = array();
			$usageA = array();
			$timesSum = array();
			$timesT = array();
			$timesA = array();
			$addition = array();

			for ($i=1; $i <= 12; $i++) { 
				$firstDayofMonth = Carbon::createFromDate($now->year, $i, 1, 'Asia/Seoul');
				$afterithMonth = Carbon::createFromDate($now->year, $i+1, 1, 'Asia/Seoul');

				$consumptionSinceithMonth = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$now,$afterithMonth){
					$q->where('node_id','=',$nodeId)->where('date','>',$afterithMonth)->where('date','<',$now);
				})->sum('amount');
				$acquireSinceithMonth = EqCapsaicinAcquire::where('node_id','=',$nodeId)->where('acquired_date','>',$afterithMonth)->where('acquired_date','<',$now)->sum('amount');
				$stock[$i] = $presentStock + $consumptionSinceithMonth - $acquireSinceithMonth;
				$usageSum[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId, $firstDayofMonth, $afterithMonth){
									$q->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<',$afterithMonth);
								})->sum('amount');
				$usageT[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$firstDayofMonth, $afterithMonth){
									$q->where('type_code','=','training')->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<',$afterithMonth);
								})->sum('amount');
				$usageA[$i] = EqCapsaicinUsage::whereHas('event', function($q) use($nodeId,$firstDayofMonth, $afterithMonth){
									$q->where('type_code','=','assembly')->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<',$afterithMonth);
								})->sum('amount');
				$timesSum[$i] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<',$afterithMonth)->count();
				$timesT[$i] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('type_code','=','training')->where('date','>',$firstDayofMonth)->where('date','<',$afterithMonth)->count();
				$timesA[$i] = EqCapsaicinEvent::where('node_id','=',$nodeId)->where('type_code','=','assembly')->where('date','>',$firstDayofMonth)->where('date','<',$afterithMonth)->count();
				$addition[$i] = EqCapsaicinAcquire::where('acquired_date','>',$afterithMonth->subMonth())->where('acquired_date','<',$afterithMonth)->sum('amount');
			}

			$data['stock'] = $stock;
			$data['usageSum'] = $usageSum;
			$data['usageT'] = $usageT;
			$data['usageA'] = $usageA;
			$data['timesSum'] = $timesSum;
			$data['timesT'] = $timesT;
			$data['timesA'] = $timesA;
			$data['addition'] = $addition;
		}

		$data['date'] = Carbon::now('Asia/Seoul');
		
		return View::make('equip.capsaicin-per-node',$data);
	}

}
