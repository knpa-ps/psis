 <?php

class EqConvertController extends EquipController {

	public function crossHeadIndex() {

		$user = Sentry::getUser();
		$start = Input::get('start');
		$end = Input::get('end');

		$data['user'] = $user;

		//날짜 범위 지정 관련
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
		$query = EqConvertSet::where('converted_date', '>=', $start)->where('converted_date', '<=', $end)->orderBy('converted_date','DESC');;

		//날짜 지정 관련 끝
		//확인여부
		$checked = Input::get('checked');
		if ($checked=="waiting") {
			$query->where('cross_head','=',1)->where('head_confirmed','=',0);
		} elseif ($checked=="checked") {
			$query->where('is_confirmed','=',1);
		} elseif ($checked=="unchecked") {
			$query->where('is_confirmed','=',0);
		}
		$data['checked'] = $checked;

		//관서명
		$deptName = Input::get('dept_name');
		if($deptName) {
			if (substr($deptName,0,1)=='@') {
				$query->where(function($query) use($deptName) {
					$query->whereHas('fromNode', function($q) use($deptName) {
						$q->where('full_name','like',substr($deptName,1));
					})->orWhereHas('targetNode', function($q) use($deptName) {
						$q->where('full_name','like',substr($deptName,1));
					});
				});
			} else {
				$query->where(function($query) use($deptName) {
					$query->whereHas('fromNode', function($q) use($deptName) {
						$q->where('full_name','like',"%$deptName%");
					})->orWhereHas('targetNode', function($q) use($deptName) {
						$q->where('full_name','like',"%$deptName%");
					});
				});
			}
		}
		$data['deptName'] = $deptName;
		//장비명
		$itemName = Input::get('item_name');
		if ($itemName) {
			$query->whereHas('item', function($q) use($itemName) {
				$q->whereHas('code', function($qry) use($itemName) {
					$qry->where('title','like',"%$itemName%");
				});
			});
		}
		$data['itemName'] = $itemName;

		//청간 전환인 converts 불러오기
		$converts = $query->where('cross_head','=',1)->get();
		$rows = array();
		foreach($converts as $convert) {
			$row = new stdClass;
			$row->id = $convert->id;
			$row->item_id = $convert->item_id;
			$row->from_node_id = $convert->from_node_id;
			$row->target_node_id = $convert->target_node_id;
			$row->converted_date = $convert->converted_date;
			$row->explanation = $convert->explanation;

			$row->is_confirmed = $convert->is_confirmed;
			$row->confirmed_date = $convert->confirmed_date;
			$row->cross_head = $convert->cross_head;
			$row->head_confirmed = $convert->head_confirmed;

			$row->classification = $convert->item->classification;
			$row->maker_name = $convert->item->maker_name;
			$row->from_node_name = $convert->fromNode->full_name;
			$row->target_node_name = $convert->targetNode->full_name;
			$row->title = $convert->item->code->title;
			$row->acquired_date = $convert->item->acquired_date;
			$row->count_sum = $convert->children->sum('count');

			array_push($rows, $row);
		}
		$currentPage = Input::get('page')== null ? 0 : Input::get('page') - 1;
		$pagedRows = array_slice($rows, $currentPage * 20, 20);
		$data['converts'] = Paginator::make($pagedRows,count($rows),20);

		return View::make('equip.convert-crosshead',$data);
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
		$isImport = Input::get('is_import');

		$data['user'] = $user;
		$data['userNodeName'] = explode(' ', $user->supplyNode->full_name);

		if (!$isImport) {
			$data['isImport'] = true;
		} else {
			if ($isImport=='true') {
				$data['isImport'] = true;
			} else {
				$data['isImport'] = false;
			}
		}

		//필터

		//날짜 범위 지정 관련
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

		$query = EqConvertSet::where('converted_date', '>=', $start)->where('converted_date', '<=', $end)->orderBy('converted_date','DESC');

		//날짜 지정 관련 끝
		
		//장비명
		$itemName = Input::get('item_name');
		if ($itemName) {
			$query->whereHas('item', function($q) use($itemName) {
				$q->whereHas('code', function($qq) use($itemName) {
					$qq->where('title','like',"%$itemName%");
				});
			});
		} else {
			$itemName='';
		}
		$data['itemName'] = $itemName;

		//확인여부
		$checked = Input::get('checked');
		if ($checked=="waiting") {
			$query->where('cross_head','=',1)->where('head_confirmed','=',0);
		} elseif ($checked=="checked") {
			$query->where('is_confirmed','=',1);
		} elseif ($checked=="unchecked") {
			$query->where('is_confirmed','=',0);
		}
		$data['checked'] = $checked;

		//관서명
		$deptName = Input::get('dept_name');
		if($deptName) {
			if (substr($deptName,0,1)=='@') {
				$query->where(function($query) use($deptName) {
					$query->whereHas('fromNode', function($q) use($deptName) {
						$q->where('full_name','like',substr($deptName,1));
					})->orWhereHas('targetNode', function($q) use($deptName) {
						$q->where('full_name','like',substr($deptName,1));
					});
				});
			} else {
				$query->where(function($query) use($deptName) {
					$query->whereHas('fromNode', function($q) use($deptName) {
						$q->where('full_name','like',"%$deptName%");
					})->orWhereHas('targetNode', function($q) use($deptName) {
						$q->where('full_name','like',"%$deptName%");
					});
				});
			}
		}
		$data['deptName'] = $deptName;

		//converts 불러오기
		if ($data['isImport'] == true) {
			// 입고내역 조회
			$converts = $query->whereHas('childOfTargetNode', function($q) use ($user){
				$q->where('full_path','like',$user->supplyNode->full_path.'%')->where('is_selectable','=',1);
			})->get();
		} else {
			// 출고내역 조회
			$converts = $query->whereHas('childOfFromNode', function($q) use ($user){
				$q->where('full_path','like',$user->supplyNode->full_path.'%')->where('is_selectable','=',1);
			})->get();
		}
		$rows = array();

		foreach($converts as $convert) {
			$row = new stdClass;
			$row->id = $convert->id;
			$row->item_id = $convert->item_id;
			$row->from_node_id = $convert->from_node_id;
			$row->target_node_id = $convert->target_node_id;
			$row->converted_date = $convert->converted_date;
			$row->explanation = $convert->explanation;

			$row->is_confirmed = $convert->is_confirmed;
			$row->confirmed_date = $convert->confirmed_date;
			$row->cross_head = $convert->cross_head;
			$row->head_confirmed = $convert->head_confirmed;

			$row->classification = $convert->item->classification;
			$row->maker_name = $convert->item->maker_name;
			$row->from_node_name = $convert->fromNode->full_name;
			$row->target_node_name = $convert->targetNode->full_name;
			$row->title = $convert->item->code->title;
			$row->acquired_date = $convert->item->acquired_date;
			$row->count_sum = $convert->children->sum('count');

			array_push($rows, $row);
		}
		$currentPage = Input::get('page')== null ? 0 : Input::get('page') - 1;
		$pagedRows = array_slice($rows, $currentPage * 20, 20);
		$data['converts'] = Paginator::make($pagedRows,count($rows),20);

		// 보유장비 목록 보여주기
		$categories = EqCategory::orderBy('sort_order')->get();
		$data['categories'] = $categories;
		foreach ($categories as $category) {
			foreach ($category->codes as $c) {
				$data['items'][$c->id] = EqItem::where('item_code','=',$c->code)->where('is_active','=',1)->whereHas('inventories', function($q) use ($user) {
					$q->where('node_id','=',$user->supplyNode->id);
				})->orderBy('acquired_date','DESC')->get();
			}
		}

		return View::make('equip.convert-index', $data);
	}
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$itemId = Input::get('item');
		$data['item'] = EqItem::find($itemId);
		$user = Sentry::getUser();
		$item = EqItem::find($itemId);
		$itemTypes = $item->types;

