<?php

class ManagerController extends \BaseController {

	public function __construct() {
		$this->service = new ManagerService;
	}

	public function changeNodeManager() {
		$userId = Input::get('userId');
		$nodeId = Input::get('nodeId');

		if(!$userId){
			$msg = "관리자로 지정할 사용자를 선택하세요.";
			$code = 0; 
			return array('msg'=>$msg, 'code'=>$code);
		}
		$node = EqSupplyManagerNode::find($nodeId);
		$node->manager_id = $userId[0];
		$node->last_manager_changed_date = date('Y-m-d H:i:s');

		if (!$node->save()) {
			return App::abort(500);
		}

		$msg = "관리자가 변경되었습니다.";
		$code = 1;
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
		return $manager;
	}

	public function semsIndex() {
		$user = Sentry::getUser();
		return View::make('manager.sems-index', array("id"=>$user->supplyNode->id));
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