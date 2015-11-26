<?php

class AdminController extends BaseController {
	public function displayDeptTree() {
		$codes = Code::where('category_code','H002')->get();

		$typeCodes = array();
        foreach ($codes as $code) {
            $typeCodes[$code->code] = $code->title;
        }
		return View::make('admin.depts', array('typeCodes'=>$typeCodes));
	}
	public function savePermission() {
		$keys = Input::get('permission_keys');
		$groupId = Input::get('group_id');
		$group = Group::find($groupId);

		if ($group === null) {
			return App::abort(400);
		}

		$permissions = array();
		foreach ($keys as $key) {
			$permissions[$key] = 1;
		}

		$group->permissions = $permissions;

		if (!$group->save()) {
			return App::abort(500);
		}

		return "변경사항이 저장되었습니다.";
	}
	public function getPermission(){
		$groupId = Input::get('id');
		$group = Group::find($groupId);

		if ($group === null) {
			return App::abort(400);
		}

		$permissions = Permission::all();

		return View::make('admin.permission-list', array('permissions'=>$permissions, 'group'=>$group));
	}
	public function displayPermissionMng() {
		$user = Sentry::getuser();
		$groups = Group::all();
		return View::make('admin.permissionmng', array('user'=>$user, 'groups'=>$groups));
	}
	public function displayDashboard() {
		return View::make('admin.dashboard');
	}

	public function displayMenuTree() {
		$groups = Group::all();
		return View::make('admin.menus', array('groups'=>$groups));
	}

	public function displayUserGroups() {
		$groups = Group::with('users')->paginate(15);

		return View::make('admin.groups', array('groups'=>$groups));
	}

	/**
	 * 그룹 목록 데이터 가져오기
	 */
	public function getUserGroupsData() {
		return Datatable::collection(Group::with('users')->get(array('id','name','key')))
        ->showColumns('id', 'name', 'key')
        ->addColumn('users_count', function($model) {
        	return $model->users()->count();
        })
        ->searchColumns('name')
        ->orderColumns('id')
        ->make();
	}

	/**
	 * 그룹 생성
	 */
	public function insertUserGroup() {

	}

	/**
	 * 그룹 삭제
	 */
	public function deleteUserGroup() {
	}

	/**
	 * 그룹 정보 수정
	 */
	public function editUserGroup() {

	}

	/**
	 * 사용자 선택 modal 출력
	 */
	public function displayUsersSelectModal() {
		$groupId = Input::get('group_id');

		return View::make('widget.user-selector', get_defined_vars());
	}

	// 전체 사용자 목록 데이터 가져오기
	public function getUserAll() {
		$excludeGroupId = Input::get('group_id');
		$group = Group::with('users')->find($excludeGroupId);

		if ($group === null) {
			return App::abort(400);
		}

		$excludeUserIds = array(-1);
		foreach ($group->users as $user) {
			$excludeUserIds[] = $user->id;
		}

		return Datatable::query(User::with('department')->whereNotIn('id', $excludeUserIds))
		->showColumns('id', 'user_name')
		->addColumn('dept_name', function($model) {
			return $model->department->full_name;
		})
		->searchColumns('user_name', 'dept_name')
		->orderColumns('id')
		->make();
	}



	// 그룹생성 modal 출력
	public function displayGroupCreateModal() {
		return View::make('widget.group-creator');
	}
	//그룹 수정 modal 출력
	public function displayGroupModifyModal($group_id) {
		return View::make('widget.group-modifier', array('id'=>$group_id) );
	}

	/**
	 * 그룹 소속 사용자 목록 데이터 가져오기
	 */
	public function getUserGroupUsersData() {
		return Datatable::query(Group::with('users')->where('id', '=', Input::get('group'))->first()->users())
        ->showColumns('id', 'user_name')
        ->addColumn('dept_full_name', function($model) {
    		$dept = Department::find($model->dept_id);
    		if ($dept === null) {
    			return '';
    		}
        	return $dept->full_name;
        })
        ->searchColumns('name')
        ->orderColumns('name')
        ->make();
	}

