<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-remote_backup.php,v 1.9 2007-03-06 19:29:51 sylvieg Exp $
// Call with eg.
// http://localhost/tiki/tiki-remote_backup.php?generate=1&my_word=ThisIsMySecretBackupWord"
// PLEASE UNCOMMENT THIS LINE TO ACTIVATE REMOTE BACKUPS (DISABLED IN THE DISTRIBUTION)
die;
require_once ('tiki-setup.php');
include_once ('lib/backups/backupslib.php');
if (isset($_REQUEST["generate"])) {
	if (isset($_REQUEST["my_word"]) && $_REQUEST["my_word"] == "YOUR PASSWORD FOR BACKUPS HERE") {
		$filename = md5($tikilib->genPass()) . '.sql';
		$backuplib->backup_database("backups/$tikidomain/$filename");
		echo "Done";
	}
}
die;
