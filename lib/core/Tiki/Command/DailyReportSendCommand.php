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
use TikiLib, Reports_Factory;

class DailyReportSendCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('daily-report:send')
            ->setDescription('Send daily user reports');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $access = TikiLib::lib('access');

      $access->check_feature('feature_daily_report_watches');

      $output->writeln('Generating reports...');
      $reportsManager = Reports_Factory::build('Reports_Manager');

      $output->writeln('Sending...');
      $reportsManager->send();

      $output->writeln('Finished.');
    }
}