	/**
	 * 그룹에 사용자 추가
	 */
	public function addUsersToUserGroup() {
		$group_id = Input::get('group_id');
		$user_ids = Input::get('users');
		if(!$user_ids){
			return "추가할 사용자를 선택하세요.";
		}
		foreach ($user_ids as $user_id) {
			User::find($user_id)->groups()->attach($group_id);
		}
		return "선택한 사용자가 해당 그룹에 추가되었습니다.";
	}

	/**
	 * 그룹에서 사용자 제거
	 */
	public function removeUsersFromUserGroup() {

		$group = Group::find(Input::get('group_id'));

		if ($group === null) {
			return App::abort(400);
		}

		$inputIds = Input::json();

		DB::beginTransaction();

		foreach ($inputIds as $id) {
			$group->users()->detach($id);
		}

		DB::commit();

		return array('result'=>0, 'message'=>trans('global.done'));

	}

	public function showGroupList()
	{
		$groups = Group::paginate(3);

		return View::make('admin.groups', array('groups'=>$groups));
	}

	public function showUserList($data = array())
	{
        $codes = Code::in('H001');
        $codeSelectItems = array();
        foreach ($codes as $code) {
            $codeSelectItems[$code->code] = $code->title;
        }
        $data['codeSelectItems'] = $codeSelectItems;

		// @todo
		$groups = Group::where('id','!=',1);
		if (!Sentry::getUser()->isSuperUser()) {
			$groups->where('id', '!=', 2)->where('id', '!=', 4);
		}

		$data['groups'] = $groups->get();

        //default form value
        $data = array_merge(array(
                'accountName' => '',
                'userRank' => 'R011',
                'userName' => '',
                'departmentName' => '',
                'departmentId' => ''
            ), $data);
		return View::make('admin.users', $data);
	}

	public function getUsers()
	{
		$user = Sentry::getUser();

		$builder = User::table()
					->select(array(
							'users.id',
							'users.account_name',
							'users.user_name',
							'codes.title',
							'departments.full_name',
							'users.dept_detail',
							'users.activated'
						));

		if (!$user->isSuperUser())
		{
			if ($user->hasAccess('admin')) {
				$region = Department::region($user->dept_id);
				$builder->where('departments.full_path','like', "%:{$region->id}:%");
			} else {
				$builder->where('departments.full_path','like', "%:{$user->dept_id}:%");
			}
		}

		return Datatables::of($builder)->make();
	}

	public function showUserDetail($userId = null)
	{
		$codes = Code::in('H001');
        $ranks = array();
        foreach ($codes as $code) {
            $ranks[$code->code] = $code->title;
        }

        if ($userId)
        {
			$user = User::where('id', '=', $userId)->with('rank', 'groups', 'department')->first();
			$cUser = Sentry::getUser();
			$region = Department::region($cUser->dept_id);
			if (!$cUser->isSuperUser() && !Department::isAncestor($user->dept_id, $region->id))
			{
				return App::abort(404, "unauthorized action");
			}
        }
        else
        {
        	$user = new User;
        }

		// @todo
		$groups = Group::where('id','!=',1);
		if (!Sentry::getUser()->isSuperUser()) {
			$groups->where('id', '!=', 2)->where('id', '!=', 4);
		}

		return View::make('admin.user-info', array(
			'user'=>$user,
			'ranks'=>$ranks,
			'groups'=>$groups->get()
		));
	}

	public function showPermissions()
	{
		$mid = Input::get('mid');
		if ($mid)
		{
			$permissions = Permission::where('module_id','=',$mid)->get();
			$currentModule = Module::where('id','=',$mid)->first();
		}
		else
		{
			$permissions = array();
			$currentModule = null;
		}
		$groups = Sentry::findAllGroups();
		$modules = Module::all();
		return View::make('admin.permissions', array('modules'=>$modules, 'permissions'=>$permissions,
			'groups'=>$groups, 'currentModule'=>$currentModule));
	}

