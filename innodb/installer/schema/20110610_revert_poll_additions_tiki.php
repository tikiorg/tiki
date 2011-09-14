<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20110610_revert_poll_additions_tiki( $installer )
{
	$installer->query( "DROP TABLE IF EXISTS `tiki_poll_votes`" );
	$result = $installer->fetchAll( "SHOW COLUMNS FROM `tiki_polls` WHERE `Field`='anonym'");

	if ($result) {
		$result = $installer->query( "ALTER TABLE `tiki_polls` DROP COLUMN `anonym`;" );
	}
}
