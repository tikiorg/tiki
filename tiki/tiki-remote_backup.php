<?php
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
        $backuplib->backup_database("backups/$filename");
        echo "Done";
    }
}

die;
?>