	public function setUserActivated()
	{
		$activated = Input::get('activated');
		$ids = Input::get('ids');
		foreach ($ids as $id)
		{
			if (!is_numeric($id))
			{
				Log::error('requested user id is not numeric value');
				return Lang::get('strings.server_error');
			}
		}


		foreach ($ids as $id)
		{
			$user = User::find($id);
			if ($user->account_name === 'admin')
			{
				return Lang::get('strings.cannot_update_admin');
			}

			$user->activated = $activated;
			if ($activated)
			{
				$user->activated_at = date('Y-m-d H:i:s');
			}
			$user->save();
		}
	}

	public function deleteUser()
	{
		$ids = Input::all();
		foreach ($ids as $id)
		{
			if (!is_numeric($id))
			{
				Log::error('requested user id is not numeric value');
				return Lang::get('strings.server_error');
			}
		}


		foreach ($ids as $id)
		{
			$user = User::find($id);
			if ($user->account_name === 'admin')
			{
				return Lang::get('strings.cannot_delete_admin');
			}
			$user->delete();
		}
	}

	public function updateUser($userId)
	{
		$codes = Code::in('H001');
		$ranks = array();
		foreach ($codes as $c) $ranks[] = $c['code'];
		$ranks = implode(',',$ranks);

		$input = Input::all();

        $validator = Validator::make($input,
            array(
                'user_name' => 'required|max:10',
                'user_rank' => "required|in:$ranks",
                'department_id' => "required|exists:departments,id",
                'dept_detail' => 'max:100',
                'password' => 'min:8|confirmed'
            )
        );

        if ($input['password'])
        {
        	$user = Sentry::findUserById($userId);
        	$user->password = $input['password'];
    		$user->save();
        }

        if ($validator->fails())
        {
        	$msg = array();
        	foreach ($validator->messages()->all() as $m) $msg[] = $m;
        	$msg = implode('<br>', $msg);
        	Log::error('update user : input validation fails. '.$validator->messages()->all());
        	LayoutComposer::addNotification('error', $msg);
        	return $this->showUserDetail($userId);
        }

        $user = User::find($userId);

        $user->user_name = $input['user_name'];
        $user->user_rank = $input['user_rank'];
        $user->dept_id = $input['department_id'];
        $user->dept_detail = $input['dept_detail'];

        $user->groups()->detach();
        if (!isset($input['groups_ids']))
        {
        	$input['groups_ids'] = array();
        }
        foreach ($input['groups_ids'] as $groupId)
        {
        	$user->groups()->attach($groupId);
        }
        $user->push();
    	LayoutComposer::addNotification('success', Lang::get('strings.success'));
        return $this->showUserDetail($userId);
	}

	public function insertUser()
	{
		$account = Input::get('account_name');
        $accountNameLabel = Lang::get('labels.login_account_name');
        $validator = Validator::make(array(
	        	$accountNameLabel => $account
		    ),
		    array(
		        $accountNameLabel => 'required|alpha_dash|between:4,30|unique:users,account_name'
		    )
		);

        if ($validator->fails())
        {
        	$msg = array();
        	foreach ($validator->messages()->all() as $m) $msg[] = $m;
        	$msg = implode('<br>', $msg);
        	Log::error('insert user : input validation fails. '.$validator->messages()->all());
        	LayoutComposer::addNotification('error', $msg);
        	return $this->showUserDetail();
        }

        $user = Sentry::createUser(array(
        	'account_name'=>$account,
        	'password'=>'tmppwd',
        	'email'=>$account,
        	'activated'=>true
        	));

        return $this->updateUser($user->id);

	}

	public function getGroups()
	{
		$builder = Group::select(array(
				'id',
				'name',
				'created_at'
			))->where('id','!=','1');
		$user = Sentry::getUser();
		if (!$user->isSuperUser()) {
			$builder->where('id', '!=', 2)->where('id', '!=', 4);
		}

		return Datatables::of($builder)->make();
	}

	public function deleteGroup()
	{
		$ids = Input::all();
		foreach ($ids as $id)
		{
			if (!is_numeric($id))
			{
				Log::error('requested group id is not numeric value');
				return Lang::get('strings.server_error');
			}
		}

		foreach ($ids as $id)
		{
			$g = Sentry::findGroupById($id);
			if ($g->name === '관리자')
			{
				return Lang::get('strings.cannot_delete_admin');
			}
			$g->delete();
		}
		return Lang::get('strings.success');
	}

