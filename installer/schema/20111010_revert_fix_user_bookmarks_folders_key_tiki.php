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
function upgrade_20111010_revert_fix_user_bookmarks_folders_key_tiki($installer)
{
	$installer->query("ALTER TABLE `tiki_user_bookmarks_folders` MODIFY `folderId` int(12) NOT NULL, DROP PRIMARY KEY");
	$result = $installer->fetchAll("SHOW INDEX FROM `tiki_user_bookmarks_folders` WHERE `Key_name`='user'");

	if ($result) {
		$result = $installer->query("DROP INDEX `user` ON `tiki_user_bookmarks_folders`");
	}
	$installer->query("ALTER TABLE `tiki_user_bookmarks_folders` ADD PRIMARY KEY (`user`,`folderId`)");
}
