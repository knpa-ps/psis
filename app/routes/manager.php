<?php 

Route::group(array('prefix'=>'manager', 'before'=>'auth|permission:manager'), function(){
	Route::get('/', 'ManagerController@displayDashboard');
	Route::resource('users', 'UserController');
	Route::get('/modify', "ManagerController@displayUserListToModify");
	Route::get('/showmodified/{id}','ManagerController@showModified');
	Route::post('/savemodified/{id}','ManagerController@saveModified');
	
	Route::get('/sems', "ManagerController@semsIndex");
	Route::post('/sems', "ManagerController@getNodeManager");
	Route::post('/sems/users/show', "ManagerController@displayNodesSelectModal");
	Route::get('/sems/users', "ManagerController@getUsers");
	Route::post('/sems/users/change_node_manager', "ManagerController@changeNodeManager");

	Route::get('/sems/ildanwi', function() {
		$guards = EqSupplyManagerNode::where("node_name",'=',"경비")->get();
		foreach ($guards as $guard) {
			$idw = new EqSupplyManagerNode;
			$idw->manager_id = null;
			$idw->parent_id = $guard->id;
			$idw->node_name = "1단위부대";
			$idw->full_path = $guard->full_path.$idw->id.':';
			$idw->is_terminal = 1;
			$idw->full_name = $guard->full_name;
			$idw->is_selectable = 0;

			if (!$idw->save()) {
				return App::abort(500);
			}
		}
	});
});