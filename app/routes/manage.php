<?php 

Route::group(array('prefix'=>'manage', 'before'=>'auth'), function(){

    Route::get('/', 'ManageController@displayDashboard');

    Route::get('reports', 'ManageController@displayReportUsers');
    Route::get('budgets', 'ManageController@displayBudgetUsers');

});