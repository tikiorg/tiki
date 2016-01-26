<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

// If we are processing a login then do not generate the challenge
// if we are in any other case then yes.
if ( ! isset($_REQUEST['login']) ) {
	$chall = $userlib->generate_challenge();

	$_SESSION['challenge'] = $chall;
	$smarty->assign('challenge', $chall);
}

