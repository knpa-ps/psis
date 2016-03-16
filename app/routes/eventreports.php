<?php

Route::group(array('prefix'=>'eventreports'), function(){
  Route::get('/', array('before'=>'permission:eventreport.read', 'uses'=>'EventReportController@displayDashboard'));
  Route::get('/create', array('before'=>'permission:eventreport.create', 'uses'=>'EventReportController@displayComposeForm'));
	Route::get('/list', array('before'=>'permission:eventreport.read', 'uses'=>'EventReportController@displayList'));
  Route::get('/edit', 'EventReportController@displayEditForm');

  Route::post('/create', 'EventReportController@createReport');
  Route::post('/edit', 'EventReportController@editReport');
	Route::post('/delete', 'EventReportController@deleteReport');

  Route::get('/drafts', 'EventReportController@displayDraftsList');
  Route::get('/drafts/{id}', 'EventReportController@getDraft')->where('id', '[0-9]+');
  Route::post('/drafts/save', 'EventReportController@saveDraft');
  Route::post('/drafts/delete', 'EventReportController@deleteDraft');

  Route::get('/templates', 'EventReportController@displayTemplatesList');
  Route::get('/templates/{id}', 'EventReportController@getTemplate')->where('id', '[0-9]+');
  Route::post('/templates/save', 'EventReportController@saveTemplate');
  Route::post('/templates/delete', 'EventReportController@deleteTemplate');
  Route::post('/templates/set_default', 'EventReportController@setDefault');
});
