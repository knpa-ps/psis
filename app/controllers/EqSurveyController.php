<?php

class EqSurveyController extends \BaseController {

	public function updateResponse($id) {
		$survey = EqItemSurvey::find($id);
		$user = Sentry::getUser();
		$node = $user->supplyNode;
		$types = $survey->item->types;

		foreach ($types as $t) {
			$response = EqItemSurveyResponse::where('survey_id','=',$id)->where('node_id','=',$node->id)->where('item_type_id','=',$t->id)->first();
			$response->count = Input::get('count_'.$t->id);
			$response->update();
		}

		Session::flash('message', '수정되었습니다.');

		return Redirect::to('equips/surveys');
	}

	public function storeResponse($id){
		$survey = EqItemSurvey::find($id);
		$user = Sentry::getUser();
		$node = $user->supplyNode;
		$types = $survey->item->types;

		foreach ($types as $t) {
			$response = new EqItemSurveyResponse;
			$response->survey_id = $survey->id;
			$response->node_id = $node->id;
			$response->creator_id = $user->id;
			$response->item_type_id = $t->id;
			$response->count = Input::get('count_'.$t->id);

			if (!$response->save()) {
				return App::abort('400');
			}
		}

		Session::flash('message', '저장되었습니다.');

		return Redirect::to('equips/surveys');
	}

	public function doResponse($id){
		$survey = EqItemSurvey::find($id);
		$user = Sentry::getUser();
		$item = $survey->item;
		$types = $item->types;

		if ($survey->isResponsed($user->supplyNode->id)==0) {
			$mode = 'create';
		} else {
			$mode = 'update';
			foreach ($types as $t) {
				$count[$t->id] = EqItemSurveyResponse::where('survey_id','=',$id)->where('item_type_id','=',$t->id)->first()->count;
			}
		}
		$sum = EqItemSurveyData::where('survey_id','=',$survey->id)->where('target_node_id','=',$user->supplyNode->id)->first()->count;

		return View::make('equip.survey-response',get_defined_vars());
	}

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
			$row[$t->type_name]= EqItemSurveyResponse::where('survey_id','=',$id)->where('item_type_id','=',$t->id)->sum('count');
		}
		$row['row_type']=0;
		$data[] = $row;

		foreach ($childrenNodes as $node) {
			
			$row['node'] = $node->toArray();
			$row['sum_row'] = EqItemSurveyData::where('survey_id','=',$id)->where('target_node_id','=',$node->id)->first()->count;

			foreach ($types as $t) {

			$surveyRes = EqItemSurveyResponse::where('survey_id','=',$id)->where('node_id','=',$node->id)->where('item_type_id','=',$t->id)->first();
			if (isset($surveyRes)) {
				$row[$t->type_name] = $surveyRes->count;
			} else {
				$row[$t->type_name] = 0;
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

		// domainId == 0이면 조사응답 탭
		// domainId == 1이면 조사하기 탭
		$domain = Input::get('domain');

		if (!$domain) {
			$domain = 0;
		}

		//조사하기 탭일 땐 자신이 등록한 설문조사 목록을 출력
		if ($domain == 1) {

			$query = EqItemSurvey::where('node_id','=',$user->supplyNode->id)->where('started_at', '>=', $start)->where('is_closed','=',0);
			$items = EqItem::where('is_active','=',1)->get();

		//조사응답 탭일 땐 자신이 응답해야 하는 설문조사 목록을 출력한다.
		} else {

			$query = EqItemSurvey::where('node_id','=',$user->supplyNode->managedParent)->where('started_at', '>=', $start)->where('is_closed','=',0);
		}

		// 장비명 필터 걸기
		if ($itemName) {
			$query->whereHas('item', function($q) use($itemName) {
				$q->whereHas('code', function($qry) use($itemName) {
					$qry->where('title','like',"%$itemName%");
				});
			});
		}

		$surveys = $query->paginate(15);

        return View::make('equip.survey-index', get_defined_vars());
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$item = EqItem::find(Input::get('item'));
		$user = Sentry::getUser();
		$mode = 'create';
		$childrenNodes = EqSupplyManagerNode::where('parent_manager_node','=',$user->supplyNode->id)->get();
		return View::make('equip.survey-new', get_defined_vars());
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$user = Sentry::getUser();
		$nodes = $user->supplyNode->children;

		DB::beginTransaction();
			$survey = new EqItemSurvey;
			$survey->item_id = $input['item_id'];
			$survey->creator_id = $user->id;
			$survey->node_id = $user->supplyNode->id;
			$survey->started_at = date('Y-m-d');
			$survey->expired_at = date('Y-m-d', strtotime('+1 week'));
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

		return Redirect::to('equips/surveys?domain=2');
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
		$user = Sentry::getUser();
		$userNode = $user->supplyNode;
		$survey = EqItemSurvey::find($id);
		$item = $survey->item;
		$childrenNodes = $userNode->children;
		$mode = 'update';

		foreach ($childrenNodes as $n) {
			$count[$n->id] = EqItemSurveyData::where('survey_id','=',$id)->where('target_node_id','=',$n->id)->first()->count;
		}
			
        return View::make('equip.survey-new',get_defined_vars());
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		$user = Sentry::getUser();
		$nodes = $user->supplyNode->children;

		DB::beginTransaction();
			foreach ($nodes as $n) {
				$data = EqItemSurveyData::where('survey_id','=',$id)->where('target_node_id','=',$n->id)->first();
				$data->count = $input['count_'.$n->id];

				$data->update();
			}

		DB::commit();

		Session::flash('message', '수정되었습니다.');

		return Redirect::to('equips/surveys?domain=1');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$survey = EqItemSurvey::find($id);
		$survey->is_closed = 1;
		$survey->update();

		Session::flash('message', '삭제되었습니다.');

		return Redirect::to('equips/surveys?domain=1');
	}


}
