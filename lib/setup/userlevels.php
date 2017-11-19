<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__)) {
	die('This script may only be included.');
}

$mylevel = $tikilib->get_user_preference($user, 'mylevel', 1);
if (isset($_REQUEST['mylevel']) and isset($prefs['userlevels'][$_REQUEST['mylevel']]) and $user) {
	$tikilib->set_user_preference($user, 'mylevel', $_REQUEST['mylevel']);
	$mylevel = $_REQUEST['mylevel'];
}
$smarty->assign('mylevel', $mylevel);
