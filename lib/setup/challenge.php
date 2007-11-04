<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/challenge.php,v 1.1.2.1 2007-11-04 22:08:34 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

// If we are processing a login then do not generate the challenge
// if we are in any other case then yes.
if ( ! strstr($_SERVER['REQUEST_URI'], 'tiki-login') ) {
	$chall = $userlib->generate_challenge();

	$_SESSION['challenge'] = $chall;
	$smarty->assign('challenge', $chall);
}

