<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: CacheClearCommand.php 45337 2013-03-25 21:28:58Z lphuberdeau $

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Assetic,
	Assetic\Asset\AssetCollection,
	Assetic\Asset\FileAsset,
    Assetic\Filter\LessphpFilter as LessFilter,
	Assetic\Filter\CssRewriteFilter as CssRewriteFilter
	;

class DevelopmentCompileCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('development:compile')
            ->setDescription('Compile source CSS files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$this->writeCss();
    }

	private function writeCss()
	{
		$collection = $this->getCssFiles();

		$writer = new Assetic\AssetWriter('.');
		foreach ($collection as $asset) {
			$asset->setTargetPath('compiled/' . $asset->getSourcePath() . '.css');
			$writer->writeAsset($asset);
		}
	}

	private function getCssFiles()
	{
		$collection = new AssetCollection(array());

		foreach (array(
			//'temp/test.less',
		) as $filePath) {
			$collection->add(new FileAsset($filePath, array(new LessFilter, new CssRewriteFilter)));
		}

		return $collection;
	}
}
