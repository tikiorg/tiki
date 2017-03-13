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

$imagegallib = TikiLib::lib('imagegal');

if ($check === true) {
	if (isset($_REQUEST['galfeatures'])) {
		// Check for last character being a / or a \
		// My next commit is to create a clas to put this code into
		if (substr($_REQUEST['gal_use_dir'], -1) != '\\'
			&& substr($_REQUEST['gal_use_dir'], -1) != '/'
			&& $_REQUEST['gal_use_dir'] != ''
		) {
			$_REQUEST['gal_use_dir'] .= '/';
		}

		if (substr($_REQUEST['gal_batch_dir'], -1) != '\\'
			&& substr($_REQUEST['gal_batch_dir'], -1) != '/'
			&& $_REQUEST['gal_batch_dir'] != ''
		) {
			$_REQUEST['gal_batch_dir'] .= '/';
		}
	}

	if (isset($_REQUEST['rmvorphimg'])) {
		$adminlib->remove_orphan_images();
		Feedback::success(tra('Orphan images successfully removed'), 'session');
	}

	if (isset($_REQUEST['mvimg']) && isset($_REQUEST['move_gallery'])) {
		if (($_REQUEST['mvimg'] == 'to_fs' && $prefs['gal_use_db'] == 'n')
			|| ($_REQUEST['mvimg'] == 'to_db' && $prefs['gal_use_db'] == 'y')
		) {
			$mvresult = $imagegallib->move_gallery_store($_REQUEST['move_gallery'], $_REQUEST['mvimg']);
			$mvmsg = sprintf(tra('moved %d images, %d errors occurred.'), $mvresult['moved_images'], $mvresult['errors']);
			if ($mvresult['timeout']) {
				$mvmsg.= ' ' . tra('a timeout occurred. Hit the reload button to move the rest');
			}
			Feedback::note($mvmsg, 'session');
		}
	}
}

$galleries = $imagegallib->list_visible_galleries(0, -1, 'name_desc', 'admin', '');
$smarty->assign_by_ref('galleries', $galleries['data']);
$smarty->assign('max_img_upload_size', $imagegallib->max_img_upload_size());
