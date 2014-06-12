<?php 

Route::group(array('prefix'=>'reports', 'before'=>'auth'), function(){

    Route::get('/', array('before'=>'permission:reports.read', 'uses'=>'ReportController@displayDashboard'));
    Route::get('/create', array('before'=>'permission:reports.create', 'uses'=>'ReportController@displayComposeForm'));
	Route::get('/list', array('before'=>'permission:reports.read', 'uses'=>'ReportController@displayList'));
	
    Route::get('/edit', 'ReportController@displayEditForm');

    Route::post('/create', 'ReportController@createReport');
    Route::post('/edit', 'ReportController@editReport');
	Route::post('/delete', 'ReportController@deleteReport');

	Route::get('/drafts', 'ReportController@displayDraftsList');
	Route::get('/drafts/{id}', 'ReportController@getDraft')->where('id', '[0-9]+');
	Route::post('/drafts/save', 'ReportController@saveDraft');
	Route::post('/drafts/delete', 'ReportController@deleteDraft');

	Route::get('/templates', 'ReportController@displayTemplatesList');
	Route::get('/templates/{id}', 'ReportController@getTemplate')->where('id', '[0-9]+');
	Route::post('/templates/save', 'ReportController@saveTemplate');
	Route::post('/templates/delete', 'ReportController@deleteTemplate');
	Route::post('/templates/set_default', 'ReportController@setDefault');
});