	public function createUserGroup()
	{
		$gname = Input::get('groupName');
		$key = Input::get('key');
		Sentry::createGroup(array(
			'name'=>$gname,
			'key'=>$key
			));
		return Redirect::to('admin/groups')->with('message', Lang::get('strings.success'));
	}

	public function modifyUsergroup() {
		$gname = Input::get('groupName');
		$key = Input::get('key');

		$group = Sentry::findGroupById(Input::get('group_id'));
		$group->name = $gname;
		$group->key = $key;
		if($group->save()){
			return Redirect::to('admin/groups')->with('message', Lang::get('strings.success'));
		}
		else{
			return Redirect::to('admin/groups')->with('message', Lang::get('strings.server_error'));
		}
	}

	public function updatePermissions()
	{
		$mid = Input::get('mid');

		$permissions = Permission::where('module_id','=',$mid)->get();
		$groups = Sentry::findAllGroups();

		foreach ($groups as $group)
		{
			$groupPerms = array();
			foreach ($permissions as $perm)
			{
				$groupIds = Input::get(str_replace('.', '_', $perm->key));
				$groupPerms[$perm->key] = $groupIds && in_array($group->id, $groupIds);
			}
			$group->permissions = $groupPerms;
			$group->save();
		}

		return json_encode(array('type'=>'success','layout'=>'topRight','text'=>Lang::get('strings.success')));
	}

	public function addDept()
	{
		$data = array('parent_id' => Input::get('parent_id'),'dept_name'=>Input::get('dept_name'),'is_alive'=>1);
		DB::table('departments')->insert($data);
		return '입력완료ㅋ';
	}

