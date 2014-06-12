<?php 

Route::group(array('prefix'=>'equips', 'before'=>'auth'), function(){

	Route::get('/', 'EquipController@index');

	Route::group(array('before'=>'permission:eq.admin'), function() {
		Route::resource('/categories', 'EqCategoryController');
	});
	
	Route::resource('/items', 'EqItemController');
	Route::get('items/{id}/data', 'EqItemController@getData');
	Route::resource('/items/{id}/details', 'EqItemController@showDetails');
	Route::resource('/inventories', 'EqInventoryController');
});