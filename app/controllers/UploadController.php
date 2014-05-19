<?php

class UploadController extends BaseController {
	public function image() {

		$validator = Validator::make(Input::all(), array(
				'image'=>'required|image'
			));

		if ($validator->fails()) {
			$result = array('code'=>-1, 'message'=>'업로드에 실패했습니다.');
			return View::make('upload-result', array('result'=>$result));
		}

		$fileName = str_random(40).'.'.Input::file('image')->getClientOriginalExtension();
		$filePath = public_path('uploads/'.$fileName);
		
		Image::make(Input::file('image')->getRealPath())
				->resize(800, 800, true, false)
				->save($filePath);
		
		$target = Input::get('target');
		$result = array(
			'code'=>0, 
			'message'=>'', 
			'url'=>url('uploads/'.$fileName),
			'target'=>$target
		);

		return View::make('upload-result', array('result'=>$result));
	}
}
