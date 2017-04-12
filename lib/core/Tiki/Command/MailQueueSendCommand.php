<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
      require_once ('lib/mail/maillib.php');
      global $prefs;
      $logslib = TikiLib::lib('logs');
      tiki_mail_setup();
      $output->writeln('Mail queue processor starting...');

      $messages = \TikiDb::get()->fetchAll('SELECT messageId, message FROM tiki_mail_queue');

      foreach ( $messages as $message ) {

          $output->writeln('Sending message '.$message['messageId'].'...');
          $mail = unserialize($message['message']);
		  $error = '';

          if ($mail && get_class($mail) === 'Zend\Mail\Message') {
            try {
                tiki_send_email($mail);
                $title = 'mail';
            } catch (\Zend\Mail\Exception\ExceptionInterface $e) {
                $title = 'mail error';
				$error = $e->getMessage();
            }

            if ($error || $prefs['log_mail'] == 'y') {
                foreach($mail->getTo() as $destination){
                    $logslib->add_log($title, $error . "\n " . $destination->getEmail() . '/' . $mail->getSubject());
                }
                foreach($mail->getCc() as $destination){
                    $logslib->add_log($title, $error . "\n " . $destination->getEmail() . '/' . $mail->getSubject());
                }
                foreach($mail->getBcc() as $destination){
                    $logslib->add_log($title, $error . "\n " . $destination->getEmail() . '/' . $mail->getSubject());
                }
            }

            if ($error) {
            	$query = 'UPDATE tiki_mail_queue SET attempts = attempts + 1 WHERE messageId = ?';
            	$output->writeln('Failed sending mail object id: ' . $message['messageId'] . ' (' . $error . ')');
            } else {
            	$query = 'DELETE FROM tiki_mail_queue WHERE messageId = ?';
            	$output->writeln('Sent.');
            }

            \TikiDb::get()->query($query, array($message['messageId']));
          } else {
              $output->writeln('ERROR: Unable to unserialize the mail object id:' . $message['messageId']);
          }
      }
      $output->writeln('Mail queue processed...');
    }
}
