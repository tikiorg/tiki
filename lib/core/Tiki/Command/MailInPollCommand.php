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
error_reporting(E_ALL);
use Tiki\MailIn;
use TikiLib;

class MailInPollCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mail-in:poll')
            ->setDescription('Read the mail-in messages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$mailinlib = TikiLib::lib('mailin');
		$accs = $mailinlib->list_active_mailin_accounts(0, -1, 'account_desc', '');

		// foreach account
		foreach ($accs['data'] as $acc) {
			if (empty($acc['account'])) {
				continue;
			}

			$account = MailIn\Account::fromDb($acc);
			$account->check();
		}
    }
}
