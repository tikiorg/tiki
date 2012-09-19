<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//Takes all the messages in the `tiki_mail_queue` table and sends them using SMTP.
//This script can be run from command line on schedule.

error_reporting(E_ALL);
ini_set("display_errors", 'stdout');

require_once ("tiki-setup.php");
require_once ("lib/webmail/htmlMimeMail.php");

echo ("Mail queue processor starting...\n");

if ($prefs['zend_mail_handler'] == 'smtp') {

	$params = array();
	$params["host"] = $prefs['zend_mail_smtp_server'];
	$params["port"] = $prefs['zend_mail_smtp_port'];
	$params["helo"] = $prefs['zend_mail_smtp_helo'];
	$params["user"] = $prefs['zend_mail_smtp_user'];
	$params["pass"] = $prefs['zend_mail_smtp_pass'];
	$params["security"] = $prefs['zend_mail_smtp_security'];

	if ($prefs['zend_mail_smtp_auth'] == 'login') {
		$params["auth"] = true;
	} else {
		$params["auth"] = false;
	}

	echo ("Connecting to the mail server...");

	$mailer   = new smtp($params);

	if (!$mailer->connect()) {
		echo ("Failed.");
		print_r($smtp->errors);
		echo ("\n");
		die;
	} else {
		echo ("Connected!\n");
	}

	$query = "SELECT messageId, message FROM `tiki_mail_queue`";
	$messages = $tikilib->fetchAll($query);

	foreach ( $messages as $message ) {
		echo("Sending message ".$message["messageId"]."...");

		if (!$mailer->send(json_decode($message["message"]))) {
			$query = "UPDATE `tiki_mail_queue` SET attempts = attempts + 1 WHERE messageId = ?";
			echo ("Failed.\n");
			print_r($mailer->errors);
			echo ("\n");
		} else {
			$query = "DELETE FROM `tiki_mail_queue` WHERE messageId = ?";
			echo ("Sent.\n");
		}

		$tikilib->query($query, array($message["messageId"]));

	}
}
echo ("Mail queue processed...\n");
