<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixKeysCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('tool:svnkeys')
			->setDescription('Fix the SVN keyword Id\'s');
	}

	/**
	 *
	 * Recursively calls, glob()
	 *
	 * @param string $pattern
	 * @param int    $flags
	 * @param string $startdir
	 * @param $exclude string|bool if this string is found withn a directory name, it wont be included
	 *
	 * @return array
	 */

	private function glob_recursive($pattern, $flags = 0, $startdir = '', $exclude= false){
		$files = glob($startdir.$pattern, $flags);
		foreach (glob($startdir.'*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_MARK) as $dir){

			if (strpos($dir,$exclude) === false) {
				$files = array_merge($files, $this->glob_recursive($pattern, $flags, $dir, $exclude));
			}
		}
		return $files;
	}


	/**
	 * Reads the beginning of each file in tiki, and resets it if it was copied, causing a broken svn keyword id.
	 *
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{

		$output->writeln("<info>Updating SVN keyword Id's. This will take a minute...</info>");
		// apply filter only to these file types
		foreach ($this->glob_recursive('*.{php,tpl,sh,sql,js,less}', GLOB_BRACE, '', 'vendor_') as $fileName) {
			$handle = fopen( $fileName, "r");
			$count = 1;
			do {
				$buffer = fgets($handle);

				if (preg_match('/(\/\/ |{\* |\# | \* )\$Id.*\$/', $buffer)){ // match several different comment styles
					$output->writeln(shell_exec('svn propset svn:keywords "Id" ' . escapeshellarg($fileName)),OutputInterface::VERBOSITY_DEBUG);
					break;
				}
				$count++;
			} while ($count < 11 && $buffer); // search through up to 11 lines of code (no results expanding that)
			fclose($handle);
		}

		$output->writeln("<info>Keywords updated, you may now review and commit.</info>");

	}
}
