<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
		'data' => 'none',
	))
);

$section = "draw";
require_once ('tiki-setup.php');
global $drawFullscreen, $prefs;
$headerlib = TikiLib::lib('header');

$filegallib = TikiLib::lib('filegal');

$access->check_feature('feature_draw');
$access->check_feature('feature_file_galleries');

include_once ("categorize_list.php");
include_once ('tiki-section_options.php');
include_once ('lib/mime/mimetypes.php');
global $mimetypes;

ask_ticket('draw');

$_REQUEST['fileId'] = (int)$_REQUEST['fileId'];
$smarty->assign('fileId', $_REQUEST['fileId']);

if ($_REQUEST['fileId'] > 0) {
	$fileInfo = $filegallib->get_file_info($_REQUEST['fileId']);
	if (empty($_REQUEST['galleryId'])) {
		$_REQUEST['galleryId'] = $fileInfo['galleryId'];
	}
} else {
	$fileInfo = array();
}

//This allows the document to be edited, but only the most recent of that group if it is an archive
if (!empty($fileInfo['archiveId']) && $fileInfo['archiveId'] > 0) {
	$_REQUEST['fileId'] = $fileInfo['archiveId'];
	$fileInfo = $filegallib->get_file_info($_REQUEST['fileId']);
}

$gal_info = $filegallib->get_file_gallery($_REQUEST['galleryId']);

if (
	!(
		($fileInfo['filetype'] == $mimetypes["svg"]) ||
		($fileInfo['filetype'] == $mimetypes["gif"]) ||
		($fileInfo['filetype'] == $mimetypes["jpg"]) ||
		($fileInfo['filetype'] == $mimetypes["png"]) ||
		($fileInfo['filetype'] == $mimetypes["tiff"])
	) && $_REQUEST['fileId'] > 0 ) {
	$smarty->assign('msg', tr("Wrong file type, expected %0", $mimetypes["svg"]));
	$smarty->display("error.tpl");
	die;
}

$perms = TikiLib::lib('tiki')->get_perm_object( $gal_info['galleryId'], 'file gallery', $gal_info );

//check permissions
if ($perms['tiki_p_upload_files'] !== 'y' ) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to view/edit this file"));
	$smarty->display("error.tpl");
	die;
}

if (!empty($_REQUEST['name']) || !empty($fileInfo['name'])) {
	$_REQUEST['name'] = (!empty($_REQUEST['name']) ? $_REQUEST['name'] : $fileInfo['name']);
} else {
	$_REQUEST['name'] = (isset($_REQUEST['page']) ? $_REQUEST['page'] : tr("New Svg Image"));
}

$_REQUEST['name'] = htmlspecialchars(str_replace(".svg", "", $_REQUEST['name']));

//Upload to file gallery
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['data'])) {
	$_REQUEST["galleryId"] = (int)$_REQUEST["galleryId"];
	$_REQUEST["fileId"] = (int)$_REQUEST["fileId"];
	if (isset($_REQUEST['imgParams'])) {
		$_REQUEST['fromFieldId'] = (int)$_REQUEST['imgParams']['fromFieldId'];
		$_REQUEST['fromItemId'] = (int)$_REQUEST['imgParams']['fromItemId'];
	}
	$_REQUEST['description'] = htmlspecialchars(isset($_REQUEST['description']) ? $_REQUEST['description'] : '');

	$type = $mimetypes["svg"];
	$fileId = '';
	$isConversion = $fileInfo['filetype'] != $mimetypes["svg"];

	if (empty($_REQUEST["fileId"]) == false && $_REQUEST["fileId"] > 0 &&
			($prefs['feature_draw_separate_base_image'] !== 'y' || !$isConversion)) {

		//existing file
		$fileId = $filegallib->save_archive(
			$_REQUEST["fileId"],
			$fileInfo['galleryId'],
			0,
			$_REQUEST['name'],
			$fileInfo['description'],
			$_REQUEST['name'].".svg",
			$_REQUEST['data'],
			strlen($_REQUEST['data']),
			$type,
			$fileInfo['user'],
			null,
			null,
			$user
		);
		// this is a conversion from an image other than svg
		if ($isConversion && $prefs['fgal_keep_fileId'] == 'y') {
			$newFileInfo = $filegallib->get_file_info($fileId);

			$archiveFileId = $tikilib->getOne(
				'SELECT fileId
				FROM tiki_files
				WHERE archiveId = ?
				ORDER BY lastModif DESC',
				array($fileId)
			);

			$newFileInfo['data'] = str_replace(
				'?fileId=' . $fileInfo['fileId'] . '#',
				'?fileId=' . $archiveFileId . '#',
				$newFileInfo['data']
			);
			$fileId = $filegallib->save_archive(
				$newFileInfo["fileId"],
				$newFileInfo['galleryId'], 0,
				$newFileInfo['filename'],
				$newFileInfo['description'],
				$newFileInfo['name'].".svg",
				$newFileInfo['data'],
				strlen($newFileInfo['data']),
				$type,
				$newFileInfo['user'],
				null,
				null,
				$user
			);
		}
	} else {
		//new file
		if ($isConversion) {
			$_REQUEST['name'] = preg_replace('/\.(:?jpg|gif|png|tif[f]?)$/', '', $_REQUEST['name']) . tra(' drawing');	// strip extension
		}
		$galleryId = $_REQUEST["galleryId"];
		if ($prefs['feature_draw_in_userfiles'] === 'y') {
			$galleryId = TikiLib::lib('filegal')->get_user_file_gallery();
		}
		$fileId = $filegallib->insert_file(
			$galleryId,
			$_REQUEST['name'],
			$_REQUEST['description'],
			$_REQUEST['name'].".svg",
			$_REQUEST['data'],
			strlen($_REQUEST['data']),
			$type,
			$user,
			null
		);
	}

	if (!empty($_REQUEST['fromItemId'])) {		// a tracker item, so update the item field
		$item = Tracker_Item::fromId($_REQUEST['fromItemId']);
		if ($item->canModifyField($_REQUEST['fromFieldId'])) {
			$definition = $item->getDefinition();
			$field = $definition->getField($_REQUEST['fromFieldId']);
			$trackerInput = $item->prepareFieldInput($field, array($_REQUEST['fromFieldId'] -> $fileId));
			$fileIds = explode(',', $trackerInput['value']);
			if (!in_array($fileId, $fileIds)) {
				if (!empty($_REQUEST['fileId']) && $fileId != $_REQUEST['fileId']) {
					$old_index = array_search($_REQUEST['fileId'], $fileIds);			// replacement (id changed when drawn on)
				} else {
					$old_index = false;
				}
				if ($old_index !== false) {
					$fileIds[$old_index] = $fileId;
				} else {
					$fileIds[] = $fileId;
				}
			}
			$trackerInput['value'] = implode(',', $fileIds);

			TikiLib::lib('trk')->replace_item($field['trackerId'], $_REQUEST['fromItemId'], array('data' => array($trackerInput)));
		}

	}

	echo $fileId;
	die;
}

