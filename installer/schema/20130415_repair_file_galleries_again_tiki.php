<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * prefs for some system gallery roots have become out of sync somehow historically - this attempts to repair them
 *
 * @param $installer
 */
function upgrade_20130415_repair_file_galleries_again_tiki($installer)
{
	// first user gals
	$id = $installer->getOne("SELECT `galleryId` FROM `tiki_file_galleries` WHERE `type` = 'system' AND `name` = 'Users File Galleries'");
	$pref = $installer->getOne("SELECT `value` FROM `tiki_preferences` WHERE `name` = 'fgal_root_user_id'");
	if ($pref != $id) {
		if ($pref) {
			$installer->query("UPDATE `tiki_preferences` SET `value` = ? WHERE `name` = 'fgal_root_user_id';", $id);
		} else {
			$installer->query("INSERT INTO `tiki_preferences` (`name`, `value`) VALUES ('fgal_root_user_id', ? );", $id);
		}
		$installer->query("UPDATE `tiki_file_galleries` SET `parentId` = ? WHERE `type` = 'user';", $id);
	}
	// than wiki attachments
	$id = $installer->getOne("SELECT `galleryId` FROM `tiki_file_galleries` WHERE `type` = 'system' AND `name` = 'Wiki Attachments'");
	$pref = $installer->getOne("SELECT `value` FROM `tiki_preferences` WHERE `name` = 'fgal_root_wiki_attachments_id'");
	if ($pref != $id) {
		if ($pref) {
			$installer->query("UPDATE `tiki_preferences` SET `value` = ? WHERE `name` = 'fgal_root_wiki_attachments_id';", $id);
		} else {
			$installer->query("INSERT INTO `tiki_preferences` (`name`, `value`) VALUES ('fgal_root_wiki_attachments_id', ? );", $id);
		}
		$installer->query("UPDATE `tiki_file_galleries` SET `parentId` = ? WHERE `type` = 'attachments';", $id);
	}
}

