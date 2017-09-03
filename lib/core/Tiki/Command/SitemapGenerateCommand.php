<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Tiki\Sitemap\Generator as SiteMapGenerator;

class SitemapGenerateCommand extends Command
{

	protected function configure()
	{
		$this
			->setName('sitemap:generate')
			->setDescription('Generate sitemap')
			->addArgument(
				'url',
				InputArgument::REQUIRED,
				'URL of the website. Example http://www.example.com'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$url = $input->getArgument('url');

		$sitemap = new SiteMapGenerator();

		$sitemap->generate($url);

		$output->writeln('<info>New sitemap created!</info>');

	}

}