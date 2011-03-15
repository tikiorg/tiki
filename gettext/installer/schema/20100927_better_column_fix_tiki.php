<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id $

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20100927_better_column_fix_tiki( $installer )
{
	global $dbs_tiki;

	$result = $installer->getOne( "SELECT COUNT(*) FROM information_schema.COLUMNS 
												WHERE COLUMN_NAME='show_lastDownload' AND TABLE_NAME='tiki_file_galleries' AND TABLE_SCHEMA='".$dbs_tiki."';" );
	if ($result == 0) {
		$result = $installer->query('ALTER TABLE `tiki_file_galleries` ADD COLUMN  `show_lastDownload` char(1) default NULL AFTER `show_hits`;');
	}
	$result = $installer->getOne( "SELECT COUNT(*) FROM information_schema.COLUMNS 
												WHERE COLUMN_NAME='tweetId' AND TABLE_NAME='tiki_shoutbox' AND TABLE_SCHEMA='".$dbs_tiki."';" );
	if ($result == 0) {
		$result = $installer->query('ALTER TABLE `tiki_shoutbox` ADD COLUMN `tweetId` bigint(20) unsigned NOT NULL AFTER `hash`;');
	}
}
