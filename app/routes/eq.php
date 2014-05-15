<?php 

Route::group(array('prefix'=>'equips', 'before'=>'auth'), function(){
	Route::resource('/categories', 'EqCategoryController');
});