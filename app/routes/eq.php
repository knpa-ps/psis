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
	// 분실/폐기
	Route::get('/items/{itemId}/discard', 'EqInventoryController@displayDiscardForm');
	Route::get('/items/{itemId}/discard_list', 'EqInventoryController@displayDiscardList');
	Route::post('/items/{itemId}/discard', 'EqInventoryController@discardItem');
	Route::get('/items/{setId}/delete_discarded_item', 'EqInventoryController@deleteDiscardedItem');

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
	Route::get('/capsaicin/node/{nodeId}/holding', 'EqCapsaicinController@nodeHolding');
	Route::get('/capsaicin/node/{nodeId}/events', 'EqCapsaicinController@nodeEvents');
	Route::get('/capsaicin/node/{nodeId}/confirm', 'EqCapsaicinController@showRegionConfirm');
	Route::post('/capsaicin/get_events', 'EqCapsaicinController@getEvents');
	Route::get('/capsaicin/node/{nodeId}/add_event', 'EqCapsaicinController@addEvent');
	Route::post('/capsaicin/node/{nodeId}/add_event', 'EqCapsaicinController@storeNewEvent');
	Route::post('/capsaicin/delete_event/{eventId}', 'EqCapsaicinController@deleteEvent');


	Route::get('/capsaicin_usage/{usageId}/edit', 'EqCapsaicinController@editUsage');
	Route::post('/capsaicin_usage/{usageId}/update', 'EqCapsaicinController@updateUsage');
	Route::delete('/capsaicin_usage/{usageId}', 'EqCapsaicinController@deleteUsageRequest');

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

	//살수차(PAVA) 메뉴 관련
	Route::resource('water_pava', 'EqWaterPavaController');
	Route::get('water_per_month', 'EqWaterPavaController@waterPerMonth');
	Route::post('get_water_consumption_by_month', 'EqWaterPavaController@getConsumptionPerMonth');
	Route::get('pava_per_month', 'EqWaterPavaController@pavaPerMonth');
	Route::post('pava_per_month_data', 'EqWaterPavaController@pavaPerMonthData');
	Route::get('pava_confirm', 'EqWaterPavaController@showRegionConfirm');
	Route::delete('pava/{eventId}', 'EqWaterPavaController@deleteEventRequest');

	Route::resource('pava_io', 'EqPavaIOController');

	// 본청 - 캡사이신, 파바 보고내역 삭제 확인
	Route::delete('confirm_delete/{reqId}', 'EquipController@deleteConfirm');


	Route::get('/capsaicin/drillstore/{nodeId}/{count}/{month}', 'EqCapsaicinController@drillstore');

	/* Custom Routes */
	// 특정 아이템 수량 다 날려버림
	Route::get('clear_item_data/{nodeId}/{itemId}','EquipController@clearItemData');
	// eq_quantity_check_period에 item_id 추가해 주고 item별 입력기간 설정
	Route::get('check_period_for_each_item','EquipController@checkPeriodForEachItem');
	Route::get('set_check_period','EquipController@setCheckPeriod');
});
