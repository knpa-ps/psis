<?php 

Route::group(array('prefix'=>'manager', 'before'=>'auth|permission:manager'), function(){
	Route::get('/', 'ManagerController@displayDashboard');
	Route::resource('users', 'UserController');
});