<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if ( $user ) {
	$creditslib = TikiLib::lib('credits');
	if (!isset($prefs['account_suspended']) || $prefs['account_suspended'] != 'y') {
		$tiki_user_credits = $creditslib->getScaledCredits($tikilib->get_user_id($user));
	} else {
		$tiki_user_credits = 0;
	}
	$smarty->assign('tiki_user_credits', $tiki_user_credits);
}
