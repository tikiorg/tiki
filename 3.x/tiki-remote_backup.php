<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-remote_backup.php,v 1.9 2007-03-06 19:29:51 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Call with eg.
// http://localhost/tiki/tiki-remote_backup.php?generate=1&my_word=ThisIsMySecretBackupWord"

// PLEASE UNCOMMENT THIS LINE TO ACTIVATE REMOTE BACKUPS (DISABLED IN THE DISTRIBUTION)
die;

require_once('tiki-setup.php');
include_once('lib/backups/backupslib.php');
if(isset($_REQUEST["generate"])) {
    if(isset($_REQUEST["my_word"]) &&
       $_REQUEST["my_word"] == "YOUR PASSWORD FOR BACKUPS HERE" ) {
        $filename = md5($tikilib->genPass()).'.sql';
        $backuplib->backup_database("backups/$tikidomain/$filename");
        echo "Done";
    }
}

die;

?>
