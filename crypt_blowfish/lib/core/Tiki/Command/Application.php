<?php

namespace Tiki\Command;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputOption;

class Application extends SymfonyApplication
{
    /**
     * Gets the default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
		$definition = parent::getDefaultInputDefinition();
		$definition->addOption(new InputOption('--site', '', InputOption::VALUE_REQUIRED, 'Multi-Tiki instance'));
		
		return $definition;
    }

}

