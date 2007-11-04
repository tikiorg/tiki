<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/mailin.php,v 1.2.2.1 2007-11-04 22:08:34 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

if ( $prefs['mailin_autocheck'] == 'y' ) {
  if ((time() - $prefs['mailin_autocheckLast'])/60 > $prefs['mailin_autocheckFreq']) {
    $tikilib->set_preference('mailin_autocheckLast', time());
    include_once('tiki-mailin-code.php');
  }
}
