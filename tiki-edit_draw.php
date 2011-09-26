<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
$section = "draw";
require_once ('tiki-setup.php');
include_once ('lib/filegals/filegallib.php');

$access->check_feature('feature_draw');
$access->check_feature('feature_file_galleries');

include_once ("categorize_list.php");
include_once ('tiki-section_options.php');
include_once ('lib/mime/mimetypes.php');

ask_ticket('draw');

$_REQUEST['fileId'] = (int)$_REQUEST['fileId'];
$smarty->assign('fileId', $_REQUEST['fileId']);

if ($_REQUEST['fileId'] > 0) {
	$fileInfo = $filegallib->get_file_info( $_REQUEST['fileId'] );
} else {
	$fileInfo = array();
}
$gal_info = $filegallib->get_file_gallery( $_REQUEST['galleryId'] );

if ( ($fileInfo['filetype'] != $mimetypes["svg"]) && $_REQUEST['fileId'] > 0 ) {
	$smarty->assign('msg', tr("Wrong file type, expected %0", $mimetypes["svg"]));
	$smarty->display("error.tpl");
	die;
}

$globalperms = Perms::get( array( 'type' => 'file galleries', 'object' => $fileInfo['galleryId'] ) );

//check permissions
if (!($globalperms->admin_file_galleries == 'y' || $globalperms->view_file_gallery == 'y')) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to view/edit this file"));
	$smarty->display("error.tpl");
	die;
}

if (!empty($_REQUEST['name']) || !empty($fileInfo['name'])) {
	$_REQUEST['name'] = (!empty($_REQUEST['name']) ? $_REQUEST['name'] : $fileInfo['name']);
} else {
	$_REQUEST['name'] = "New Svg Image";
}

$_REQUEST['name'] = htmlspecialchars(str_replace(".svg", "", $_REQUEST['name']));

//Upload to file gallery
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['data'])) {
	$_REQUEST["galleryId"] = (int)$_REQUEST["galleryId"];
	$_REQUEST["fileId"] = (int)$_REQUEST["fileId"];
	$_REQUEST['description'] = htmlspecialchars(isset($_REQUEST['description']) ? $_REQUEST['description'] : $_REQUEST['name']);
	
	$type = $mimetypes["svg"];
	$fileId = '';
	if (empty($_REQUEST["fileId"]) == false) {
		//existing file
		$fileId = $filegallib->save_archive($_REQUEST["fileId"], $fileInfo['galleryId'], 0, $_REQUEST['name'], $fileInfo['description'], $_REQUEST['name'].".svg", $_REQUEST['data'], strlen($_REQUEST['data']), $type, $fileInfo['user'], null, null, $user, date());
	} else {
		//new file
		$fileId = $filegallib->insert_file($_REQUEST["galleryId"], $_REQUEST['name'], $_REQUEST['description'], $_REQUEST['name'].".svg", $_REQUEST['data'], strlen($_REQUEST['data']), $type, $user, date());
	}
	
	echo $fileId;
	die;
}

$smarty->assign( "data", $fileInfo["data"] );
//Obtain fileId, DO NOT LET ANYTHING OTHER THAN NUMBERS BY (for injection free code)
if (is_numeric($_REQUEST['fileId']) == false) $_REQUEST['fileId'] = 0; 
if (is_numeric($_REQUEST['galleryId']) == false) $_REQUEST['galleryId'] = 0;

$fileId = htmlspecialchars($_REQUEST['fileId']);
$galleryId = htmlspecialchars($_REQUEST['galleryId']);
$name = htmlspecialchars($_REQUEST['name']);

$index = htmlspecialchars($_REQUEST['index']);
$page = htmlspecialchars($_REQUEST['page']);
$label = htmlspecialchars($_REQUEST['label']);
$width = htmlspecialchars($_REQUEST['width']);
$height = htmlspecialchars($_REQUEST['height']);

$smarty->assign( "page", $page );
$smarty->assign( "isFromPage", isset($page) );

$backLocation = ($page ? "tiki-index.php?page=$page" : "tiki-list_file_gallery.php?galleryId=$galleryId");

$smarty->assign( "fileId", $fileId );
$smarty->assign( "galleryId", $galleryId );
$smarty->assign( "width", $width );
$smarty->assign( "height", $width );
$smarty->assign( "name", $name);

$headerlib->add_jsfile("lib/svg-edit/embedapi.js");
$headerlib->add_jsfile("lib/svg-edit_tiki/draw.js");
$headerlib->add_cssfile("lib/svg-edit_tiki/draw.css");

if (
	isset($_REQUEST['index']) &&
	isset($_REQUEST['page']) && 
	isset($_REQUEST['label'])
) {
	$headerlib->add_jq_onready("	
		$.wikiTrackingDraw = {
			index: '$index',
			page: '$page',
			label: '$label',
			type: 'draw',
			content: '',
			'params[width]': $height,
			'params[height]': $width
		};
	");
}

$headerlib->add_jq_onready("
	$('#tiki-draw_fullscreen').click(function() {
		$('#tiki_draw').drawFullscreen();
	});
	
	$('#svgedit').loadDraw({
		fileId: $('#svg_file_id').val(),
		galleryId: $('#svg_gallery_id').val(),
		name: $('#svg_file_name').val(),
		data: $('#svg-data').html()
	});
	
	$('#tiki-draw_rename').click(function() {
		$('#svg_file_name').val($('#svgedit').renameDraw());
	});
	
	$('#tiki-draw_save').click(function() {
		$('#svgedit').saveDraw();
	});
	
	$('#tiki-draw_back').click(function() {
		document.location = '$backLocation';
	});
");

if (isset($_REQUEST['map'])) {
	require_once("lib/wiki-plugins/wikiplugin_map.php");
	
	echo wikiplugin_map();
	
	$headerlib->add_jq_onready("
		$('#openlayers1').drawOver({
			draw: $('#svgedit'),
			type: 'map'
		});
	");
}
// Display the template
$smarty->assign('mid', 'tiki-edit_draw.tpl');
// use tiki_full to include include CSS and JavaScript
$smarty->display("tiki.tpl");
