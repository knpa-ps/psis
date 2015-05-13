<?php
use Carbon\Carbon;

class EqWaterController extends EquipController {

	public function getConsumptionPerMonth()
	{
		$regionId = Input::get('regionId');
		$year = Input::get('year');

		//월별 사용량, 사용횟수 합 구하기
		$waterConsumptionPerMonth = array();
		$countPerMonth = array();

		for ($i=1; $i <= 12; $i++) {

			$firstDayofMonth = Carbon::createFromDate($year, $i, 1, 'Asia/Seoul')->subDay();
			if ($i != 12) {
				$lastDayofMonth = Carbon::createFromDate($year, $i+1, 1, 'Asia/Seoul')->subDay();
			} else {
				$lastDayofMonth = Carbon::createFromDate($year, $i, 31, 'Asia/Seoul');
			}

			$events = EqWaterEvent::where('node_id','=',$regionId)->where('date','>=',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->get();
			$consumption = $events->sum('amount');
			$count = $events->count();

			$waterConsumptionPerMonth[] = round($consumption,2);
			$countPerMonth[] = $count;

		}

		$regionName = EqSupplyManagerNode::find($regionId)->node_name;

		$data[] = $waterConsumptionPerMonth;
		$data[] = $countPerMonth;
		$data[] = $regionName;

		return $data;
	}

	public function index_by_region()
	{
		$user = Sentry::getUser();
		$year = Carbon::now()->year;

		if ($user->supplyNode->type_code == "D002") {
			return View::make('equip.water-bymonth', get_defined_vars());
		}

		$regions = EqSupplyManagerNode::where('type_code','=',"D002")->get();

		$consumption = array();
		$count = array();

		$consumptionSum = 0;
		$countSum = 0;

		foreach ($regions as $r) {

			$consumption[$r->id] = EqWaterEvent::where('node_id','=',$r->id)->where('date','>=',$year)->sum('amount');
			$count[$r->id] = EqWaterEvent::where('node_id','=',$r->id)->where('date','>=',$year)->count();

			$consumptionSum += $consumption[$r->id];
			$countSum += $count[$r->id];
		}
		
        return View::make('equip.water-byregion', get_defined_vars());	
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Sentry::getUser();
		$userNode = $user->supplyNode;
		$userNode->type_code == "D002" ? $data['isRegion'] = true : $data['isRegion'] = false;

		$data['nodeId'] = $userNode->id;

		$start = Input::get('start');
		$end = Input::get('end');
		$eventName = Input::get('event_name');
		$data['eventName'] = $eventName;
		$regionId = Input::get('region');
		$data['region'] = $regionId;
		$year = Carbon::now()->year;
		$data['year'] = $year;
		$now = Carbon::now();

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
		$query = EqWaterEvent::where('date', '>=', $start)->where('date', '<=', $end);
		//행사명 필터 걸었다
		if ($eventName) {
			$query->where('event_name','like',"%$eventName%");
		}
		
		//지방청 필터 검
		$data['regions'] = EqSupplyManagerNode::where('type_code','=','D002')->get();
		if ($regionId) {
			$query->where('node_id','=',$regionId);
		}


		$data['totalUsage'] = $query->get()->sum('amount');

		$data['rows'] = $query->orderBy('date','DESC')->paginate(15);


		if (Input::get('export')) {
			$this->service->exportWaterByEvent($data['rows'], EqSupplyManagerNode::find($regionId), $now);
			return;
		}

        return View::make('equip.water-byaffair', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$mode = 'create';
		$nodeId = Input::get('nodeId');	
		$node = EqSupplyManagerNode::find($nodeId);

        return View::make('equip.water-usage-form', get_defined_vars());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$event = new EqWaterEvent;

		$event->event_name = $input["event_name"];
		$event->node_id = $input["node"];
		$event->location = $input["location"];
		$event->date = $input["date"];
		$event->amount = $input["amount"];
		$event->attached_file_name = $input["file_name"];

		if (!$event->save()) {
			return App::abort(500);
		}


		return Redirect::action('EqWaterController@index', array('region' => $input['node']))->with('message', '저장되었습니다.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        return View::make('eqwaters.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make('eqwaters.edit');
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
		$event = EqWaterEvent::find($id);
		$fileName = $event->attached_file_name;
		if ($fileName != '') {
			if (!unlink("./uploads/docs/".$fileName)) {
				return '첨부문서 삭제 중 오류가 발생했습니다';
			}
		}
		if (!$event->delete()) {
			return '살수차 사용내역 삭제 중 오류가 발생했습니다';
		}

		return '해당 사용내역이 삭제되었습니다.';
	}

}
