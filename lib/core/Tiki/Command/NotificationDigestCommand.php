<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationDigestCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('notification:digest')
            ->setDescription('Send out email notification digests')
			->addArgument(
				'domain',
				InputArgument::REQUIRED,
				'Domain name to use (cannot be obtained from the URL)'
			)
			->addArgument(
				'days',
				InputArgument::OPTIONAL,
				'Number of days to include in the digest',
				7
			)
			->addOption(
				'ssl',
				null,
				InputOption::VALUE_NONE,
				'Use HTTPS for generated links'
			)
			->addOption(
				'path',
				null,
				InputOption::VALUE_REQUIRED,
				'Path to Tiki, if not in the domain root'
			)
			->addOption(
				'port',
				null,
				InputOption::VALUE_REQUIRED,
				'Port to include in the URL'
			)
			;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		global $prefs, $url_scheme, $url_host, $tikiroot, $url_port;
		$days = intval($input->getArgument('days')) ?: 7;

		if ($input->getOption('ssl')) {
			$url_scheme = 'https';
		}

		if ($input->hasOption('port')) {
			$url_port = (int) $input->getOption('port');
		}

		if ($input->hasOption('path')) {
			$tikiroot = $input->getOption('path');
			// Make sure slash before and after
			$tikiroot = rtrim($tikiroot, '/') . '/';
			$tikiroot = '/' . ltrim($tikiroot, '/');
		}

		$url_host = $input->getArgument('domain');

		$list = \TikiDb::get()->fetchAll("
			SELECT userId, login, email, IFNULL(p.value, ?) language
			FROM users_users u
				LEFT JOIN tiki_user_preferences p ON u.login = p.user AND p.prefName = 'language'", [$prefs['site_language']]);

		$monitormail = \TikiLib::lib('monitormail');

		$from = date('Y-m-d H:i:s', time() - $days*24*3600);
		$to = date('Y-m-d H:i:s');

		foreach ($list as $info) {
			$success = $monitormail->sendDigest($info, $from, $to);
			if ($success) {
				$output->writeln("Digest sent to {$info['email']}");
			} else {
				$output->writeln("No data for {$info['email']}");
			}
		}
    }
}
