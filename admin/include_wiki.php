<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	die('This script may only be included.');
}
require_once ('tiki-setup.php');

global $tikidomain;
$path = $tikidomain ? "storage/$tikidomain/dump_wiki.tar" : 'storage/dump_wiki.tar';

if ($access->ticketMatch()) {
	if (!empty($_REQUEST['w_use_dir'])) {
		if (substr($_REQUEST['w_use_dir'], -1) != '\\' && substr($_REQUEST['w_use_dir'], -1) != '/') {
			$_REQUEST['w_use_dir'] .= '/';
		}
		simple_set_value('w_use_dir');
	}

	if (isset($_REQUEST['createdump'])) {
		include ('lib/tar.class.php');
		error_reporting(E_ERROR | E_WARNING);
		$adminlib->dump();
		if (is_file($path)) {
			Feedback::success(tr('Dump created at %0', '<em>' . $path . '</em>'), 'session');
		} else {
			Feedback::error(tra('Dump was not created. Please check permissions for the storage/ directory.'), 'session');
		}
	}

	if (!empty($_REQUEST['moveWikiUp'])) {
		$filegallib = TikiLib::lib('filegal');
		$errorsWikiUp = array();
		$info = $filegallib->get_file_gallery_info($prefs['home_file_gallery']);
		if (empty($info)) {
			Feedback::error(tr('You must set a home file gallery'), 'session');
		} else {
			$filegallib->moveAllWikiUpToFgal($prefs['home_file_gallery']);
		}
	}

// Included for the forum dropdown
	if (isset($_REQUEST['createtag'])) {
		// Check existence
		if ($adminlib->tag_exists($_REQUEST['newtagname'])) {
			Feedback::error(tra('Tag already exists'), 'session');
		}
		$adminlib->create_tag($_REQUEST['newtagname']);
		Feedback::success(tr('Tag %0 created.', '<em>' . $_REQUEST['newtagname'] . '</em>'), 'session');
	}

	if (isset($_REQUEST['removedump'])) {
		@unlink($path);
		if (!is_file($path)) {
			Feedback::success(tr('Dump file %0 removed.', '<em>' . $path . '</em>'), 'session');
		} else {
			Feedback::error(tr('Dump file %0 was not removed.', '<em>' . $path . '</em>'), 'session');
		}
	}

	if (isset($_REQUEST['downloaddump'])) {
		global $tikidomain;
		// Check existence
		if ($tikidomain) {
			$file = "storage/$tikidomain/dump_wiki.tar";
		}else {
			$file = "storage/dump_wiki.tar";
		}

		if (is_file($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}
	}

	if (isset($_REQUEST['restoretag'])) {
		// Check existance
		if (!$adminlib->tag_exists($_REQUEST['tagname'])) {
			Feedback::error(tr('Tag %0 not found', '<em>' . $_REQUEST['tagname'] . '</em>'), 'session');
		}
		$result = $adminlib->restore_tag($_REQUEST['tagname']);
		if ($result) {
			Feedback::success(tr('Tag %0 restored.', '<em>' . $_REQUEST['tagname'] . '</em>'), 'session');
		} else {
			Feedback::error(tr('Tag %0 not restored.', '<em>' . $_REQUEST['tagname'] . '</em>'), 'session');
		}
	}

	if (isset($_REQUEST['removetag'])) {
		$result = $adminlib->remove_tag($_REQUEST['tagname']);
		if ($result) {
			Feedback::success(tr('Tag %0 removed.', '<em>' . $_REQUEST['tagname'] . '</em>'), 'session');
		} else {
			Feedback::error(tr('Tag %0 not removed.', '<em>' . $_REQUEST['tagname'] . '</em>'), 'session');
		}
	}

	if (isset($_REQUEST['rmvunusedpic'])) {
		$adminlib->remove_unused_pictures();
		Feedback::success(tr('Process to remove pictures has completed.'), 'session');
	}

	if ((isset($_REQUEST['feature_backlinks']) && $_REQUEST['feature_backlinks'] == 'on' && $prefs['feature_backlinks'] == 'y')
		|| (empty($_REQUEST['feature_backlinks']) && $prefs['feature_backlinks'] == 'y')
	) {
		$backlinksChange = true;
	}

	if (isset($backlinksChange) && $backlinksChange) {
		$wikilib = TikiLib::lib('wiki');
		$wikilib->refresh_backlinks();
	}
}

$smarty->assign('isDump', is_file($path));
$tags = $adminlib->get_tags();
$smarty->assign_by_ref('tags', $tags);
