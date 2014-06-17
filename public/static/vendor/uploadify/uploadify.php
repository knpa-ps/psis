<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
$targetFolder = '/psis/public/uploads'; // Relative to the root

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	$fileName = $_FILES['Filedata']['name'];
	$targetFile = rtrim($targetPath,'/') . '/' . iconv('utf-8', 'euc-kr', $fileName);
	
	// Validate the file type
	$fileTypes = array('php', 'php3', 'html', 'htm'); // File extensions
	$fileParts = pathinfo($fileName);
	
	if (!in_array($fileParts['extension'],$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		echo $fileName;
	} else {
		echo 'cannot upload php, php3, html, htm files.';
	}
}
?>