<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__)) {
	die('This script may only be included.');
}

if ( $prefs['mailin_autocheck'] == 'y' ) {
  if ((time() - $prefs['mailin_autocheckLast'])/60 > $prefs['mailin_autocheckFreq']) {
    $tikilib->set_preference('mailin_autocheckLast', time());
    include_once('tiki-mailin-code.php');
  }
}
