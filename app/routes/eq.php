<?php 

Route::group(array('prefix'=>'equips', 'before'=>'auth'), function(){

	Route::get('/', 'EquipController@displayDashboard');

	Route::group(array('before'=>'permission:eq.admin'), function() {
		Route::resource('/categories', 'EqCategoryController');
	});
	
	Route::resource('/items', 'EqItemController');
	Route::resource('/items/{id}/details', 'EqItemController@showDetails');
});