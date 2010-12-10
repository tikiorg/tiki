<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20091004_last_tracker_items_modules_merge_tiki.php 25197 2010-02-13 22:45:59Z pkdille $

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20101207_unique_login_tiki( $installer )
{
	$result = $installer->query( "select count(*) nb from users_users having count(*) > 1" );
	$row = $result->fetchRow();

	if (intval($row['nb']) == 0) {
		$result = $installer->query( "alter table users_users add unique login (login)" );
	}
}
