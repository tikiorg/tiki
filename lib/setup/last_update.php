<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/last_update.php,v 1.1.2.1 2007-11-04 22:08:34 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

/* trick for use with doc/devtools/cvsup.sh */
if ( is_file('.lastup') and is_readable('.lastup') ) {
	$lastup = file('.lastup');
	$smarty->assign('lastup', $lastup[0]);
}
