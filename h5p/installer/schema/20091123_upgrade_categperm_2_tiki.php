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
function upgrade_20091123_upgrade_categperm_2_tiki($installer)
{
/* second pass at upgrading version 3 category perms to v4 (for 4.1)
 * jonnyb 23 nov 2009
 * 
 * goals for part 2
 * Assign tiki_p_modify_object_categories where feature_categories is on and tiki_p_edit (or $edit array similar to part 1?)
 * Rename tiki_p_*_categorized descriptions as deprecated (done apart from search)
 */
	
	// $edit describes what was supposed to be given by tiki_p_edit_categorized
	// this time use a subset of it to assign tiki_p_modify_object_categories

	global $installer;
	
	$query = "SELECT `name`, `value` FROM `tiki_preferences` WHERE `name`='feature_categories'";
	@$result = $installer->query($query);

	$feature_cats = false;
	if ($result) {
		while ($res = $result->fetchRow()) {
			if (isset($res['value']) && $res['value'] == 'y') {
				$feature_cats = true;
			}
		}
	}
	
	
	if ($feature_cats) {	// only relevant if categories are enabled

		$edit[] = 'tiki_p_modify_tracker_items';
		$edit[] = 'tiki_p_create_tracker_items';
		$edit[] = 'tiki_p_modify_tracker_items_pending';
		$edit[] = 'tiki_p_modify_tracker_items_closed';
		
		//$edit[] = 'tiki_p_upload_images'; // can upload doesn't necessarily mean can categorise
		//$edit[] = 'tiki_p_upload_files';
		
		$edit[] = 'tiki_p_edit_article';
		//$edit[] = 'tiki_p_submit_article';
		
		//$edit[] = 'tiki_p_forum_post_topic';	// forum posts cannot be categorised
		//$edit[] = 'tiki_p_forum_post';
		$edit[] = 'tiki_p_admin_forum';			// but forums can
		
		$edit[] = 'tiki_p_create_blogs';
		$edit[] = 'tiki_p_blog_post';
		
		$edit[] = 'tiki_p_edit';
		//$edit[] = 'tiki_p_wiki_attach_files';
		
		//$edit[] = 'tiki_p_add_events';		// events cannot be categorised
		//$edit[] = 'tiki_p_change_events';
		$edit[] = 'tiki_p_admin_calendar';		// but calendars can
		
		$editString = implode('\',\'', $edit);
		
		// first for group perms
		$query = 'SELECT * FROM `users_grouppermissions` WHERE `permName` IN (\'' . $editString . '\')';
		$test = 'SELECT COUNT(*) FROM `users_grouppermissions` WHERE `permName` = \'tiki_p_modify_object_categories\' AND `groupName`=?';
		$insert = 'INSERT IGNORE into `users_grouppermissions` (`permName`, `groupName`) values (\'tiki_p_modify_object_categories\',?)';
		
		// add the perm tiki_p_modify_object_categories where  
		$result = $installer->query($query);
		while ($res = $result->fetchRow()) {
			if (!$installer->getOne($test, array($res['groupName']))) {
				$installer->query($insert, array($res['groupName']));
			}
		}
		
		// then for object perms
		$query = 'SELECT * FROM `users_objectpermissions` WHERE `permName` IN (\'' . $editString . '\')';
		$test = 'SELECT COUNT(*) FROM `users_objectpermissions` WHERE `permName` = \'tiki_p_modify_object_categories\' AND `groupName`=? AND `objectType`=? AND `objectId`=?';
		$insert = 'INSERT IGNORE into `users_objectpermissions` (`permName`, `groupName`, `objectType`, `objectId`) values (\'tiki_p_modify_object_categories\',?,?,?)';
		
		// add the perm tiki_p_modify_object_categories where  
		$result = $installer->query($query);
		while ($res = $result->fetchRow()) {
			if (!$installer->getOne($test, array($res['groupName'], $res['objectType'], $res['objectId']))) {
				$installer->query($insert, array($res['groupName'], $res['objectType'], $res['objectId']));
			}
		}
		
		// tiki_p_search_categorized wasn't dealt with in the previous script...
		// view ones for search from part 2 script
		$view[] = 'tiki_p_view_trackers';
		$view[] = 'tiki_p_view_image_gallery';
		$view[] = 'tiki_p_download_files';
		$view[] = 'tiki_p_view_file_gallery';
		$view[] = 'tiki_p_view_fgal_explorer';
		$view[] = 'tiki_p_view_fgal_path';
		$view[] = 'tiki_p_read_article';
		$view[] = 'tiki_p_forum_read';
		$view[] = 'tiki_p_read_blog';
		$view[] = 'tiki_p_view';
		$view[] = 'tiki_p_wiki_view_attachments';
		$view[] = 'tiki_p_wiki_view_history';
		$view[] = 'tiki_p_wiki_view_comments';
		$view[] = 'tiki_p_view_faqs';
		$view[] = 'tiki_p_subscribe_newsletters';
		$view[] = 'tiki_p_view_calendar';
		$view[] = 'tiki_p_view_events';
		$view[] = 'tiki_p_view_tiki_calendar';
		$view[] = 'tiki_p_view_directory';
		$view[] = 'tiki_p_view_freetags';
		$view[] = 'tiki_p_view_sheet';
		$view[] = 'tiki_p_view_shoutbox';
		$view[] = 'tiki_p_view_html_pages';
		$view[] = 'tiki_p_view_category';

		
		$query = 'SELECT * FROM `users_objectpermissions` WHERE `permName` = ?';
		$insert = 'INSERT IGNORE into `users_objectpermissions` (`permName`, `groupName`, `objectType`, `objectId`) values (?,?,?,?)';
		$test = 'SELECT COUNT(*) FROM `users_objectpermissions` WHERE `permName` = ? AND `groupName`=? AND `objectType`=? AND `objectId`=?';
		
		// replace the perm tiki_p_search_categorized with the adequate set of perms for the objects
		$result = $installer->query($query, array('tiki_p_search_categorized'));
		while ($res = $result->fetchRow()) {
			foreach ($view as $perm) {
				if (!$installer->getOne($test, array($perm, $res['groupName'], $res['objectType'], $res['objectId']))) {
					$installer->query($insert, array($perm, $res['groupName'], $res['objectType'], $res['objectId']));
				}
			}
		}
		// missed from 20091113_old_categ_perm_tiki.sql
		$installer->query('update ignore `users_permissions` set `permDesc`=\'Obsolete tw>=4 (Can search on objects of this category)\' where `permName`=\'tiki_p_search_categorized\';');
		
		
	}	// end if ($a_pref['feature_categories'])	
	
}
