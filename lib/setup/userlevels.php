<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/userlevels.php,v 1.2.2.1 2007-11-04 22:08:35 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

$mylevel = $tikilib->get_user_preference($user,'mylevel',1);
if ( isset($_REQUEST['level']) and isset($prefs['userlevels'][$_REQUEST['level']]) and $user ) {
	$tikilib->set_user_preference($user,"mylevel",$_REQUEST['level']);
	$mylevel = $_REQUEST['level'];
}
$smarty->assign('mylevel',$mylevel);
