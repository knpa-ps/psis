<?php

class EqSurveyController extends \BaseController {

	public function getData($id){

		$survey = EqItemSurvey::find($id);

		$types = EqItemType::where('item_id','=',$survey->item->id)->get();

		$user = Sentry::getUser();
		$userNode = $user->supplyNode;
		$childrenNodes = $userNode->children;

		$row = array(
			'node'=> (object) array(
				'node_name'=>'계',
			));	

		$row['sum_row'] = EqItemSurveyData::where('survey_id','=',$id)->sum('count');
		foreach ($types as $t) {
			$row[$t->type_name]= 0;
		}
		$row['row_type']=0;
		$data[] = $row;

		foreach ($childrenNodes as $node) {
			
			$row['node'] = $node->toArray();
			$row['sum_row'] = EqItemSurveyData::where('survey_id','=',$id)->where('target_node_id','=',$node->id)->first()->count;

			foreach ($types as $t) {

			$surveyRes = EqItemSurveyResponse::where('survey_id','=',$id)->where('item_type_id','=',$t->id)->first();
			if (isset($surveyRes)) {
				$row[$t->type_name] = $surveyRes->count;
			}

			if ($row[$t->type_name]==0) {
				$row[$t->type_name] = '';
			}

			if ($row['sum_row']==0) {
				$row['sum_row'] = '';
			}

			}
			$row['row_type'] = 1;
			$data[] = $row;
		}

		return array('data'=>$data);		
	}

	public function newSurvey(){
		$item = EqItem::find(Input::get('item'));
		$user = Sentry::getUser();
		$mode = 'create';
		$childrenNodes = EqSupplyManagerNode::where('parent_id','=',$user->supplyNode->id)->get();
		return View::make('equip.survey-new', get_defined_vars());
	}

	public function storeNewSurvey() {
		$input = Input::all();
		$user = Sentry::getUser();
		$nodes = $user->supplyNode->children;

		DB::beginTransaction();
			$survey = new EqItemSurvey;
			$survey->item_id = $input['item_id'];
			$survey->creator_id = $user->id;
			$survey->node_id = $user->supplyNode->id;
			$survey->started_at = date('Y-m-d');
			$survey->expired_at = date('Y-m-d', strtotime('+2 month'));
			if (!$survey->save()) {
				return App::abort(400);
			}

			foreach ($nodes as $n) {
				$data = new EqItemSurveyData;
				$data->survey_id = $survey->id;
				$data->target_node_id = $n->id;
				$data->count = $input['count_'.$n->id];

				if (!$data->save()) {
					return App::abort(400);
				}
			}

		DB::commit();

		Session::flash('message', '저장되었습니다.');

		return Redirect::to('equips/surveys');
	}

	public function updateSurvey($id) {

	}

	public function deleteSurvey($id) {

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Sentry::getUser();
		$start = Input::get('start');
		$end = Input::get('end');

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

		$itemName = Input::get('item_name');

		$query = EqItemSurvey::where('started_at', '>=', $start);

		if ($itemName) {
			$query->whereHas('item', function($q) use ($itemName) {
				$q->where('name', 'like', "%$itemName%");
			});
		}

		$surveys = $query->paginate(15);

		$responsesQuery = EqItemSurveyResponse::where('node_id','=',$user->supplyNode->id);

		$items = EqItem::where('is_active','=',1)->get();

        return View::make('equip.survey-index', get_defined_vars());
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$itemId = Input::get('item');
		$user = Sentry::getUser();
		$userNode = $user->supplyNode;
		$data = array();

		$types = EqItemType::where('item_id','=',$itemId)->get();
		
		$data['types'] = $types;
		$data['mode'] = 'create';
		$data['item'] = EqItem::find($itemId);
		$data['userNode'] = $userNode;
		$data['lowerNodes'] = EqSupplyManagerNode::where('parent_id','=',$userNode->id)->get();
		
        return View::make('equip.supplies-create',$data);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$survey = EqItemSurvey::find($id);

		$item = $survey->item;
		if ($item == null) {
			return App::abort(404);
		}
		$types = EqItemType::where('item_id','=',$survey->item_id)->get();
		$data['survey'] = $survey;
		$data['item'] = $item;
		$data['types'] = $types;
		return View::make('equip.survey-show', $data);
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


}
