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

class PreferencesDeleteCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('preferences:delete')
            ->setDescription('Delete a preference')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Preference name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $preference = $input->getArgument('name');

        $tikilib = TikiLib::lib('tiki');
        $prefslib = TikiLib::lib('prefs');

        $preferenceInfo = $prefslib->getPreference($preference);

        if (empty($preferenceInfo)) {
            $output->write('<error>Preference not found.</error>');
            return;
        }

        $value = $tikilib->delete_preference($preference);

        if (!empty($preferenceInfo['separator']) && is_array($value)) {
            $value = implode($preferenceInfo['separator'], $value);
        }

        $output->writeln(sprintf('Preference %s was deleted', $preference));
    }
}
