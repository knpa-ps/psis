<?php

class FileController extends BaseController {

	public function download()
	{
		$fid = Input::get('id');

		$file = PSFile::where('id','=',$fid)->first();

		if (!$file)
		{
			return App::abort(400, "no file found");
		}



		$filepath = $file->path;
		$filesize = $file->size;
		$filename = $file->name;
		if( $this->is_ie() ) 
		{
			$filename = $this->utf2euc($filename);
		}
		 
		header("Pragma: public");
		header("Expires: 0");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: $filesize");
		 
		readfile($filepath);
	}

	function mb_basename($path) { return end(explode('/',$path)); } 
	function utf2euc($str) { return iconv("UTF-8","cp949//IGNORE", $str); }
	function is_ie() { return isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false; }
 
}
