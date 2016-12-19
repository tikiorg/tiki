<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
$section = 'docs';

require_once ('tiki-setup.php');
$filegallib = TikiLib::lib('filegal');
include_once ('lib/mime/mimetypes.php');
global $mimetypes;

$auto_query_args = array(
	'fileId',
	'edit'
);

$access->check_feature('feature_docs');
$access->check_feature('feature_file_galleries');

ask_ticket('docs');

$fileId = (int)$_REQUEST['fileId'];
$smarty->assign('fileId', $fileId);

if ($fileId > 0) {
	$fileInfo = $filegallib->get_file_info($fileId);
} else {
	$fileInfo = array();
}

//This allows the document to be edited, but only the most recent of that group if it is an archive
if (!empty($fileInfo['archiveId']) && $fileInfo['archiveId'] > 0) {
	$fileId = $fileInfo['archiveId'];
	$fileInfo = $filegallib->get_file_info($fileId);
}

$cat_type = 'file';
$cat_objid = (int) $fileId;
$cat_object_exists = ! empty($fileInfo);
include_once ('categorize_list.php');
include_once ('tiki-section_options.php');

$gal_info = $filegallib->get_file_gallery($_REQUEST['galleryId']);

$fileType = reset(explode(';', $fileInfo['filetype']));
$extension = end(explode('.', $fileInfo['filename']));
$supportedExtensions = array('odt', 'ods', 'odp');
$supportedTypes = array_map(
	function ($type) use ($mimetypes) {
		return $mimetypes[$type];
	},
	$supportedExtensions
);

if (! in_array($extension, $supportedExtensions) && ! in_array($fileType, $supportedTypes)) {
	$smarty->assign('msg', tr('Wrong file type, expected one of %0', implode(', ', $supportedTypes)));
	$smarty->display('error.tpl');
	die;
}

$globalperms = Perms::get(array( 'type' => 'file galleries', 'object' => $fileInfo['galleryId'] ));

//check permissions
if (!($globalperms->admin_file_galleries == 'y' || $globalperms->view_file_gallery == 'y')) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('You do not have permission to view/edit this file'));
	$smarty->display('error.tpl');
	die;
}

if (!empty($_REQUEST['name']) || !empty($fileInfo['name'])) {
	$_REQUEST['name'] = (!empty($_REQUEST['name']) ? $_REQUEST['name'] : $fileInfo['name']);
} else {
	$_REQUEST['name'] = 'New Doc';
}

$_REQUEST['name'] = htmlspecialchars(str_replace('.odt', '', $_REQUEST['name']));

//Upload to file gallery
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['data'])) {
	$_REQUEST['galleryId'] = (int)$_REQUEST['galleryId'];
	$_REQUEST['description'] = htmlspecialchars(isset($_REQUEST['description']) ? $_REQUEST['description'] : $_REQUEST['name']);

	//webodf has to send an encoded string so that all browsers can handle the post-back
	$_REQUEST['data'] = base64_decode($_REQUEST['data']);

	$type = $mimetypes['odt'];
	if (! empty($fileId)) {
		//existing file
		$fileId = $filegallib->save_archive(
			$fileId,
			$fileInfo['galleryId'],
			0,
			$_REQUEST['name'],
			$fileInfo['description'],
			$_REQUEST['name'] . '.odt',
			$_REQUEST['data'],
			strlen($_REQUEST['data']),
			$type,
			$fileInfo['user'],
			null,
			null,
			$user,
			date()
		);
	} else {
		//new file
		$fileId = $filegallib->insert_file(
			$_REQUEST['galleryId'],
			$_REQUEST['name'],
			$_REQUEST['description'],
			$_REQUEST['name'] . '.odt',
			$_REQUEST['data'],
			strlen($_REQUEST['data']),
			$type,
			$user,
			date()
		);
	}

	echo $fileId;
	die;
}


$smarty->assign('page', $page);
$smarty->assign('isFromPage', isset($page));
$smarty->assign('fileId', $fileId);

$headerlib->add_jsfile('vendor_extra/webodf/webodf.js');

$savingText = json_encode(tr('Saving...'));

$headerlib->add_jq_onready(
    "window.odfcanvas = new odf.OdfCanvas($('#tiki_doc')[0]);
	odfcanvas.load('tiki-download_file.php?fileId=' + $('#fileId').val());

	//make editable
	$('.editButton').click(function() {
		odfcanvas.setEditable(true);

		$('.editState,.viewState').toggle();

		return false;
	});

	runtime.writeFile = function(path, data) {
		$.tikiModal($savingText);
		var base64 = new core.Base64();
		data = base64.convertUTF8ArrayToBase64(data);
		$.post('tiki-edit_docs.php', {
			fileId: $('#fileId').val(),
			data: data
		}, function(id) {
				$.tikiModal();
				$('#fileId').val(id);
		});
	};

	$('.saveButton').click(function() {
		odfcanvas.save();
		return false;
	});"
);

if (isset($_REQUEST['edit'])) {
	$smarty->assign('edit', 'true');
	$headerlib->add_jq_onready('odfcanvas.setEditable(true);');
} else {
	$smarty->assign('edit', 'false');
}


// Display the template
$smarty->assign('mid', 'tiki-edit_docs.tpl');
// use tiki_full to include include CSS and JavaScript
$smarty->display('tiki.tpl');
