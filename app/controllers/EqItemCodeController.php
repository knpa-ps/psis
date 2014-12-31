<?php
use Carbon\Carbon;

class EqItemCodeController extends EquipController {

	public function holdingDetail($itemId) {

		$item = EqItem::find($itemId);
		$user = Sentry::getUser();

		$elements = array();

		if ($user->supplyNode->parent_id == null) {
			// 본청인 경우
			// 본청에서 취득한것
			$acquiredSet = EqItemAcquire::where('item_id','=',$itemId)->select('acquired_date as date', DB::raw('SUM(count) as income'))->groupby('acquired_date')->get();

			if($acquiredSet) {
				foreach ($acquiredSet as $a) {
					$obj = new stdClass();
					$obj->date = $a['date'];
					$obj->income = $a['income'];
					$obj->outgoings = 0;
					$obj->classification = '본청 구입';
					array_push($elements, $obj);
				}
			}
			// 각각을 모두 elements라는 array에 넣어준다.
		} else {
			// 본청 아닌 경우
			// 보급받은것
			$beSuppliedSet = EqItemSupplySet::where('item_id','=',$itemId)->where('from_node_id','=',$user->supplyNode->parent->id)->get();
			if($beSuppliedSet) {
				foreach ($beSuppliedSet as $s) {
					$obj = new stdClass();
					$obj->date = $s['supplied_date'];
					$obj->income = $s->children->sum('count');
					$obj->outgoings = 0;
					$obj->classification = EqSupplyManagerNode::find($s->from_node_id)->node_name.' 보급';
					array_push($elements, $obj);
				}
			}
		}
		
		//보급준것
		$suppliedSet = EqItemSupplySet::where('item_id','=', $itemId)->where('from_node_id','=',$user->supplyNode->id)->get();

		if($suppliedSet) {
			foreach ($suppliedSet as $s) {
				$obj = new stdClass();
				$obj->date = $s['supplied_date'];
				$obj->income = 0;
				$obj->outgoings = $s->children->sum('count');
				$obj->classification = EqSupplyManagerNode::find($s->from_node_id)->node_name.' 보급';
				array_push($elements, $obj);

			}
		}

		//관리전환받은것, 준것 convert
		$convertedSet = EqConvertSet::where('from_node_id','=',$user->supplyNode->id)->where('is_confirmed','=',1)->get();
		$beConvertedSet = EqConvertSet::where('target_node_id','=',$user->supplyNode->id)->where('is_confirmed','=',1)->get();

		if($convertedSet) {
			foreach ($convertedSet as $c) {
				$obj = new stdClass();
				$obj->date = $c['converted_date'];
				$obj->income = 0;
				$obj->outgoings = $c->children->sum('count');
				$obj->classification = EqSupplyManagerNode::find($c->from_node_id)->node_name.' 관리전환';
				array_push($elements, $obj);
			}
		}

		if($beConvertedSet) {
			foreach ($beConvertedSet as $c) {
				$obj = new stdClass();
				$obj->date = $c['converted_date'];
				$obj->income = $c->children->sum('count');
				$obj->outgoings = 0;
				$obj->classification = EqSupplyManagerNode::find($c->from_node_id)->node_name.' 관리전환';
				array_push($elements, $obj);
			}
		}
		// 폐기한것 discard
		$discardSets = EqItemDiscardSet::where('item_id','=',$itemId)->where('node_id','=',$user->supplyNode->id)->get();
		if ($discardSets) {
			foreach ($discardSets as $dSet) {
				$obj = new stdClass();
				$obj->date = $dSet['discarded_date'];
				$obj->income = 0;
				$obj->outgoings = $dSet->children->sum('count');
				switch ($dSet['category']) {
					case 'lost':
						$obj->classification = "폐기-분실";
						break;
					case 'wrecked':
						$obj->classification = "폐기-파손";
						break;
					case 'expired':
						$obj->classification = "폐기-불용";
						break;
					default:
						$obj->classification = "폐기";
						break;
				};
				array_push($elements, $obj);
			}
		}

		$data['elements'] = Paginator::make($elements, count($elements),15);
		$data['remaining'] = EqInventorySet::where('item_id','=',$itemId)->where('node_id','=',$user->supplyNode->id)->first()->children->sum('count');
		$data['itemId'] = $itemId;
		return View::make('equip.item-holding-detail', $data);

	}
	
