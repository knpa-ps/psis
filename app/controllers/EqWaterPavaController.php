<?php
use Carbon\Carbon;

class EqWaterPavaController extends EquipController {

	public function pavaPerMonth()
	{
		$user = Sentry::getuser();
		$nodeId = $user->supplyNode->id;
		$year = Input::get('year');

		$now = Carbon::now();
		if ($year == null) {
			$year = $now->year;
		}
		$data['node']=$user->supplyNode;
		$data['year'] = $year;
		$data['nowYear'] = $now->year;
		$data['initYears'] = EqPavaInitHolding::select('year')->distinct()->get();

		$yearInitHolding = EqPavaInitHolding::where('year','=',$year)->where('node_id','=',$nodeId)->first()->amount;
		$data['yearInitHolding'] = $yearInitHolding;

		$events = EqWaterPavaEvent::whereNotNull('pava_amount')->where('node_id','=',$nodeId)->where('date','like',$year.'%')->get();
		$trainings = EqPavaIO::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('sort','=','training')->get();

		$stock = array();
		$usageSum = array();
		$usageT = array();
		$usageA = array();
		$timesSum = array();
		$timesT = array();
		$timesA = array();
		$lost = array();

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

			$eventsUntilithMonth = EqWaterPavaEvent::whereNotNull('pava_amount')->where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('date','<=',$lastDayofMonth)->get();
			$trainingsUntilithMonth = EqPavaIO::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('date','<=',$lastDayofMonth)->where('sort','=','training')->get();
			$consumptionUntilithMonth = $eventsUntilithMonth->sum('pava_amount') + $trainingsUntilithMonth->sum('amount');

			$lostUntilithMonth = EqPavaIO::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('date','<=',$lastDayofMonth)->where('sort','=','lost')->sum('amount');
			$stock[$i] = $yearInitHolding - $consumptionUntilithMonth - $lostUntilithMonth;

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
				$lost[$i] = null;
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
				$lost[$i] = null;
				continue;
			}

			$eventsThisMonth = EqWaterPavaEvent::whereNotNull('pava_amount')->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->get();
			$trainingsThisMonth = EqPavaIO::where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->where('sort','=','training')->get();
			$lostThisMonth = EqPavaIO::where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->where('sort','=','lost')->get();

			
			$usageT[$i] = $trainingsThisMonth->sum('amount');
			$usageA[$i] = $eventsThisMonth->sum('pava_amount');
			$usageSum[$i] = $usageT[$i] + $usageA[$i];

			$timesSum[$i] = $eventsThisMonth->count() + $trainingsThisMonth->count();
			$timesT[$i] = $trainingsThisMonth->count();
			$timesA[$i] = $eventsThisMonth->count();
			$lost[$i]  = $lostThisMonth->sum('amount');
		}
		$data['stockSum'] = array_sum($stock);
 		$data['usageSumSum'] = array_sum($usageSum);
 		$data['usageTSum'] = array_sum($usageT);
 		$data['usageASum'] = array_sum($usageA);
 		$data['timesSumSum'] = array_sum($timesSum);
 		$data['timesTSum'] = array_sum($timesT);
 		$data['timesASum'] = array_sum($timesA);
 		$data['lostSum'] = array_sum($lost);

		$data['stock'] = $stock;
		$data['usageSum'] = $usageSum;
		$data['usageT'] = $usageT;
		$data['usageA'] = $usageA;
		$data['timesSum'] = $timesSum;
		$data['timesT'] = $timesT;
		$data['timesA'] = $timesA;
		$data['lost'] = $lost;

		return View::make('equip.waterpava.pava-per-month', $data);
	}

	public function getConsumptionPerMonth()
	{
		$regionId = Input::get('regionId');
		$year = Input::get('year');

		//월별 경고, 직사, 곡사, 사용횟수 합 구하기
		$warnPerMonth = array();
		$directPerMonth = array();
		$highAnglePerMonth = array();
		$sumPerMonth = array();

		$countPerMonth = array();

		for ($i=1; $i <= 12; $i++) {

			$firstDayofMonth = Carbon::createFromDate($year, $i, 1, 'Asia/Seoul')->subDay();
			if ($i != 12) {
				$lastDayofMonth = Carbon::createFromDate($year, $i+1, 1, 'Asia/Seoul')->subDay();
			} else {
				$lastDayofMonth = Carbon::createFromDate($year, $i, 31, 'Asia/Seoul');
			}

			$events = EqWaterPavaEvent::where('node_id','=',$regionId)->where('date','>=',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->get();
			$warn = $events->sum('warn_amount');
			$direct = $events->sum('direct_amount');
			$highAngle = $events->sum('high_angle_amount');
			$sum = $warn+$direct+$highAngle;

			$count = $events->count();

			$warnPerMonth[] = round($warn,2);
			$directPerMonth[] = round($direct,2);
			$highAnglePerMonth[] = round($highAngle,2);
			$sumPerMonth[] = round($sum,2);
			$countPerMonth[] = $count;

		}

		$regionName = EqSupplyManagerNode::find($regionId)->node_name;

		$data[] = $warnPerMonth;
		$data[] = $directPerMonth;
		$data[] = $highAnglePerMonth;
		$data[] = $sumPerMonth;
		$data[] = $countPerMonth;
		$data[] = $regionName;

		return $data;
	}

	public function waterPerMonth()
	{
		$user = Sentry::getUser();
		$nowYear = Carbon::now()->year;

		$selectedYear = Input::get('year') ? Input::get('year') : $nowYear;

		$oldest = EqWaterPavaEvent::orderBy('date','ASC')->first();
		if ($oldest) {
			$oldestDate=$oldest->date;
			$initYear = substr($oldestDate, 0, 4);
		} else {
			$initYear = $nowYear;
		}
		if ($user->supplyNode->type_code == "D002") {
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
		$node = $user->supplyNode;
		$data['node'] = $node;
		$start = Input::get('start');
		$end = Input::get('end');
		$eventName = Input::get('event_name');
		$data['eventName'] = $eventName;

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
		$node = $user->supplyNode;
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
		$event->warn_amount = $input['warn'];
		$event->direct_amount = $input['direct'];
		$event->high_angle_amount = $input['high_angle'];

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
