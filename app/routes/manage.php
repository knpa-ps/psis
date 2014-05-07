<?php 

Route::group(array('prefix'=>'manage', 'before'=>'auth'), function(){

    Route::get('/', 'ManageController@displayDashboard');

    Route::get('/data', 'ManageController@getUserData');
    Route::post('/data/detail', 'ManageController@getUserdataDetail');
    Route::post('/activate', 'ManageController@activateUser');

    Route::post('/modify/data', 'ManageController@getModifyData');
    Route::post('/modify', 'ManageController@modifyData');

});