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
		$userNode = $user->supplyNode;
		$lowerNodes = $userNode->children;
		$data['nodes'] = $lowerNodes;
		$data['tabDept'] = Input::get('tab_dept');
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
		$data['isState'] = Input::get('is_state');
		$data['node'] = EqSupplyManagerNode::find($nodeId);
		$start = Input::get('start');
		$end = Input::get('end');
		$eventName = Input::get('event_name');
		$eventType = Input::get('event_type');
		$rows = array();

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

		$data['rows'] = $rows; 
		return View::make('equip.capsaicin-per-node',$data);
	}

}
