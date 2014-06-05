<?php 

Route::group(array('prefix'=>'manager', 'before'=>'auth|permission:manager'), function(){
	Route::get('/', 'ManagerController@displayDashboard');
	Route::resource('users', 'UserController');
	Route::get('/modify', "ManagerController@displayUserListToModify");
	Route::get('/showmodified/{id}','ManagerController@showModified');
	Route::post('/savemodified/{id}','ManagerController@saveModified');
});