<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/** filegal_uploader: Adds a widget to the page to upload files
 *
 * @param array $params
 *     'galleryId' => int	file gallery to upload into by default
 *
 * @param Smarty $smarty
 * @return string html
 */
function smarty_function_filegal_uploader($params, $smarty)
{
	$headerlib = TikiLib::lib('header');

//	Image loader and canvas libs
	$headerlib->add_jsfile('vendor/blueimp/javascript-load-image/js/load-image.all.min.js');
	$headerlib->add_jsfile('vendor/blueimp/javascript-canvas-to-blob/js/canvas-to-blob.min.js');

//	The Iframe Transport is required for browsers without support for XHR file uploads
	$headerlib->add_jsfile('vendor/blueimp/jquery-file-upload/js/jquery.iframe-transport.js');
//	The basic File Upload plugin
	$headerlib->add_jsfile('vendor/blueimp/jquery-file-upload/js/jquery.fileupload.js');
//	The File Upload processing plugin
	$headerlib->add_jsfile('vendor/blueimp/jquery-file-upload/js/jquery.fileupload-process.js');
//	The File Upload image preview & resize plugin
	$headerlib->add_jsfile('vendor/blueimp/jquery-file-upload/js/jquery.fileupload-image.js');
//	The File Upload audio preview plugin
	$headerlib->add_jsfile('vendor/blueimp/jquery-file-upload/js/jquery.fileupload-audio.js');
//	The File Upload video preview plugin
	$headerlib->add_jsfile('vendor/blueimp/jquery-file-upload/js/jquery.fileupload-video.js');
//	The File Upload validation plugin
	$headerlib->add_jsfile('vendor/blueimp/jquery-file-upload/js/jquery.fileupload-validate.js');
//	The File Upload user interface plugin
	$headerlib->add_jsfile('vendor/blueimp/jquery-file-upload/js/jquery.fileupload-ui.js');
// CSS
	$headerlib->add_cssfile('vendor/blueimp/jquery-file-upload/css/jquery.fileupload.css');
	$headerlib->add_cssfile('vendor/blueimp/jquery-file-upload/css/jquery.fileupload-ui.css');

//	Tiki customised application script
	$headerlib->add_jsfile('lib/jquery_tiki/tiki-jquery_upload.js');


	$return = $smarty->fetch('file/jquery_upload.tpl');

	return $return;
}
