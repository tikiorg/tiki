<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');

$access->check_feature('validateUsers');

if (!empty($_REQUEST['user'])) {
	$user_info = $userlib->get_user_info($_REQUEST['user']);
	$userlib->send_validation_email($user_info['login'], $user_info['valid'], $user_info['email']);
	$smarty->assign('msg', tra('An email has been sent to you with the instructions to follow.'));
} else {
	$smarty->assign('msg', tra("The mail can't be sent. Contact the administrator"));
}
$smarty->assign('mid', 'tiki-information.tpl');
$smarty->display('tiki.tpl');
	
