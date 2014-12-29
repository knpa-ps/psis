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
});