<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

/**
 * Stub command implementation to list the commands even though they are not available.
 */
class UnavailableCommand extends Command
{
	public function __construct($commandName)
	{
		$this->setName($commandName);
		parent::__construct();
	}

    protected function configure()
    {
        $this
            ->setDescription('Command not available');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$output->writeln('<error>Command not available at this stage. Complete required installation steps.</error>');
    }
}
