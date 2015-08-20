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
		$filePath = public_path('uploads');

		// Image::make(Input::file('image')->getRealPath())
		// 		->resize(800, 800, true, false)
		// 		->save($filePath);
		Input::file('image')->move($filePath, $fileName);

		$target = Input::get('target');
		$result = array(
			'code'=>0,
			'message'=>'',
			'url'=>url('uploads/'.$fileName),
			'target'=>$target
		);

		return View::make('upload-result', array('result'=>$result));
	}

	public function document() {
		$validator = Validator::make(Input::all(), array(
				'doc'=>'required'
		));

		if ($validator->fails()) {
			$result = array('code'=>-1, 'message'=>'업로드에 실패했습니다.');
			return View::make('upload-result', array('result'=>$result));
		}

		$fileName = str_random(15).'.'.Input::file('doc')->getClientOriginalExtension();
		$filePath = public_path('uploads/docs');

		Input::file('doc')->move($filePath, $fileName);

		$result = array('code'=>0,
						'message'=>'업로드 되었습니다.',
						'fileName'=>$fileName);

		return View::make('upload-result', array('result'=>$result));
	}

	public function imageCkeditor() {
		$validator = Validator::make(Input::all(), array(
				'upload'=>'required|image'
			));

		if ($validator->fails()) {
			return "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction('".Input::get('CKEditorFuncNum')."', '', '업로드 실패')</script>";
		}

		$fileName = str_random(40).'.'.Input::file('upload')->getClientOriginalExtension();
		$filePath = public_path('uploads/'.$fileName);

		Image::make(Input::file('upload')->getRealPath())
				->resize(800, 800, true, false)
				->save($filePath);

		$url = url('uploads/'.$fileName);

		return "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction('".Input::get('CKEditorFuncNum')."', '$url', '업로드 성공')</script>";
	}
}
