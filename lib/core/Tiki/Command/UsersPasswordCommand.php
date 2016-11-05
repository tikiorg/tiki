<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: IndexRebuildCommand.php 59965 2016-10-12 19:21:30Z rjsmelo $

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UsersPasswordCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('users:password')
			->setDescription('Set the password to a given user')
			->addOption(
				'force',
				'f',
				InputOption::VALUE_NONE,
				'Force set password'
			)
			->addArgument(
				'username',
				InputArgument::REQUIRED,
				'User login name'
			)
			->addArgument(
				'password',
				InputArgument::REQUIRED,
				'User new password'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

		global $prefs;

		$userlib = \TikiLib::lib('user');

		$user = $input->getArgument('username');

		if (!$userlib->user_exists($user)) {
			$output->writeln("<error>User {$user} does not exist.</error>");
			exit(1);
		}

		$password = $input->getArgument('password');

		// Check password constraints
		$polerr = $userlib->check_password_policy($password);
		if (!empty($polerr)) {
			$output->writeln("<error>{$polerr}</error>");
			exit(1);
		}

		if ($prefs['auth_method'] != 'tiki'){
			$output->writeln("<info>\nWarning: Tiki authentication method set to: <options=bold>" . $prefs['auth_method'] . "</>\n"
			. "Depending on the settings for this authentication method, \n"
			. "this change of the local password might not be enough for the user to be able to login</info>"
			. "\n");
		}

		if ($prefs['feature_user_encryption'] === 'y' && !$input->getOption('force')) {
			$output->writeln("<error>User encryption feature is enabled.\n" .
				"Changing the user password might loose encrypted data.\n\n" .
				"Use -f to force changing password.</error>");
			exit(1);
		}

		$userlib->change_user_password($user, $password);
		$output->writeln('Password changed successfully.');
	}

}
