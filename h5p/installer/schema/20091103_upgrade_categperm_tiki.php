<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @param $installer
 */
function upgrade_20091103_upgrade_categperm_tiki($installer)
{
	// $view describes what was supposed to be given by tiki_p_view_categorized
	// $edit describes what was supposed to be given by tiki_p_edit_categorized
	// these lists are probably incomplete
	$view[] = 'tiki_p_view_trackers';
	$edit[] = 'tiki_p_modify_tracker_items';
	$edit[] = 'tiki_p_create_tracker_items';
	$edit[] = 'tiki_p_modify_tracker_items_pending';
	$edit[] = 'tiki_p_modify_tracker_items_closed';

	$view[] = 'tiki_p_view_image_gallery';
	$view[] = 'tiki_p_download_files';
	$edit[] = 'tiki_p_upload_images';

	$view[] = 'tiki_p_view_file_gallery';
	$view[] = 'tiki_p_view_fgal_explorer';
	$view[] = 'tiki_p_view_fgal_path';
	$edit[] = 'tiki_p_upload_files';

	$view[] = 'tiki_p_read_article';
	$edit[] = 'tiki_p_edit_article';
	$edit[] = 'tiki_p_submit_article';

	$view[] = 'tiki_p_forum_read';
	$edit[] = 'tiki_p_forum_post_topic';
	$edit[] = 'tiki_p_forum_post';

	$view[] = 'tiki_p_read_blog';
	$edit[] = 'tiki_p_create_blogs';
	$edit[] = 'tiki_p_blog_post';

	$view[] = 'tiki_p_view';
	$view[] = 'tiki_p_wiki_view_attachments';
	$view[] = 'tiki_p_wiki_view_history';
	$view[] = 'tiki_p_wiki_view_comments';
	$edit[] = 'tiki_p_edit';
	$edit[] = 'tiki_p_wiki_attach_files';

	$view[] = 'tiki_p_view_faqs';

	$view[] = 'tiki_p_subscribe_newsletters';

	$view[] = 'tiki_p_view_calendar';
	$view[] = 'tiki_p_view_events';
	$view[] = 'tiki_p_view_tiki_calendar';
	$edit[] = 'tiki_p_add_events';
	$edit[] = 'tiki_p_change_events';

	$view[] = 'tiki_p_view_directory';

	$view[] = 'tiki_p_view_freetags';

	$view[] = 'tiki_p_view_sheet';

	$view[] = 'tiki_p_view_shoutbox';

	$view[] = 'tiki_p_view_html_pages';

	$view[] = 'tiki_p_view_category';

	$query = 'SELECT * FROM `users_objectpermissions` WHERE `permName` = ?';
	$insert = 'INSERT IGNORE into `users_objectpermissions` (`permName`, `groupName`, `objectType`, `objectId`) values (?,?,?,?)';
	$test = 'SELECT COUNT(*) FROM `users_objectpermissions` WHERE `permName` = ? AND `groupName`=? AND `objectType`=? AND `objectId`=?';

	// replace the perm tiki_p_view_categorized with the adequate set of perms for the objects
	$result = $installer->query($query, array('tiki_p_view_categorized'));
	while ($res = $result->fetchRow()) {
		foreach ($view as $perm) {
			if (!$installer->getOne($test, array($perm, $res['groupName'], $res['objectType'], $res['objectId']))) {
				$installer->query($insert, array($perm, $res['groupName'], $res['objectType'], $res['objectId']));
			}
		}
	}

	// replace the perm tiki_p_edit_categorized with the adequate set of perms for the objects 
	$result = $installer->query($query, array('tiki_p_edit_categorized'));
	while ($res = $result->fetchRow()) {
		foreach ($edit as $perm) {
			if (!$installer->getOne($test, array($perm, $res['groupName'], $res['objectType'], $res['objectId']))) {
				$installer->query($insert, array($perm, $res['groupName'], $res['objectType'], $res['objectId']));
			}
		}
	}

	//rename tiki_p_view_categories to tiki_p_view_category
	$query = 'UPDATE IGNORE  `users_grouppermissions` SET `permName`=? WHERE `permName`=?';
	$installer->query($query, array('tiki_p_view_category', 'tiki_p_view_categories'));
	$query = 'UPDATE IGNORE  `users_objectpermissions` SET `permName`=? WHERE `permName`=?';
	$installer->query($query, array('tiki_p_view_category', 'tiki_p_view_categories'));
	$query = 'UPDATE IGNORE  `tiki_menu_options` SET `perm`=? WHERE `perm`=?';
	$installer->query($query, array('tiki_p_view_category', 'tiki_p_view_categories'));

	$query = 'UPDATE IGNORE  `users_grouppermissions` SET `permName`=? WHERE `permName`=?';
	$installer->query($query, array('tiki_p_modify_object_categories', 'tiki_p_edit_categorized'));
	$query = 'UPDATE IGNORE  `users_objectpermissions` SET `permName`=? WHERE `permName`=?';
	$installer->query($query, array('tiki_p_modify_object_categories', 'tiki_p_edit_categorized'));
	$query = 'UPDATE IGNORE  `tiki_menu_options` SET `perm`=? WHERE `perm`=?';
	$installer->query($query, array('tiki_p_modify_object_categories', 'tiki_p_edit_categorized'));

	// FINALLY: remove tiki_p_view_categorized and tiki_p_edit_categorized
	// Not done yet - before we are sure with have all the mapping

}
