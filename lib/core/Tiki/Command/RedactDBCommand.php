<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: InstallCommand.php 45724 2013-04-26 17:33:23Z changi67 $

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RedactDBCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('database:redact')
			->setDescription('Redact database')
			->addOption(
				'force',
				null,
				InputOption::VALUE_NONE,
				'Force installation. Overwrite any current database.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// Not used, just a reminder
		$force = $input->getOption('force');

		// Here we go
		$tikilib = \TikiLib::lib('tiki');

		// For $dbs_tiki - better way?
		require('db/redact/local.php');

		$output->writeln('<info>Redacting database.</info>');

		// Reset admin account
		$output->writeln('<info>Resetting admin account.</info>');
		$query = "UPDATE users_users SET email = ? WHERE login='admin';";
		$bindvars = array('admin@example.com');
		$result = $tikilib->query($query, $bindvars);
		$query = "UPDATE `users_users` SET `password`='admin', `hash`= md5('admin') WHERE `login`='admin'";
		$result = $tikilib->query($query);

		// first get valid prefix
		$userprefix = 'user_';
		$userprefixready = false;
		$userprefixindex = 0;
		while (!$userprefixready) {
			$query = "SELECT count(*) FROM users_users WHERE login LIKE '".$userprefix."%';";
			$result = $tikilib->getOne($query);
			if ($result > 0) {
				$userprefixindex++;
				$userprefix = 'user'.$userprefixindex.'_';
			} else {
				$userprefixready = true;
			}
		}
		$output->writeln('<comment>Using user names like '.$userprefix.'123.</comment>');

		// Pseudonymise e-mail
		$output->writeln('<comment>Pseudonymising user e-mails.</comment>');
		$query = "	SELECT DISTINCT table_name
				FROM information_schema.columns
				WHERE column_name = 'email'
				AND table_name <> 'users_users'
				AND table_schema = '$dbs_tiki';";
		$result = $tikilib->query($query);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		foreach ($ret as $table) {
			unset($bindvars);
			$output->writeln('<info>'.$table['table_name'].'</info>');
			$query = "UPDATE ".$table['table_name']." t, users_users u SET t.email = CONCAT('".$userprefix."', u.userId, '@example.com') WHERE t.email = u.email AND u.login <> 'admin';";
			$result = $tikilib->query($query);
			$query = "SET @newnum:=0;UPDATE ".$table['table_name']." t SET t.email = CONCAT('emailchanged', @newnum:=@newnum+1, '@example.com') WHERE t.email NOT LIKE '".$userprefix."%@example.com';";
			$result = $tikilib->query($query);
		}

		// Pseudonymise user name
		$output->writeln('<comment>Pseudonymising user names.</comment>');
		$query = "	SELECT DISTINCT table_name
				FROM information_schema.columns
				WHERE column_name = 'user'
				AND table_name <> 'users_users'
				AND table_schema = '$dbs_tiki';";
		$result = $tikilib->query($query);
		$ret = array();	
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		foreach ($ret as $table) {
			unset($bindvars);
			$output->writeln('<info>'.$table['table_name'].'</info>');
			$query = "UPDATE ".$table['table_name']." t, users_users u SET t.user = CONCAT('".$userprefix."', u.userId) WHERE t.user = u.login AND u.login <> 'admin';";
			$result = $tikilib->query($query);
		}

		// Pseudonymise user selector tracker fields
		$output->writeln('<comment>Pseudonymising user selector tracker fields.</comment>');
		$query = "SELECT fieldId, trackerId, name FROM tiki_tracker_fields WHERE type='u';";
		$result = $tikilib->query($query);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		foreach ($ret as $field) {
			unset($bindvars);
			$output->writeln('<info>Tracker '.$field['trackerId'].' Field '.$field['fieldId'].': '.$field['name'].'</info>');
			$trackername = $tikilib->getOne('SELECT name FROM tiki_trackers WHERE trackerId = '.$field['trackerId'].';'); 
			$output->writeln('<comment>Consider removing data from Tracker '.$field['trackerId'].' ('.$trackername.').</comment>');
			$query = "UPDATE tiki_tracker_item_fields t, users_users u SET t.value = CONCAT('".$userprefix."', u.userId) WHERE t.value = u.login AND u.login <> 'admin';";
			$result = $tikilib->query($query);
		}

		// Final user pseudonymisation in users_users
		$query = "SELECT MAX(userId) FROM users_users;";
		$num = $tikilib->getOne($query);
		for ($i = 2; $i <= $num; $i++) {
			$query = "UPDATE `users_users` SET `email` = ?, `login` = ?, `hash`=md5( ? ) WHERE `userId` = ?";
			$bindvars = array("$userprefix$i@example.com", "$userprefix$i", "pass$i", $i);
			$result = $tikilib->query($query, $bindvars);
			// TODO : Update user avatars
		}
		$query = "UPDATE `users_users` SET `provpass` = '';";
		$result = $tikilib->query($query);		
		$query = "UPDATE `users_users` SET `password` = '';";
		$result = $tikilib->query($query);

		// Remove user web-mail accounts
		$output->writeln('<info>Removing user mail accounts.</info>');
		$query = "TRUNCATE TABLE tiki_user_mail_accounts;";
		$result = $tikilib->query($query);
		$output->writeln('<info>Removing mail queue.</info>');
		$query = "TRUNCATE TABLE tiki_mail_queue;";
		$result = $tikilib->query($query);

		// Remove messu_messages
		$output->writeln('<info>Removing user messu.</info>');
		$query = "TRUNCATE TABLE messu_messages;";
		$result = $tikilib->query($query);

		// Remove all session data
		$output->writeln('<info>Removing session data.</info>');
		$query = "TRUNCATE TABLE sessions;";
		$result = $tikilib->query($query);
		$query = "TRUNCATE TABLE tiki_cookies;";
		$result = $tikilib->query($query);
		$query = "TRUNCATE TABLE tiki_sessions;";
		$result = $tikilib->query($query);

		// Remove payments
		$output->writeln('<info>Removing payments data.</info>');
		$query = "TRUNCATE TABLE tiki_payment_received;";
		$result = $tikilib->query($query);
		$query = "TRUNCATE TABLE tiki_payment_requests;";
		$result = $tikilib->query($query);

		// Remove DSN and mailin
		$output->writeln('<info>Removing DSN and mailin account data.</info>');
		$query = "TRUNCATE TABLE tiki_dsn;";
		$result = $tikilib->query($query);
		$query = "TRUNCATE TABLE tiki_mailin_accounts;";
		$result = $tikilib->query($query);

		// Remove auth tokens
		$output->writeln('<info>Removing auth tokens.</info>');
		$query = "TRUNCATE TABLE tiki_auth_tokens;";
		$result = $tikilib->query($query);

		// Remove web services
		$output->writeln('<info>Removing webservices info.</info>');
		$query = "TRUNCATE TABLE tiki_webservice;";
		$result = $tikilib->query($query);

		// Remove google, intertiki, ldap and 3rd party data
		$output->writeln('<info>Removing google, intertiki, ldap and other 3rd party app data.</info>');
		$query = "DELETE FROM tiki_preferences WHERE " .
			"name LIKE 'auth_ldap_%' OR " .
			"name LIKE '%key' OR " .
			"name LIKE '%secret' OR " .
			"name LIKE '%secr' OR " .
			"name LIKE '%client_id' OR " .
			"name LIKE '%application_id' OR " .
			"name LIKE '%access_token%' OR " .
			"name LIKE '%salt' OR " .
			"name = 'registerPasscode' OR " .
			"name = 'interlist' OR " .
			"name LIKE '%intertiki%';";
		$result = $tikilib->query($query);

		$output->writeln('<comment>Read the disclaimer!</comment>');
		$output->writeln('<comment>The following means jack:</comment>');
		$output->writeln('<info>Finished redacting database.</info>');
	}
}
