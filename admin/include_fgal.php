<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

$filegallib = TikiLib::lib('filegal');

if ($access->ticketMatch()) {
	// Check for last character being a / or a \
	if (substr($_REQUEST["fgal_use_dir"], -1) != "\\" && substr($_REQUEST["fgal_use_dir"], -1) != "/" && $_REQUEST["fgal_use_dir"] != "") {
		$_REQUEST["fgal_use_dir"].= "/";
	}
	// Check for last character being a / or a \
	if (substr($_REQUEST["fgal_podcast_dir"], -1) != "\\" && substr($_REQUEST["fgal_podcast_dir"], -1) != "/" && $_REQUEST["fgal_podcast_dir"] != "") {
		$_REQUEST["fgal_podcast_dir"].= "/";
	}
	if (substr($_REQUEST["fgal_batch_dir"], -1) != "\\" && substr($_REQUEST["fgal_batch_dir"], -1) != "/" && $_REQUEST["fgal_batch_dir"] != "") {
		$_REQUEST["fgal_batch_dir"].= "/";
	}
	simple_set_value("fgal_use_dir");
	simple_set_value("fgal_podcast_dir");
	simple_set_value("fgal_batch_dir");
	if (!empty($_REQUEST['fgal_quota']) && !empty($_REQUEST['fgal_quota_default']) && $_REQUEST['fgal_quota_default'] > $_REQUEST['fgal_quota']) {
		$_REQUEST['fgal_quota_default'] = $_REQUEST['fgal_quota'];
	}
	simple_set_value('fgal_quota_default');
	if (!empty($_REQUEST['updateMime'])) {
		$files = $filegallib->table('tiki_files');
		$rows = $files->fetchAll(array('fileId', 'filename', 'filetype'), array('archiveId' => 0, 'filetype' => 'application/octet-stream'));
		foreach ($rows as $row) {
			$t = $filegallib->fixMime($row);
			if ($t != 'application/octet-stream') {
				$files->update(array('filetype' => $t), array('fileId' => $row['fileId']));
			}
		}
	}

	if (!empty($_REQUEST['move'])) {
		if ($_REQUEST['move'] == 'to_fs') {
			if (empty($prefs['fgal_use_dir'])) {
				$errors[] = tra('You must specify a directory');
			} else {
				$feedbacks = array();
				$errors = $filegallib->moveFiles($_REQUEST['move'], $feedbacks);
			}
		} elseif ($_REQUEST['move'] == 'to_db') {
			$feedbacks = array();
			$errors = $filegallib->moveFiles($_REQUEST['move'], $feedbacks);
		}
		if (!empty($errors)) {
			Feedback::error(['mes' => $errors], 'session');
		}
		if (!empty($feedbacks)) {
			Feedback::note(['mes' => $feedbacks], 'session');
		}
	}

	if (!empty($_REQUEST['mimes'])) {
		$mimes = $_REQUEST['mimes'];
		foreach ($mimes as $mime => $cmd) {
			$mime = trim($mime);
			if (empty($cmd)) {
				$filegallib->delete_file_handler($mime);
			} else {
				$filegallib->change_file_handler($mime, $cmd);
			}
		}
	}
	if (!empty($_REQUEST['newMime']) && !empty($_REQUEST['newCmd'])) {
		$filegallib->change_file_handler($_REQUEST['newMime'], $_REQUEST['newCmd']);
	}
	if (isset($_REQUEST["filegalredosearch"])) {
		$filegallib->reindex_all_files_for_search_text();
	}

	if (isset($_REQUEST["filegalfixvndmsfiles"])) {
		$filegallib->fix_vnd_ms_files();
	}
}

if ($prefs['fgal_viewerjs_feature'] === 'y') {
	$viewerjs_err = '';
	if (empty($prefs['fgal_viewerjs_uri'])) {

		$viewerjs_err = tra('ViewerJS URI not set');

	} else if (strpos($prefs['fgal_viewerjs_uri'], '://') === false) {	// local install

		if (! is_readable($prefs['fgal_viewerjs_uri'])) {
			$viewerjs_err = tr('ViewerJS URI not found (local file not readable)');
		}

	} else {												// remote (will take a while)

		$file_headers = get_headers(TikiLib::lib('access')->absoluteUrl($prefs['fgal_viewerjs_uri']));
		if (strpos($file_headers[0], '200') === false) {
			$viewerjs_err = tr('ViewerJS URI not found (%0)', $file_headers[0]);
		}
	}

	$smarty->assign('viewerjs_err', $viewerjs_err);
}

$usedSize = $filegallib->getUsedSize();
$smarty->assign_by_ref('usedSize', $usedSize);

$handlers = $filegallib->get_file_handlers();
ksort($handlers);
$smarty->assign("fgal_handlers", $handlers);
$usedTypes = $filegallib->getFiletype();
$missingHandlers = array();
$vnd_ms_files_exist = false;

foreach ($usedTypes as $type) {
	if (! $filegallib->get_parse_app($type, true)) {
		$missingHandlers[] = $type;
		if (strpos($type, '/vnd.ms-') !== false) {
			$vnd_ms_files_exist = true;
		}
	}
}

$smarty->assign_by_ref('missingHandlers', $missingHandlers);
$smarty->assign('vnd_ms_files_exist', $vnd_ms_files_exist);
include_once ('fgal_listing_conf.php');
