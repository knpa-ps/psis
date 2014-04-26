<?php 

Route::group(array('prefix'=>'reports', 'before'=>'auth'), function(){

    Route::get('/', array('before'=>'permission:report.read', 'uses'=>'ReportController@displayDashboard'));
    Route::get('/create', array('before'=>'permission:report.create', 'uses'=>'ReportController@displayComposeForm'));
	Route::get('/list', array('before'=>'permission:report.read', 'uses'=>'ReportController@displayList'));
	
    Route::get('/edit', 'ReportController@displayEditForm');

    Route::post('/create', 'ReportController@createReport');
    Route::post('/edit', 'ReportController@editReport');
	Route::post('/delete', 'ReportController@deleteReport');
});