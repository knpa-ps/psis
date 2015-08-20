<?php

class EqPavaIOController extends EquipController {

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
		$sort = Input::get('sort');
		$data['sort'] = $sort;
		$selectedRegionId = Input::get('region');
		$data['region'] = $selectedRegionId;

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
		$query = EqPavaIO::where('date', '>=', $start)->where('date', '<=', $end);
		//행사명 필터 걸었다
		if ($eventName) {
			$query->where('event_name','like',"%$eventName%");
		}

		if ($sort) {
			$query->where('sort','=',$sort);
		}

		// 본청계정인 경우 지방청필터
		if ($node->type_code == "D001") {
			if ($selectedRegionId) {
				$query->where('node_id','=',$selectedRegionId);			
			}
			$data['regions'] = EqSupplyManagerNode::where('type_code','=',"D002")->get();
		}
		$events = $query->orderBy('date','DESC')->paginate(15);

		$data['events'] = $events;

		// if (Input::get('export')) {
		// 	$this->service->exportCapsaicinByEvent($rows, EqSupplyManagerNode::find($regionId), $now);
		// 	return;
		// }
		
		if ($node->type_code == "D001") {
			return View::make('equip.pava.index-head', $data);
		}

        return View::make('equip.pava.index', $data);
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
        return View::make('equip.pava.create',get_defined_vars());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input=Input::all();

		$event = new EqPavaIO;
		$event->event_name = $input['event_name'];
		$event->node_id = $input['node_id'];
		$event->date = $input['date'];
		$event->sort = $input['sort'];
		$event->amount = $input['amount'];

		if (!$event->save()) {
			return App::abort(500);
		}

		return Redirect::action('EqPavaIOController@index')->with('message', '저장되었습니다.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        return View::make('equip.pava.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make('equip.pava.edit');
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
		$event = EqPavaIO::find($id);

		if (!$event->delete()) {
			return '소비내역 삭제 도중 오류가 발생했습니다.';
		}
		
		return '해당 소비내역이 삭제되었습니다.'; 
	}

}
