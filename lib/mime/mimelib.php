<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// returns mimetypes of files
function tiki_get_mime($filename) {
	if (function_exists("mime_content_type")) {
		//notice: this is the better way. 
		//Compile php with --enable-mime-magic
		//to be able to use this.
		return mime_content_type($filename);
	} else {
		//The "Microsoft Way" - just kidding
		$defaultmime = "application/octet-stream";

		include_once ("lib/mime/mimetypes.php");
		$filesplit = preg_split("/\.+/", $filename, -1, PREG_SPLIT_NO_EMPTY);
		$ext = $filesplit[count($filesplit) - 1];

		if (isset($mimetypes[$ext])) {
			return $mimetypes[$ext];
		} else {
			return $defaultmime;
		}
	}
}

//returns "image" from image/jpeg
function tiki_get_mime_main($filename) {
	$filesplit = preg_split("#/+#", tiki_get_mime($filename));

	return $filesplit["0"];
}

//returns "jpeg" from image/jpeg
function tiki_get_mime_sub($filename) {
	$filesplit = preg_split("#/+#", tiki_get_mime($filename));

	return $filesplit["1"];
}

?>
