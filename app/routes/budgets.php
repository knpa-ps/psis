<?php 

Route::group(array('prefix'=>'budgets', 'before'=>'auth'), function(){

    Route::get('/', 'BudgetController@displayDashboard');
    Route::get('/meal-cost/data', 'BgtMealCostController@read');
    Route::get('/meal-cost/download', 'BgtMealCostController@download');
    Route::resource('/meal-cost', 'BgtMealCostController');
   
    Route::get('/mob-pay/data', 'BgtMobPayController@read');
    Route::get('/mob-pay/download', 'BgtMobPayController@download');
    Route::resource('/mob-pay', 'BgtMobPayController');
});