<?php 

Route::group(array('prefix'=>'budgets', 'before'=>'auth'), function(){

    Route::get('/', 'BudgetController@displayDashboard');
    Route::get('/meal-cost/data', 'BgtMealCostController@read');
    Route::resource('/meal-cost', 'BgtMealCostController');
    
    Route::get('/mob-pay/data', 'BgtMobPayController@read');
    Route::resource('/mob-pay', 'BgtMobPayController');
});