	public function showRegisteredList($codeId) {

		$code = EqItemCode::find($codeId);
		$data['code'] = $code;
		$data['items'] = $code->items;

		return View::make('equip.items-registered-list', $data);

	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function deleteDetailFile($fileId){
		$file = EqItemDetailFile::find($fileId);
		if(!$file->delete()){
			return App::abort(400);
		}
		return "삭제되었습니다.";
	}
	public function deletePost($itemId, $id){
		$detail = EqItemDetail::find($id);
		if($detail->delete()){
			return array(
				'result'=>0,
				'message'=>'삭제되었습니다.',
				'url'=>url('equips/items/'.$itemId.'/details')
			);
		}
	}
	public function displayUpdatePostForm($itemId,$id){
		$detail = EqItemDetail::find($id);
		$data['creator_name'] = $detail->creator->user_name;
		$data['itemId'] = $itemId;
		$data['id'] = $id;
		$data['title'] = $detail->title;
		$data['content'] = $detail->content;
		$files = EqItemDetailFile::where('detail_id','=',$id)->get();
		$data['files'] = $files;

		return View::make('equip.item-detail-update', $data);
	}
	public function UpdatePost($itemId,$id){
		$input = Input::all();
		$files = json_decode($input['files']);
		$fileToDelete = json_decode($input['file_to_delete']);
		
		DB::beginTransaction();


		if(!count($fileToDelete) == 0){
			foreach ($fileToDelete as $d) {
				$file = EqItemDetailFile::find($d);
				if(!$file->delete()){
					return App::abort(400);
				}
			}
		}
	
		$detail = EqItemDetail::find($id);
		$detail->title = $input['title'];
		$detail->content = $input['input_body'];
		if(!$detail->update()){
			return App::abort(400);
		}

		if(!count($files) == 0){
			foreach ($files as $fileName) {
				$detailFile = new EqItemDetailFile;
				$detailFile->detail_id = $detail->id;
				$detailFile->file_name = $fileName;
				if(!$detailFile->save()){
					return App::abort(400);
				}
			}
		}

		DB::commit();
		Session::flash('message', '수정되었습니다');
		return Redirect::action('EqItemCodeController@displayExtraInfo', array('itemId'=>$itemId, 'id'=>$id));
	}

	public function displayExtraInfo($itemId,$id){
		$detail = EqItemDetail::find($id);
		$data = compact('detail');

		$files = EqItemDetailFile::where('detail_id','=',$id)->get();
		$data['files'] = $files;
		return View::make('equip.item-detail',$data);
	}

	public function displayDetailsList($itemId){
		$details = EqItemDetail::where('item_id','=',$itemId)->get();
		$data = compact('details');
		$data['itemId'] = $itemId;
		return View::make('equip.items-details-list',$data);
	}
	public function displayDetailForm($itemId){
		$user = Sentry::getUser();
		$data = compact('user');
		$data['itemId'] = $itemId;
		return View::make('equip.item-detail-new', $data);
	}

	public function doPost($itemId){
	
		$input = Input::all();
		$user = Sentry::getUser();
		$files = json_decode($input['files']);

		DB::beginTransaction();		

		$detail = new EqItemDetail;
		$detail->title = $input['title'];
		$detail->content = $input['input_body'];
		$detail->item_id = $itemId;
		$detail->creator_id = $user->id;

		if(!$detail->save()){
			return App::abort(400);
		}

		if($files){
			foreach ($files as $fileName) {
				$detailFile = new EqItemDetailFile;
				$detailFile->detail_id = $detail->id;
				$detailFile->file_name = $fileName;
				if(!$detailFile->save()){
					return App::abort(400);
				}
			}
		}

		DB::commit();

		Session::flash('message', '저장되었습니다.');
		return Redirect::action('EqItemCodeController@displayDetailsList', $itemId);
	}

	public function index()
	{
		$user = Sentry::getUser();

		$data['domains'] = $this->service->getVisibleDomains($user);

		if (count($data['domains']) == 0) {
			return App::abort(403);
		}

		$domainId = Input::get('domain');

		if (!$domainId) {
			$domainId = $data['domains'][0]->id;
		}

		if (!$user->hasAccess(EqDomain::find($domainId)->permission)) {
			return App::abort(403);
		}

		$data['user'] = $user;

		$data['itemCodes'] =  EqItemCode::whereHas('category', function($q) use ($domainId) {
									$q->where('domain_id', '=', $domainId);
								})->orderBy('code', 'asc')->orderBy('sort_order', 'asc')->get();

		$data['domainId'] = $domainId;
        return View::make('equip.item-codes', $data);
	}

	public function showDetail($itemCode, $itemId) {
		$user = Sentry::getUser();
		$item = EqItem::find($itemId);
		if ($item == null) {
			return App::abort(404);
		}
		$types = EqItemType::where('item_id','=',$itemId)->get();
		$data['domainId'] = $item->code->category->domain->id;
		$data['category'] = $item->code->category;
		$data['item'] = $item;
		$data['types'] = $types;
		$invSet = EqInventorySet::where('item_id','=',$itemId)->where('node_id','=',$user->supplyNode->id)->first();
		$data['inventorySet'] = $invSet;
		$modifiable = false; 
		$now = Carbon::now();
		$includingToday = EqQuantityCheckPeriod::where('check_end','>',$now)->where('check_start','<',$now)->get();
		if (sizeof($includingToday) !== 0) {
			$modifiable = true;
		}
		$data['modifiable'] = $modifiable;
		return View::make('equip.items-show', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$categories = EqCategory::all();
		return View::make('equip.item-code-add', get_defined_vars());
	}
	

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$code = new EqItemCode;
		$categoryId = $input['item_category'];
		$code->category_id = $categoryId;

		//code는 해당 카테고리의 마지막 번호에 1을 더한거.
		
		$lastCode = EqItemCode::where('category_id','=',$categoryId)->orderBy('code','desc')->first();
		$codeIndex = substr($lastCode->code, 1) + 1;
		if (strlen($codeIndex) <= 3)
		{
		    $codeIndex = '0'.$codeIndex;
		}
		$code->code = substr($lastCode->code, 0,1).$codeIndex;
		$code->title = $input['title'];
		$code->sort_order = $lastCode->sort_order + 1;

		if (!$code->save()) {
			return App::abort(500);
		}

		Session::flash('message', '추가되었습니다.');
		return Redirect::action('EqItemCodeController@index'); 
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($code)
	{
		$code = EqItemCode::where('code','=',$code)->first();
		$data['code'] = $code;
		$data['items'] = $code->items;

		return View::make('equip.items-registered-list', $data);
	}

	/**http://localhost/psis/equips/inventories
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 * 해당 장비의 is_active 플래그를 0으로 바꾸어 비활성화한다.
	 */
	public function destroy($id)
	{
		
	}

	public function getData($id) {
		$types = EqItemType::where('item_id','=',$id)->get();
		$validator = Validator::make(Input::all(), array(
				'parent'=>'integer|min:0'
			));

		if ($validator->fails()) {
			return App::abort(400);
		}
		
		$parentId = Input::get('parent');

		if (!$parentId || $parentId == 1) {
			
			$user = Sentry::getUser();
			$managingNode = $user->supplyNode;
			$nodes = EqSupplyManagerNode::where('parent_id','=',$managingNode->id)->get();

			// 총계
			$row = array(
				'node'=> (object) array(
					'node_name'=>'계', 
					'is_terminal'=>true, 
				));	

			$row['sum_row'] = 0;
			foreach ($types as $t) {
				$row[$t->type_name] = EqInventoryData::whereHas('parentSet', function($q) use ($managingNode) {
					$q->whereHas('ownerNode', function($qq) use ($managingNode) {
						$qq->where('full_path','like',$managingNode->full_path.':%');
					});
				})->where('item_type_id','=',$t->id)->sum('count');
				
				$row['sum_row'] += $row[$t->type_name];
			}
			$row['row_type']=0;
			$data[] = $row;

		} else {
			$parent = EqSupplyManagerNode::find($parentId);
			if (!$parent) {
				return App::abort(400);
			}
			$nodes = $parent->children()->get();
			
			$row = array(
						'node'=> (object) array(
							'id'=>$parent->parent_id, 
							'node_name'=>'상위부서로', 
							'is_terminal'=>false, 
							'parent_id'=>$parent->parent_id
						));	
			$row['sum_row'] = '';
			foreach ($types as $t) {
				$row[$t->type_name]='';
			}

			$row['row_type']=0;
			$data[] = $row;
			
			$row = array(
				'node'=> (object) array(
					'node_name'=>'계', 
					'is_terminal'=>true, 
				));	

			$row['sum_row'] = 0;

			foreach ($types as $t) {
				$row[$t->type_name] = EqInventoryData::whereHas('parentSet', function($q) use ($parentId) {
					$q->whereHas('ownerNode', function($qq) use ($parentId) {
						$qq->where('parent_id','=',$parentId);
					});
				})->where('item_type_id','=',$t->id)->sum('count');
				
				$row['sum_row'] += $row[$t->type_name];
			}
			
			$row['row_type']=0;
			$data[] = $row;
		}


		foreach ($nodes as $node) {
			

			$row['node'] = $node->toArray();
			$row['sum_row'] = 0;

			foreach ($types as $t) {

			$invData = EqInventoryData::whereHas('parentSet', function($q) use ($node) {
				$q->where('node_id','=',$node->id);
			})->where('item_type_id','=',$t->id)->first();

			if ($invData != null) {
				$row[$t->type_name] = $invData->count;
			} else {
				$row[$t->type_name] = 0;
			}

			$row['sum_row'] += $row[$t->type_name];

			if ($row[$t->type_name]==0) {
				$row[$t->type_name] = '';
			}

			if ($row['sum_row']==0) {
				$row['sum_row'] = '';
			}

			}
			$row['row_type'] = 1;
			$data[] = $row;

			// 파손수량 행 추가
			$row['node'] = (object) array(
							'id'=>'', 
							'node_name'=>'파손수량', 
							'is_terminal'=>true, 
							'parent_id'=>''
						);
			$row['sum_row'] = 0;

			foreach ($types as $t) {

			$invData = EqInventoryData::whereHas('parentSet', function($q) use ($node) {
				$q->where('node_id','=',$node->id);
			})->where('item_type_id','=',$t->id)->first();

			if ($invData != null) {
				$row[$t->type_name] = $invData->wrecked;
			} else {
				$row[$t->type_name] = 0;
			}

			$row['sum_row'] += $row[$t->type_name];

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
}
