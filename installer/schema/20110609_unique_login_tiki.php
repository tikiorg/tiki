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

function upgrade_20110609_unique_login_tiki( $installer )
{
	$result = $installer->query( "select count(*) nb from users_users having count(*) > 1" );
	$row = $result->fetchRow();

	if (intval($row['nb']) == 0) {
		$result = $installer->query( "drop index login on users_users" );
		$result = $installer->query( "alter table users_users add unique login (login)" );
	}
}
