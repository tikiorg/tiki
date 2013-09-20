<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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

		// Pseudonymise e-mail
		$output->writeln('<comment>Pseudonymising user e-mails.</comment>');
		$query = "	SELECT DISTINCT table_name
				FROM information_schema.columns
				WHERE column_name = 'email'
				AND table_name <> 'users_users'
				AND table_schema = '$dbs_tiki';";
		$result = $tikilib->query($query);
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		foreach ($ret as $table) {
			unset($bindvars);
			$output->writeln('<info>'.$table['table_name'].'</info>');
			$query = "UPDATE ".$table['table_name']." t, users_users u SET t.email = CONCAT('user', u.userId, '@example.com') WHERE t.email = u.email AND u.login <> 'admin';";
			$bindvars = array ( $table['table_name'] );
			$result = $tikilib->query($query, $bindvars);
		}

		// Pseudonymise user name
		$output->writeln('<comment>Pseudonymising user names.</comment>');
		$query = "	SELECT DISTINCT table_name
				FROM information_schema.columns
				WHERE column_name = 'user'
				AND table_name <> 'users_users'
				AND table_schema = '$dbs_tiki';";
		$result = $tikilib->query($query);
		while ($res = $result->fetchRow()) {
			unset($ret);
			$ret[] = $res;
		}
		foreach ($ret as $table) {
			unset($bindvars);
			$output->writeln('<info>'.$table['table_name'].'</info>');
			$query = "UPDATE ".$table['table_name']." t, users_users u SET t.user = CONCAT('user', u.userId) WHERE t.user = u.login AND u.login <> 'admin';";
			$bindvars = array ( $table['table_name'] );
			$result = $tikilib->query($query, $bindvars);
		}

		// Final user pseudonymisation in users_users
		$query = "SELECT count(userId) FROM users_users;";
		$result = $tikilib->query($query);
		$res = $result->fetchRow();
		$num = (int)$res['count(userId)'];
		for ($i = 2; $i <= $num; $i++) {
			$query = "UPDATE `users_users` SET `email` = ?, `login` = ?, `hash`=md5( ? ) WHERE `userId` = ?";
			$bindvars = array("user$i@example.com", "user$i", "pass$i", $i);
			$query = $tikilib->query($query, $bindvars);
			// TODO : Update user avatars
		}

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
		$output->writeln('<info>Removing session data.</info>');
		$query = "TRUNCATE TABLE tiki_payment_received;";
		$result = $tikilib->query($query);
		$query = "TRUNCATE TABLE tiki_payment_requests;";
		$result = $tikilib->query($query);

		$output->writeln('<comment>Read the disclaimer!</comment>');
		$output->writeln('<comment>The following means jack:</comment>');
		$output->writeln('<info>Finished redacting database.</info>');
	}
}