	public function adjustDepts()
	{
		/**
		 * full path, full name, depth
		 */
		// 초기화
		DB::table('departments')->update(array(
			'full_path'=>DB::raw('CONCAT(":",id,":")'),
			'full_name'=>DB::raw('dept_name'),
			'depth'=>1
			));

		$maxDepth = Input::get('maxDepth');
		$maxDepth = $maxDepth ? $maxDepth : 10;

		for ($i=0; $i<$maxDepth; $i++)
		{
			DB::insert(DB::raw('INSERT INTO departments (id, full_path, full_name, depth)
			SELECT
			id,
			(
				SELECT
				IF(parent_id=0 OR parent_id IS NULL,"",CONCAT(":",parent_id))
				FROM departments AS b
				WHERE b.id = TRIM(LEADING ":" FROM LEFT(sub.full_path, LOCATE(":",sub.full_path,2)-1))
			) as newPath,
			(
				(SELECT IFNULL(CONCAT(dept_name, " "), "")
					FROM departments
					WHERE id = (SELECT
				parent_id
				FROM departments AS b
				WHERE b.id = TRIM(LEADING ":" FROM LEFT(sub.full_path, LOCATE(":",sub.full_path,2)-1))))
			) as newName,
			(
				SELECT
				IF(parent_id=0 OR parent_id IS NULL,0,1)
				FROM departments AS b
				WHERE b.id = TRIM(LEADING ":" FROM LEFT(sub.full_path, LOCATE(":",sub.full_path,2)-1))
			) as depthIncrement

			FROM departments AS sub

			ON DUPLICATE KEY UPDATE
				full_path = CONCAT(VALUES(full_path),departments.full_path),
				full_name = CONCAT(VALUES(full_name),departments.full_name),
				depth = departments.depth+VALUES(depth) '));
		}

		//is terminal
		DB::insert(DB::raw('INSERT INTO departments
							(id, is_terminal, is_alive)
							SELECT
							id,
							(SELECT
							COUNT(*) = 0
							FROM departments AS c
							WHERE c.parent_id = p.id),
							1
							FROM departments p

							ON DUPLICATE KEY UPDATE
							is_terminal = values(is_terminal),
							is_alive = values(is_alive)'));

		//sort_order
		DB::insert(DB::raw('UPDATE departments SET sort_order = id'));

		return 'done';
	}

	public function setUserGroups() {
		$userIds = Input::get('user_ids');
		$groupIds = Input::get('groups_ids');

		foreach ($userIds as $user) {
			$user = User::find($user);
			$user->groups()->sync($groupIds);
			$user->save();
		}

		return '완료되었습니다';
	}

	public function showDepts() {

		return View::make('admin.depts');
	}

	public function adjustHierarchy() {

		$headNode = EqSupplyManagerNode::where('type_code','=','D001')->first();

		DB::beginTransaction();

		$this->doAdjustHierarchy($headNode, 1);

		DB::commit();
	}

	private function doAdjustHierarchy(EqSupplyManagerNode $parent = null, $parentManagerNodeId ) {

		if ($parent === null) {
			return "parent_node error.";
		} else {
			$children = $parent->children;
		}
		if (sizeof($children) == 0) {
			$parent->is_terminal = 1;
			if (!$parent->save()) {
				return "is_terminal save failed.";
			}
		}

		foreach ($children as $child) {

			// 하위부서의 계층 정보를 업데이트
			$child->full_path = $parent->full_path."{$child->id}:";
			$child->parent_manager_node = $parentManagerNodeId;

			if ($child->is_selectable == 0) {
				$child->parent_manager_node = null;
			}

			if ($parent->type_code == 'D001') {
				$child->full_name = $child->node_name;
			} else {
				if ($child->is_selectable == 1 or $child->type_code=="D003") {
					$child->full_name = $parent->full_name." {$child->node_name}";
				} else {
					$child->full_name = $parent->full_name;
				}
			}

			if (!$child->save()) {
				return "child node save failed.";
			}

			// traverse
			if ($child->is_selectable == 0) {
				$this->doAdjustHierarchy($child, $parentManagerNodeId);
			} else {
				$this->doAdjustHierarchy($child, $child->id);
			}
		}
	}

	//지워진 Type에 입력된 데이터를 실제 Type의 데이터에 합치는 과정
	public function mergeInventoryCount($itemId){
		$sets=EqInventorySet::where('item_id','=',$itemId)->get();

		DB::beginTransaction();
		foreach($sets as $set){
			$realTypeId=EqItemType::where('item_id','=',$itemId)->first()->id;
			$dataCountSum=EqInventoryData::where('inventory_set_id','=',$set->id)->sum('count');
			$dataWreckedSum=EqInventoryData::where('inventory_set_id','=',$set->id)->sum('wrecked');
			$realType=EqInventoryData::where('inventory_set_id','=',$set->id)->where('item_type_id','=',$realTypeId)->first();
			$realType->count=$dataCountSum;
			$realType->wrecked=$dataWreckedSum;
			$realType->save();
		}
		DB::commit();
	}

	public function deleteTypeData($itemId){
		$sets=EqInventorySet::where('item_id','=',$itemId)->get();
		DB::beginTransaction();
		foreach($sets as $set){
			$realTypeId=EqItemType::where('item_id','=',$itemId)->first()->id;
			$deletedTypes=EqInventoryData::where('inventory_set_id','=',$set->id)->where('item_type_id','!=',$realTypeId)->get();
			if($deletedTypes!==null){
				foreach($deletedTypes as $deletedType){
					$deletedType->delete();
				}
			}
		}
		DB::commit();
	}

	// 90 사이즈 만들어넣기. 추후 type 넣어야 할 경우 재 사용
	public function insertType90(){
		$datas=EqInventoryData::where('item_type_id','=',298)->get();
		$sets=EqInventorySet::where('item_id','=',156)->get();
		DB::beginTransaction();
		foreach($datas as $d){
			$d->item_type_id=34;
			if (!$d->save()) {
				return App::abort(500);
			}
		}
		foreach($sets as $set){
			$invData=new EqInventoryData;
			$invData->inventory_set_id=$set->id;
			$invData->item_type_id=298;
			$invData->count=0;
			if (!$invData->save()) {
				return App::abort(500);
			}
		}
		DB::commit();
	}

	public function returnPath($node){
		return explode(':', trim($node->full_path, ':'));
	}

	// 경찰서 경비, 방순대 산하기관들이 뒤섞여 이를 바로잡아줌
	public function departmentAdjust(){
		$psNodes = EqSupplyManagerNode::where('type_code','=','D003')->get();

		DB::beginTransaction();
		foreach($psNodes as $psNode){
			$guard = $psNode->children()->where('node_name','=','경비')->first();
			$patrol = $psNode->children()->where('node_name','=','방순대')->first();

			// 방순대 산하에 있는 경비 산하기관들을 경비 산하로 이동
			if($patrol){
				echo $patrol->full_name."<br>";
				$patrolDepts = $patrol->children()->get();
				foreach($patrolDepts as $patrolDept){
					$nodeName = $patrolDept->node_name;
					if(!($nodeName == "본부소대" || $nodeName == "1소대" || $nodeName == "2소대" || $nodeName == "3소대")){
						$pathArray = explode(':', trim($patrolDept->full_path, ':'));
						$path = $guard->full_path.":".end($pathArray).":";
						$patrolDept->full_path = $path;
						$patrolDept->parent_id = $guard->id;
						if (!$patrolDept->save()) {
							return App::abort(500);
						}
					}
				}
			}

			// 경비 산하에 있는 방순대 산하기관들을 방순대 산하로 이동
			if($guard){
				echo $guard->full_name."<br>";
				$guardDepts = $guard->children()->get();
				foreach($guardDepts as $guardDept){
					$nodeName = $guardDept->node_name;
					if($nodeName == "본부소대" || $nodeName == "1소대" || $nodeName == "2소대" || $nodeName == "3소대"){
						$pathArray = explode(':', trim($guardDept->full_path, ':'));
						$path = $patrol->full_path.end($pathArray).":";
						$guardDept->full_path = $path;
						$guardDept->parent_id = $patrol->id;
						if (!$guardDept->save()) {
							return App::abort(500);
						}
					}
				}
			}
		}
		DB::commit();
	}

	public function displayCheckPeriod() {
		$categories = EqCategory::orderBy('sort_order')->get();
		// 오늘 날짜
		$today = date('Y-m-d');
		foreach ($categories as $category) {
			foreach ($category->codes as $c) {
				$items[$c->id] = EqItem::where('item_code','=',$c->code)->where('is_active','=',1)->orderBy('acquired_date','DESC')->get();
				foreach ($items[$c->id] as $item) {
					$checkPeriod[$item->id] = EqQuantityCheckPeriod::where('item_id','=',$item->id)->first();
				}
			}
		}
				return View::make('admin.equips-term',get_defined_vars());
	}

	public function setCheckPeriodForItem() {
		$data=Input::all();
		$codes = EqItemCode::all();
		foreach ($codes as $code) {
			$items = $code->items;
			foreach ($items as $item) {
				$checkPeriod = EqQuantityCheckPeriod::where('item_id','=',$item->id)->first();
				$itemName = "item_".$item->id;
				$checkPeriod->check_end = $data[$itemName];
				if(!$checkPeriod->save()){
					return App::abort(500);
				}
			}
			
			$codeName = "code_".$code->id;
			if($data[$codeName] != "") {
				foreach ($items as $item) {
					$checkPeriod = EqQuantityCheckPeriod::where('item_id','=',$item->id)->first();
					$itemName = "item_".$item->id;
					$checkPeriod->check_end = $data[$codeName];
					if(!$checkPeriod->save()){
						return App::abort(500);
					}
				}
			}
		}


		return "저장되었습니다.";
	}

	public function setCheckPeriod($year) {
		$checkPeriods = EqQuantityCheckPeriod::get();
		$items = EqItem::where('is_active','=',1)->where('acquired_date','like',$year.'%')->get();
		DB::beginTransaction();
		foreach ($items as $item) {
			$checkPeriod = EqQuantityCheckPeriod::where('item_id','=',$item->id)->first();

			$checkPeriod->check_end = "2015-10-30";
			$checkPeriod->save();
		}
		DB::commit();
	}
}