		$inventorySet = EqInventorySet::where('node_id','=',$user->supplyNode->id)->where('item_id','=',$item->id)->first();

		foreach ($itemTypes as $t) {
			$query = EqInventoryData::where('inventory_set_id','=',$inventorySet->id);
			$holding = $query->where('item_type_id','=',$t->id)->first()->count;
			$data['holding'][$t->id] = $holding;
		}

		return View::make('equip.convert-create',$data);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validator = Validator::make(
			$input, array(
				'supply_node_id' => 'required'
			)
		);

		if ($validator->fails()) {
			return Redirect::back()->with('message', '대상관서를 반드시 선택해야 합니다');
		}

		if (array_sum($input['type_counts'])==0) {
			return Redirect::back()->with('message', '관리전환 수량이 입력되지 않았습니다.');
		}

		$user = Sentry::getUser();
		$userNode = $user->supplyNode;
		$item = EqItem::find($input['item_id']);
		$itemTypes = $item->types;

		// 입력한 숫자가 정수인지 검사한다.
		$inputCounts = Input::get('type_counts');
		foreach ($inputCounts as $c) {

			if ($c === '') {
				$c = 0;
			}

			if (!is_numeric($c) || $c < 0) {
				return Redirect::back()->with('message', '수량에는 양의 정수를 입력해야 합니다.');
			}
		}

