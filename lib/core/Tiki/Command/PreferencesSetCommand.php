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
use Symfony\Component\Console\Output\OutputInterface;
use TikiLib;

class PreferencesSetCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('preferences:set')
            ->setDescription('Set a preference')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Preference name'
            )
            ->addArgument(
                'value',
                InputArgument::REQUIRED,
                'Preference value'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $preference = $input->getArgument('name');
        $value = $input->getArgument('value');

        $tikilib = TikiLib::lib('tiki');
        $prefslib = TikiLib::lib('prefs');

        $preferenceInfo = $prefslib->getPreference($preference);

        if (empty($preferenceInfo)) {
            $output->write('<error>Preference not found.</error>');
            return;
        }

        if ($preferenceInfo['type'] == 'flag' && !in_array($value, array('y', 'n'))) {
            $output->writeln(sprintf('Preference %s is of type flag, allowed values are "y" or "n", you used %s.', $preference, $value));
            return;
        }

        if (!empty($preferenceInfo['separator']) && !is_array($value)) {
            $value = explode($preferenceInfo['separator'], $value);
        }

        if ($tikilib->set_preference($preference, $value)) {
            $output->writeln(sprintf('Preference %s was set.', $preference));
        } else {
            $output->writeln('<error>Unable to set preference.</error>');
        }
    }
}