if ($fileInfo['filetype'] == $mimetypes["svg"]) {
	$data = $fileInfo["data"];
} else { //we already confirmed that this is an image, here we make it compatible with svg
	$src = $tikilib->tikiUrl() . 'tiki-download_file.php?fileId=' . $fileInfo['fileId'];
	$w = @imagesx($src);		// can't see how this can ever work - imagesx param is a resource not a string url (jb)
	$h = @imagesy($src);

	if (empty($w) || empty($h)) { //go ahead and download the image, it may exist off-site, copywrited content
		$image = imagecreatefromstring(file_get_contents($src));
		$w = imagesx($image);
		$h = imagesy($image);
	}

	if ($w == 0 && $h == 0) {
		$w = 640;
		$h = 480;
	}

	$data = '<svg width="' . $w . '" height="' . $h
		. '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
	<g>
		<title>Layer 1</title>
		<image x="1" y="1" width="100%" height="100%" id="svg_1" xlink:href="' . $src . '#image"/>
	</g>
</svg>';
}

//echo $data;die;
$smarty->assign("data", $data);
//Obtain fileId, DO NOT LET ANYTHING OTHER THAN NUMBERS BY (for injection free code)
if (is_numeric($_REQUEST['fileId']) == false) $_REQUEST['fileId'] = 0;
if (is_numeric($_REQUEST['galleryId']) == false) $_REQUEST['galleryId'] = 0;

$fileId = htmlspecialchars($_REQUEST['fileId']);
$galleryId = htmlspecialchars($_REQUEST['galleryId']);
$name = htmlspecialchars($_REQUEST['name']);
$archive = htmlspecialchars($_REQUEST['archive']);

$index = htmlspecialchars($_REQUEST['index']);
$page = htmlspecialchars($_REQUEST['page']);
$label = htmlspecialchars($_REQUEST['label']);
$width = htmlspecialchars($_REQUEST['width']);
$height = htmlspecialchars($_REQUEST['height']);

$smarty->assign("page", $page);
$smarty->assign("isFromPage", isset($page));

$backLocation = ($page ? "tiki-index.php?page=$page" : "tiki-list_file_gallery.php?galleryId=$galleryId");

$smarty->assign("fileId", $fileId);
$smarty->assign("galleryId", $galleryId);
$smarty->assign("width", $width);
$smarty->assign("height", $height);
$smarty->assign("name", $name);
$smarty->assign("archive", $archive);

$jsTracking = "$.wikiTrackingDraw = {
	index: '$index',
	page: '$page',
	label: '$label',
	type: 'draw',
	content: '',
	params: {
		width: '$width',
		height: '$height',
		id: '$fileId'
	}
};";

if (isset($_REQUEST['raw'])) {
	$jsFunctionality = "";
} else {
	$prefs['feature_draw_hide_buttons'] = addslashes(htmlentities($prefs['feature_draw_hide_buttons']));

	$jsFunctionality =
	"$('#drawFullscreen')
		.click(function() {
			$('#tiki_draw').drawFullscreen();
		})
		.click();

	$('#tiki_draw')
		.loadDraw({
			fileId: $('#fileId').val(),
			galleryId: $('#galleryId').val(),
			name: $('#fileName').val(),
			data: $('#fileData').val()
		})
		.bind('renamedDraw', function(e, name) {
			$('#fileName').val(name);
			$('.pagetitle').text(name);
		});

	$('#drawBack').click(function() {
		window.history.back();
	});";
}

if (
	isset($_REQUEST['index']) &&
	isset($_REQUEST['page']) &&
	isset($_REQUEST['label'])
) {
	$headerlib->add_jq_onready($jsTracking);
}

if ($drawFullscreen == true || isset($_REQUEST['raw'])) {
	echo $headerlib->output_js();
	$smarty->assign('drawFullscreen', 'true');
	echo $smarty->fetch('tiki-edit_draw.tpl');
} else {
	$headerlib->add_jq_onready($jsFunctionality);
	// Display the template
	$smarty->assign('mid', 'tiki-edit_draw.tpl');
	// use tiki_full to include include CSS and JavaScript
	$smarty->display("tiki.tpl");
}