		// 보유수량이 충분한지 검사하는 로직

		$inventorySet = EqInventorySet::where('node_id','=',$userNode->id)->where('item_id','=',$item->id)->first();

		foreach ($itemTypes as $t) {
			$query = EqInventoryData::where('inventory_set_id','=',$inventorySet->id);
			$holding = $query->where('item_type_id','=',$t->id)->first()->count;

			if ($holding < $input['type_counts'][$t->id]) {
				return Redirect::back()->with('message','보유수량이 부족합니다. '.$t->type_name.' 보유수량 : '.$holding);
			}
		}

		// 보유수량 검사 끝

		DB::beginTransaction();

		$convSet = new EqConvertSet;
		$convSet->item_id = $input['item_id'];
		$convSet->from_node_id = $user->supplyNode->id;
		$convSet->target_node_id = $input['supply_node_id'];
		$convSet->converted_date = $input['converted_date'];
		$convSet->explanation = $input['explanation'];
		$convSet->is_confirmed = 0;

		// 타 청으로 보내는 경우 본청 승인을 받아야 하므로 이를 검사한다.
		$targetNode = EqSupplyManagerNode::find($input['supply_node_id']);

		$targetHeadId = explode(":", $targetNode->full_path)[2];
		$myHeadId = explode(":", $userNode->full_path)[2];

		// 유저가 본청이면 이 부분을 그냥 넘어간다.
		if ($myHeadId) {
			// 유저가 본청이 아닐 경우 관리전환 대상 부서가 타청인지 검사
			$convSet->cross_head = ($targetHeadId === $myHeadId) ? false : true;
		}

		$convSet->head_confirmed = false;

		if (!$convSet->save()) {
			return App::abort(500);
		}

		foreach ($itemTypes as $t) {
			$convData = new EqConvertData;
			$convData->convert_set_id = $convSet->id;
			$convData->item_type_id = $t->id;
			$convData->count = $input['type_counts'][$t->id];

			if (!$convData->save()) {
				return App::abort(500);
			}
		}

		DB::commit();

		return Redirect::action('EqConvertController@index', 'is_import=false')->with('message','관리전환이 등록되었습니다.');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$convSet = EqConvertSet::find($id);
		$user = Sentry::getUser();
		$isImport = $convSet->target_node_id == Sentry::getUser()->supplyNode->id;
		$item = EqItem::find($convSet->item_id);
		$types = $item->types;

		foreach ($types as $t) {
			$convData[$t->id] = EqConvertData::where('convert_set_id','=',$id)->where('item_type_id','=',$t->id)->first()->count;
		}

		$sum = $convSet->children->sum('count');

		return View::make('equip.convert-show', get_defined_vars());
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
	public function destroy($id) {
		$result = $this->service->deleteConvertSet($id);

		if ($result === 1) {
			Session::flash('message', '관리전환이 취소되었습니다.');
			return Redirect::back();
		} else {
			Session::flash('message', '관리전환 취소 중 오류가 발생했습니다.');
			return Redirect::back();
		}
	}

