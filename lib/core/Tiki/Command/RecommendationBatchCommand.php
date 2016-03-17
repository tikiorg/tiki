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

use Tiki\Recommendation\Input\UserInput;
use TikiLib;

class RecommendationBatchCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('recommendation:batch')
            ->setDescription('Identify and send recommendations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$batch = TikiLib::lib('recommendationcontentbatch');
		$batch->process();
    }
}
