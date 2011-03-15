<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/live_support/lsadminlib.php');
$access->check_feature('feature_live_support');
$smarty->assign('sent', 'n');
$smarty->assign('nomsg', 'y');
if (isset($_REQUEST['save'])) {
	$lsadminlib->post_support_message($_REQUEST['username'], $user, $_REQUEST['user_email'], $_REQUEST['data'], $_REQUEST['priority'], $_REQUEST['module'], 'o', '');
	$smarty->assign('sent', 'y');
}
if ($user) {
	$smarty->assign('user_email', $userlib->get_user_email($user));
}
$smarty->assign('modules', $lsadminlib->get_modules());
// Display the template
$smarty->display("tiki-live_support_client.tpl");
