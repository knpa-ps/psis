<?php 

Route::group(array('prefix'=>'manage', 'before'=>'auth'), function(){

    Route::get('/', 'ManageController@displayDashboard');

    Route::get('/data', 'ManageController@getUserdata');

});