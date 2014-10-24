<?php 

Route::group(array('prefix'=>'upload'), function(){
	Route::post('image', 'UploadController@image');
	Route::post('image/ckeditor', 'UploadController@imageCkeditor');
	Route::post('capsaicin_doc', 'UploadController@capsaicinDoc');
});