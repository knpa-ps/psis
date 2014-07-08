<?php 

Route::group(array('prefix'=>'equips', 'before'=>'auth'), function(){

	Route::get('/', 'EquipController@index');

	Route::group(array('before'=>'permission:eq.admin'), function() {
		Route::resource('/categories', 'EqCategoryController');
	});
	
	
	Route::resource('/items', 'EqItemController');
	Route::get('items/{id}/data', 'EqItemController@getData');
	Route::resource('/inventories', 'EqInventoryController');
	Route::post('/inventories/create/get_items_in_category', 'EqInventoryController@getItemsInCategory');
	Route::get('/items/{itemId}/details', 'EqItemController@displayDetailsList');
	Route::get('/items/{itemId}/detail/{id}', 'EqItemController@displayExtraInfo');
	Route::get('/items/{itemId}/new_detail', 'EqItemController@displayDetailForm');
	Route::post('/items/{itemId}/new_detail', 'EqItemController@doPost');
	Route::delete('/items/{itemId}/detail/{id}', 'EqItemController@deletePost');
	Route::get('items/{itemId}/detail/{id}/update', 'EqItemController@displayUpdatePostForm');
	Route::post('/items/{itemId}/detail/{id}/update', 'EqItemController@updatePost');

	Route::resource('supplies', 'EqSupplyController');
	Route::put('/supplies/{id}/detail', 'EqSupplyController@addSupply');
	Route::delete('/supplies/{id}/detail/{detailId}', 'EqSupplyController@removeSupply');
	Route::post('/supplies/create/get_classifiers', 'EqSupplyController@getClassifiers');
});

