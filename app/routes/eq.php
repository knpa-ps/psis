<?php 

Route::group(array('prefix'=>'equips', 'before'=>'auth'), function(){

	Route::get('/', 'EquipController@index');


	Route::resource('/surveys', 'EqSurveyController');
	Route::put('/surveys/{id}/response', 'EqSurveyController@updateResponse');
	Route::post('/surveys/{id}/response','EqSurveyController@storeResponse');
	Route::get('/surveys/{id}/response', 'EqSurveyController@doResponse');
	Route::get('/surveys/{id}/data', 'EqSurveyController@getData');	
	
	Route::resource('/inventories', 'EqInventoryController');
	Route::get('/inventories/code/{itemCode}','EqInventoryController@showCodeBelongs');
	Route::post('/inventories/create/get_item_type_set/{itemId}', 'EqInventoryController@getItemTypeSet');
	Route::post('/inventories/create/get_items_in_code', 'EqInventoryController@getItemsInCode');
	Route::post('/inventories/create/get_items_in_category', 'EqInventoryController@getItemsInCategory');
	Route::get('/items/{itemId}/discard', 'EqInventoryController@displayDiscardForm');
	Route::post('/items/{itemId}/discard', 'EqInventoryController@discardItem');
	Route::post('/items/{itemId}/wrecked_update', 'EqInventoryController@wreckedUpdate');


	Route::get('/items/{itemId}/details', 'EqItemController@displayDetailsList');
	Route::get('/items/{itemId}/detail/{id}', 'EqItemController@displayExtraInfo');
	Route::get('/items/{itemId}/new_detail', 'EqItemController@displayDetailForm');
	Route::post('/items/{itemId}/new_detail', 'EqItemController@doPost');
	Route::delete('/items/{itemId}/detail/{id}', 'EqItemController@deletePost');
	Route::get('items/{itemId}/detail/{id}/update', 'EqItemController@displayUpdatePostForm');
	Route::post('/items/{itemId}/detail/{id}/update', 'EqItemController@updatePost');
	Route::get('/items/{itemId}/holding', 'EqItemController@holdingDetail');
	Route::get('items/{id}/data', 'EqItemController@getData');
	Route::get('/items/list/{codeId}', 'EqItemController@showRegisteredList');

	Route::resource('capsaicin', 'EqCapsaicinController');
	Route::get('/capsaicin/node/{nodeId}', 'EqCapsaicinController@displayNodeState');


	Route::resource('supplies', 'EqSupplyController');
	Route::put('/supplies/{id}/detail', 'EqSupplyController@addSupply');
	Route::delete('/supplies/{id}/detail/{detailId}', 'EqSupplyController@removeSupply');
	Route::post('/supplies/create/get_classifiers', 'EqSupplyController@getClassifiers');

	Route::resource('convert', 'EqConvertController');
	Route::post('/convert/{id}/confirm', 'EqConvertController@convertConfirm');

});

