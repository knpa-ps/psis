<?php 

Route::group(array('prefix'=>'user'), function(){
    Route::get('profile', array('uses'=>'UserController@showProfile'));
    Route::get('profile_edit', 'UserController@showProfileEdit');
    Route::post('contact_mod', 'UserController@contactMod');
});