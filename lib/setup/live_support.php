<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/live_support.php,v 1.1.2.1 2007-11-04 22:08:34 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

$smarty->assign('user_is_operator', 'n');
if ( $user ) {
	include_once('lib/live_support/lsadminlib.php');
	if ( $lsadminlib->is_operator($user) ) {
		$smarty->assign('user_is_operator', 'y');
	}
}
