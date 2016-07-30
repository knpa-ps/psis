<?php
use Carbon\Carbon;

class EqWaterPavaController extends EquipController {

	public function deleteEventRequest($usageId) {
		$delReq = EqDeleteRequest::where('usage_id','=',$usageId)->first();

		if (!$delReq) {
			$delReq = new EqDeleteRequest;
			$delReq->usage_id = $usageId;
			$delReq->type = "pava";
			$delReq->confirmed = 0;

			if (!$delReq->save()) {
				return App::abort(500);
			}

			return "본청 관리자 승인 후 삭제됩니다.";
		} else {
			return "삭제 대기중입니다.";
		}
	}

	public function showRegionConfirm() {
		$user = Sentry::getUser();
		$node = $user->supplySet->node;

		$data['node'] = $node;
		$data['requests'] = EqDeleteRequest::where('confirmed','=','0')->where('type','=','pava')->paginate(15);

		return View::make('equip.waterpava.pava-region-confirm', $data);
	}

	public function pavaPerMonthData() {

		$year = Input::get('year');
		$nodeId = Input::get('regionId');

		return $this->service->getPavaPerMonthData($year, $nodeId);
	}

	public function pavaPerMonth()
	{
		$user = Sentry::getuser();
		$node = $user->supplySet->node;

		$nowYear = Carbon::now()->year;

		$selectedYear = Input::get('year') ? Input::get('year') : $nowYear;

		if ($node->type_code == "D001") {

			$regions = EqSupplyManagerNode::where('type_code','=',"D002")->get();
			$selectedNodeId = Input::get("nodeId") ? Input::get("nodeId") : 2;
			$data = $this->service->getPavaPerMonthData($selectedYear, $selectedNodeId);
			$data['regions'] = $regions;
			$selectedNode = EqSupplyManagerNode::find($selectedNodeId);
			$data['selectedNode'] = $selectedNode;
			return View::make("equip.waterpava.pava-per-month-head", $data);
		}


		$data = $this->service->getPavaPerMonthData($selectedYear, $node->id);

		return View::make('equip.waterpava.pava-per-month', $data);
	}

	public function getConsumptionPerMonth()
	{
		$regionId = Input::get('regionId');
		$year = Input::get('year');

		//월별 살수량 합 사용횟수 합 구하기
		$amountPerMonth = array();
		$countPerMonth = array();

		for ($i=1; $i <= 12; $i++) {

			$firstDayofMonth = Carbon::createFromDate($year, $i, 1, 'Asia/Seoul')->subDay();
			if ($i != 12) {
				$lastDayofMonth = Carbon::createFromDate($year, $i+1, 1, 'Asia/Seoul')->subDay();
			} else {
				$lastDayofMonth = Carbon::createFromDate($year, $i, 31, 'Asia/Seoul');
			}

			$events = EqWaterPavaEvent::where('node_id','=',$regionId)->where('date','>=',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->get();
			$sum = $events->sum('amount');

			$count = $events->count();

			$amountPerMonth[] = round($sum,2);
			$countPerMonth[] = $count;

		}

		$regionName = EqSupplyManagerNode::find($regionId)->node_name;

		$data[] = $amountPerMonth;
		$data[] = $countPerMonth;
		$data[] = $regionName;

		return $data;
	}

	public function waterPerMonth()
	{
		$user = Sentry::getUser();
		$node = $user->supplySet->node;
		$nowYear = Carbon::now()->year;

		$selectedYear = Input::get('year') ? Input::get('year') : $nowYear;

		$oldest = EqWaterPavaEvent::orderBy('date','ASC')->first();
		if ($oldest) {
			$oldestDate=$oldest->date;
			$initYear = substr($oldestDate, 0, 4);
		} else {
			$initYear = $nowYear;
		}
		if ($user->supplySet->node->type_code == "D002") {
			return View::make('equip.waterpava.water-per-month', get_defined_vars());
		}

		$regions = EqSupplyManagerNode::where('type_code','=',"D002")->get();

		$consumption = array();
		$count = array();

		$consumptionSum = 0;
		$countSum = 0;

		foreach ($regions as $r) {

			$events = EqWaterPavaEvent::where('node_id','=',$r->id)->where('date','>=',$selectedYear)->where('date','<=',$selectedYear+1)->get();

		}
        return View::make('equip.waterpava.water-per-month-head', get_defined_vars());
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Sentry::getUser();
		$node = $user->supplySet->node;
		$data['node'] = $node;
		$start = Input::get('start');
		$end = Input::get('end');
		$eventName = Input::get('event_name');
		$data['eventName'] = $eventName;
		$selectedRegionId = Input::get('region');

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
		$query = EqWaterPavaEvent::where('date', '>=', $start)->where('date', '<=', $end);
		//행사명 필터 걸었다
		if ($eventName) {
			$query->where('event_name','like',"%$eventName%");
		}

		// 본청계정이 아닌 경우 자기 지방청에서 올린것만 볼 수 있음
		if ($node->type_code=="D002") {
			$query->where('node_id','=',$node->id);
		}

		// 본청계정인 경우 지방청 필터 관련 데이터 넣어줌
		if ($node->type_code == "D001") {
			if ($selectedRegionId) {
				$query->where('node_id','=',$selectedRegionId);
			}
			$data['regions'] = EqSupplyManagerNode::where('type_code','=',"D002")->get();
			$data['region'] = $selectedRegionId;
		}

		$events = $query->orderBy('date','DESC')->paginate(15);

		$data['events'] = $events;

		// if (Input::get('export')) {
		// 	$this->service->exportCapsaicinByEvent($rows, EqSupplyManagerNode::find($regionId), $now);
		// 	return;
		// }

        return View::make('equip.waterpava.index',$data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$user = Sentry::getUser();
		$node = $user->supplySet->node;
		$mode = 'create';
        return View::make('equip.waterpava.create', get_defined_vars());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input=Input::all();

		$event = new EqWaterPavaEvent;
		$event->event_name = $input['event_name'];
		$event->node_id = $input['node_id'];
		$event->location = $input['location'];
		$event->date = $input['date'];
		$event->amount = $input['water'];

		if (array_key_exists('pava', $input)) {
			if (is_numeric($input['pava'])) {
				$event->pava_amount = $input['pava'];
			} else {
				return Redirect::back()->with('message', "pava사용량은 숫자만 입력가능합니다.");
			}
		}
		if (array_key_exists('dye', $input)) {
			if (is_numeric($input['dye'])) {
				$event->dye_amount = $input['dye'];
			} else {
				return Redirect::back()->with('message', "염료 사용량은 숫자만 입력가능합니다.");
			}
		}

		if (array_key_exists('file_name', $input)) {
			$event->attached_file_name = $input['file_name'];
		}


		if (!$event->save()) {
			return App::abort(500);
		}

		return Redirect::action('EqWaterPavaController@index')->with('message', '저장되었습니다.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        return View::make('eqwaterpavas.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make('equip.waterpava.edit');
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
		$event = EqWaterPavaEvent::find($id);

		if (!$event->delete()) {
			return '사용내역 삭제 도중 오류가 발생했습니다.';
		}

		return '해당 사용내역이 삭제되었습니다.';
	}

}
