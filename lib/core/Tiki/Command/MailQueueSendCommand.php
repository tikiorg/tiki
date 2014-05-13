<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: MailInPollCommand.php 50599 2014-03-31 21:22:59Z lphuberdeau $

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
error_reporting(E_ALL);
use TikiLib;

class MailQueueSendCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mail-queue:send')
            ->setDescription('Send the messages stored in the Mail Queue');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      require_once ("lib/mail/maillib.php");
      global $prefs;
      $logslib = TikiLib::lib('logs'); 
      tiki_mail_setup();
      echo ("Mail queue processor starting...\n");

      $messages = \TikiDb::get()->fetchAll("SELECT messageId, message FROM `tiki_mail_queue`");

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
            	foreach ($mail->getRecipients() as $u) {
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

            \TikiDb::get()->query($query, array($message["messageId"]));
          } else {
              echo ("ERROR: Unable to unserialize the mailer object\n");
          }
      }
      echo ("Mail queue processed...\n");
    }
}
