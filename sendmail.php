<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//Takes all the messages in the `tiki_mail_queue` table and sends them using SMTP.
//This script can be run from command line on schedule.

error_reporting(E_ALL);
ini_set("display_errors", 'stdout');

require_once ("tiki-setup.php");
require_once ("lib/mail/maillib.php");

tiki_mail_setup();
echo ("Mail queue processor starting...\n");

$query = "SELECT messageId, message FROM `tiki_mail_queue`";
$messages = $tikilib->fetchAll($query);

foreach ( $messages as $message ) {

    echo("Sending message ".$message["messageId"]."...");

    $mail = unserialize($message["message"]);
    if ($mail) {
        try {
  	        $mail->send();
            $title = 'mail';
  	    } catch (Zend_Mail_Exception $e) {
  			$title = 'mail error';
  		}

  		if ($title == 'mail error' || $prefs['log_mail'] == 'y') {
  			foreach ($recipients as $u) {
  				$logslib->add_log($title, $u . '/' . $mail->getSubject());
  			}
  		}

  		if ($title == 'mail error') {
  			$query = "UPDATE `tiki_mail_queue` SET attempts = attempts + 1 WHERE messageId = ?";
  			echo ("Failed.\n");
  			print_r($mailer->errors);
  			echo ("\n");
  		} else {
  			$query = "DELETE FROM `tiki_mail_queue` WHERE messageId = ?";
  			echo ("Sent.\n");
  		}

  		$tikilib->query($query, array($message["messageId"]));
    } else {
        echo ("ERROR: Unable to unserialize the mailer object\n");
    }

}
echo ("Mail queue processed...\n");
