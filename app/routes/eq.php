<?php 

Route::group(array('prefix'=>'equips', 'before'=>'auth'), function(){

	Route::get('/', 'EquipController@index');

	Route::get('/update_personnel/show', 'EquipController@showUpdatePersonnelForm');
	Route::post('/update_personnel', 'EquipController@updatePersonnel');
	Route::post('/get_node_name/{nodeId}', 'EquipController@getNodeName');

	Route::resource('/surveys', 'EqSurveyController');
	Route::put('/surveys/{id}/response', 'EqSurveyController@updateResponse');
	Route::post('/surveys/{id}/response','EqSurveyController@storeResponse');
	Route::get('/surveys/{id}/response', 'EqSurveyController@doResponse');
	Route::get('/surveys/{id}/data', 'EqSurveyController@getData');	
	
	Route::resource('/inventories', 'EqInventoryController');
	Route::get('/inventories/{itemCode}/{itemId}', 'EqInventoryController@showDetail');
	Route::post('/inventories/create/get_item_type_set/{itemId}', 'EqInventoryController@getItemTypeSet');
	Route::post('/inventories/create/get_items_in_code', 'EqInventoryController@getItemsInCode');
	Route::post('/inventories/create/get_items_in_category', 'EqInventoryController@getItemsInCategory');
	Route::get('/items/{itemId}/discard', 'EqInventoryController@displayDiscardForm');
	Route::post('/items/{itemId}/discard', 'EqInventoryController@discardItem');
	Route::post('/items/{itemId}/wrecked_update', 'EqInventoryController@wreckedUpdate');
	Route::post('/items/{itemId}/count_update', 'EqInventoryController@countUpdate');


	Route::get('/items/{itemId}/details', 'EqItemCodeController@displayDetailsList');
	Route::get('/items/{itemId}/detail/{id}', 'EqItemCodeController@displayExtraInfo');
	Route::get('/items/{itemId}/new_detail', 'EqItemCodeController@displayDetailForm');
	Route::post('/items/{itemId}/new_detail', 'EqItemCodeController@doPost');
	Route::delete('/items/{itemId}/detail/{id}', 'EqItemCodeController@deletePost');
	Route::get('items/{itemId}/detail/{id}/update', 'EqItemCodeController@displayUpdatePostForm');
	Route::post('/items/{itemId}/detail/{id}/update', 'EqItemCodeController@updatePost');
	Route::get('/items/{itemId}/holding', 'EqItemCodeController@holdingDetail');
	Route::get('items/{id}/data', 'EqItemCodeController@getData');
	Route::get('/items/list/{codeId}', 'EqItemCodeController@showRegisteredList');

	Route::resource('capsaicin', 'EqCapsaicinController');
	Route::get('/capsaicin/node/{nodeId}', 'EqCapsaicinController@displayNodeState');
	Route::get('/capsaicin_usage/{usageId}/edit', 'EqCapsaicinController@editUsage');
	Route::post('/capsaicin_usage/{usageId}/update', 'EqCapsaicinController@updateUsage');
	Route::delete('/capsaicin_usage/{usageId}', 'EqCapsaicinController@deleteUsage');

	Route::resource('pava', 'EqPavaController');
	Route::get('/pava/node/{nodeId}', 'EqPavaController@displayNodeState');
	Route::get('/pava_usage/{usageId}/edit', 'EqPavaController@editUsage');
	Route::post('/pava_usage/{usageId}/update', 'EqPavaController@updateUsage');
	Route::delete('/pava_usage/{usageId}', 'EqPavaController@deleteUsage');

	Route::resource('supplies', 'EqSupplyController');
	Route::put('/supplies/{id}/detail', 'EqSupplyController@addSupply');
	Route::delete('/supplies/{id}/detail/{detailId}', 'EqSupplyController@removeSupply');
	Route::post('/supplies/create/get_classifiers', 'EqSupplyController@getClassifiers');

	Route::resource('convert', 'EqConvertController');
	Route::get('/convert_cross_head', 'EqConvertController@crossHeadIndex');
	Route::get('/convert_cross_head/{id}/confirm', 'EqConvertController@headConfirm');
	Route::post('/convert/{id}/confirm', 'EqConvertController@convertConfirm');

	Route::resource('water_affair', 'EqWaterController');
	Route::get('water_region', 'EqWaterController@index_by_region');
	Route::post('water_region/get_consumption_by_month', 'EqWaterController@getConsumptionPerMonth');
});

