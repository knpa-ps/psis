<?php 

Route::group(array('prefix'=>'reports', 'before'=>'auth'), function(){

    Route::get('/', array('before'=>'permission:report.read', 'uses'=>'ReportController@displayDashboard'));
    Route::get('/create', array('before'=>'permission:report.create', 'uses'=>'ReportController@displayComposeForm'));
	Route::get('/list', array('before'=>'permission:report.read', 'uses'=>'ReportController@displayList'));
	
});