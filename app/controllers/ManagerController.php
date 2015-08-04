<?php

class ManagerController extends \BaseController {

	public function __construct() {
		$this->service = new ManagerService;
	}

	public function changeNodeManager() {
		$userId = Input::get('userId');
		$nodeId = Input::get('nodeId');
		$user = User::find($userId);
		if(!$userId){
			$msg = "관리자로 지정할 사용자를 선택하세요.";
			$code = 0; 
			return array('msg'=>$msg, 'code'=>$code);
		}
		$capsaicinGroup = Group::where('key','=','equip.capsaicin')->first();
		$psGroup = Group::where('key','=','equip.ps.admin')->first();
		// 유저가 타 관서 장비관리자였을 경우 원래 관서의 관리자 없애기
		$prevNode = EqSupplyManagerNode::where('manager_id','=',$userId[0])->first();
		if ($prevNode) {
			$prevNode->manager_id = null;
			if (!$prevNode->save()) {
				return App::abort(500);
			}
		}
		// 유저가 타 관서 장비관리자였을 경우 일단 관리자그룹에서 제거
		$havingEqGroups = Group::whereHas('users', function($q) use($userId) {
			$q->where('id','=',$userId[0]);
		})->get();
		foreach ($havingEqGroups as $g) {
			if (explode('.', $g->key)[0] == 'equip') {
				$g->users()->detach($userId[0]);
			}
		};
		// 선택한 노드의 관리자로 임명함
		$node = EqSupplyManagerNode::find($nodeId);
		if ($node->manager_id !== '') {
			$predecessorId = $node->manager_id;
		}
		$node->manager_id = $userId[0];
		$node->last_manager_changed_date = date('Y-m-d H:i:s');
		DB::beginTransaction();
		if (!$node->save()) {
			return App::abort(500);
		}
		// 관리자로 지정되면 장비관리자 그룹에 넣기
		// 지방청 관리자면 지방청관리자 그룹에 넣기
		
		if ($node->type_code == 'D002') {
			User::find($userId[0])->groups()->attach($capsaicinGroup->id);
			User::find($userId[0])->groups()->attach($psGroup->id);
		} else {
			User::find($userId[0])->groups()->attach($psGroup->id);
		}
		// 해당 노드의 전임 관리자는 그룹에서 빼기
		if ($predecessorId) {
			if ($node->type_code == 'D002') {
				$capsaicinGroup->users()->detach($predecessorId);
				$psGroup->users()->detach($predecessorId);
			} else {
				$psGroup->users()->detach($predecessorId);
			}
		}
		$user = User::find($userId[0]);
		
		// 해당 계정이 승인되어있지 않으면 승인해준다
		if (!$user->activated) {
			$user->activated = true;
			if (!$user->save()) {
				return App::abort(500);
			}
		}
		
		$msg = "관리자가 변경되었습니다.";
		$code = 1;
		DB::commit();
		return array('msg'=>$msg, 'code'=>$code);
	}
	public function displayNodesSelectModal() {
		$nodeId = Input::get('node_id');
		return View::make('widget.node-selector', get_defined_vars());
	}
	public function getUsers() {
		return Datatable::query(User::with('department'))
		->showColumns('id', 'user_name', 'account_name')
		->addColumn('dept_name', function($model) {
			return $model->department->full_name;
		})
		->searchColumns('user_name', 'dept_name', 'account_name')
		->orderColumns('id')
		->make();
	}

	public function getNodeManager(){
		$nodeId = Input::get("nodeId");
		$node = EqSupplyManagerNode::find($nodeId);
		$manager = $node->manager;
		if ($manager) {
			$manager['last_manager_changed_date'] = $node->last_manager_changed_date;
		}
		if ($manager) {
			$manager['is_exist'] = 1;
		} else {
			$manager['is_exist'] = 0;
		}
		$manager['personnel'] = $node->personnel;
		$manager['capacity'] = $node->capacity;
		return $manager;
	}

	public function semsIndex() {
		$user = Sentry::getUser();
		$childrenNodes = $user->supplyNode->managedChildren;
		if (!$childrenNodes) {
			return Redirect::back()->with('message','관리할 하위부서가 없습니다.');
		}
		return View::make('manager.sems-index', array("id"=>$user->supplyNode->id, "fullName"=>$user->supplyNode->full_name));
	}

	public function displayDashboard() {

		$user = Sentry::getUser();
		$fullPath = $user->department->full_path;
		
		if($user->isSuperUser()){
			$data['mods'] = ModUser::where('approved','=','0')->orderBy('id','desc')->get();
		} else {
			$data['mods'] = $this->service->getModifyRequestsList($fullPath);
		}

		return View::make('manager.dashboard', $data);
	}

	public function displayUserListToModify(){

		$user = Sentry::getUser();
		$fullPath = $user->department->full_path;

		if($user->isSuperUser()){
			$data['mods'] = ModUser::where('approved','=','0')->orderBy('id','desc')->get();
		} else {
			$data['mods'] = $this->service->getModifyRequestsList($fullPath);
		}

		return View::make('manager.to-modify', $data);
	}

	public function showModified($id){

		$new = ModUser::find($id);
		$user = User::find($new->user_id);

		$data = array();
		$data['newrank'] = $new->rank->title;
		$data['newdept'] = $new->department->full_name;
		$data['newname'] = $new->user_name;
		$data['oldrank'] = $user->rank->title;
		$data['oldname'] = $user->user_name;
		$data['olddept'] = $user->department->full_name;

		return $data;
	}

	public function saveModified($id){

		$new = ModUser::find($id);
		$user = User::find($new->user_id);

		$user->user_rank = $new->user_rank;
		$user->user_name = $new->user_name;
		$user->dept_id = $new->dept_id;

		if($user->save()){
			DB::update('update mod_user set approved = 1 where user_id ='.$new->user_id);
			return "정보가 수정되었습니다.";
		} else {
			return "서버 오류가 발생했습니다.";
		}
	}
}