	public function headConfirm($id) {
		$convSet = EqConvertSet::find($id);
		$convSet->head_confirmed = 1;
		if (!$convSet->save()) {
			return App::abort(500);
		}
		return Redirect::back()->with('message', '청간 관리전환이 승인되었습니다.');
	}

	public function convertConfirm($id) {

		$input = Input::all();
		$convSet = EqConvertSet::find($id);
		$user = Sentry::getUser();

		$fromNode = EqSupplyManagerNode::find($convSet->from_node_id);

		$item = EqItem::find($input['item_id']);
		$types = $item->types;


		// 1. 받는 쪽 인벤토리에 갯수 추가
		// 2. 주는 쪽 인벤토리에서 갯수 빼기
		// 3. 관리전환set의 is_confirmed 플래그 1로 변경
		// 하면 관리전환 확인이 완료된다.

		DB::beginTransaction();

		$myInvSet = EqInventorySet::where('node_id','=',$user->supplyNode->id)->where('item_id','=',$item->id)->first();
		// 관리전환 받는 관서에서 기존 보유전적이 없는 경우 인벤토리를 만들어준다.

		if (!$myInvSet) {
			$myInvSet = new EqInventorySet;
			$myInvSet->item_id = $item->id;
			$myInvSet->node_id = $user->supplyNode->id;

			if (!$myInvSet->save()) {
				return App::abort(500);
			}
			// 캐시도 만들어준다
			try {
				$this->service->makeCache($user->supplyNode->id);
			} catch (Exception $e) {
				return Redirect::to('equips/convert')->with('message', $e->getMessage() );
			}

			// inventory 생성 후 Data에 받은 수량 추가한다
			foreach ($types as $t) {
				$stocked = EqConvertData::where('convert_set_id','=',$convSet->id)->where('item_type_id','=',$t->id)->first()->count;
				//inventoryData도 없으니 만들어줘야 함
				$myInvData = new EqInventoryData;
				$myInvData->inventory_set_id = $myInvSet->id;
				$myInvData->item_type_id = $t->id;
				//초기 수량이 0이므로 그냥 갯수 받은대로 넣어주면 된다.
				try {
					$this->service->inventorySupply($myInvData, $stocked);
				} catch (Exception $e) {
					return Redirect::back()->with('message', $e->getMessage() );
				}
			}
		} else {
			// 기존 보유전적이 있는 경우 갯수를 더해준다.
			$stockedSum=0;
			foreach ($types as $t) {
				$myInvData = EqInventoryData::where('inventory_set_id','=',$myInvSet->id)->where('item_type_id','=',$t->id)->first();
				$stocked = EqConvertData::where('convert_set_id','=',$convSet->id)->where('item_type_id','=',$t->id)->first()->count;

				// 인벤토리에서 빼고 저장하는 함수
				try {
					$this->service->inventorySupply($myInvData, $stocked);
				} catch (Exception $e) {
					return Redirect::back()->with('message', $e->getMessage() );
				}
			}
		}
		// 받는 쪽 인벤토리 정리 끝
		// 주는 쪽 시작
		$fromInvSet = EqInventorySet::where('node_id','=',$fromNode->id)->where('item_id','=',$item->id)->first();
		// 주는 쪽은 인벤토리가 이미 있으므로 없는 경우를 생각할 필요가 없음.
		foreach ($types as $t) {
			$fromInvData = EqInventoryData::where('inventory_set_id','=',$fromInvSet->id)->where('item_type_id','=',$t->id)->first();
			$subValue = EqConvertData::where('convert_set_id','=',$convSet->id)->where('item_type_id','=',$t->id)->first()->count;
			// 인벤토리에서 빼고 저장하는 함수
			try {
				$this->service->inventoryWithdraw($fromInvData, $subValue);
			} catch (Exception $e) {
				return Redirect::back()->with('message', $e->getMessage() );
			}
		}
		//주는 쪽 끝
		//컨펌 플래그 변경
		$convSet->is_confirmed = 1;
		$convSet->confirmed_date = date("Y-m-d");
		if (!$convSet->save()) {
			return App::abort(500);
		}

		DB::commit();

		return Redirect::back()->with('message','관리전환이 확인되었습니다.');
	}

}
