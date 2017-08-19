#!/usr/bin/php
<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: svnup.php 62620 2017-05-16 18:05:56Z drsassafras $

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\HelpCommand;

if (isset($_SERVER['REQUEST_METHOD'])) {
	die('Only available through command-line.');
}

$tikiBase = realpath(dirname(__FILE__). '/../..');
require_once $tikiBase . '/vendor_bundled/vendor/autoload.php';


/**
 * Add a singleton command "svnup" using the Symfony console component for this script
 *
 * Class SvnUpCommand
 * @package Tiki\Command
 */

class SvnUpCommand extends Command
{

	protected function configure()
	{
		$this
			->setName('englishupdate')
			->setDescription("Update translation files with updates made to English strings. Will compare working copy by default.")
			->addOption(
				'email',
				'e',
				InputOption::VALUE_REQUIRED,
				'Email address to send a message if untranslated strings are found. Strings sill not be updated if this option is selected.'
			)
			->addOption(
				'revision',
				'r',
				InputOption::VALUE_REQUIRED,
				'Revision numbers my be selected eg. 63000:63010, or simply 63000 to update strings from 63000 onward.'
			)
			->addOption(
				'lag',
				'l',
				InputOption::VALUE_REQUIRED,
				'Search through previous commits by X number of days, for updated translation strings. Working copy will be ignored.'
			);
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$tikiBase = realpath(dirname(__FILE__) . '/../..');

		echo $tikiBase;

		$rev = '';

		// check that the --lag option is valid, and complain if its not.
		if ($input->getOption('lag')) {
			if ($input->getOption('lag') < 0 || !is_numeric($input->getOption('lag'))) {
				$help = new HelpCommand();
				$help->setCommand($this);
				$help->run($input, $output);

				return $output->writeln('Invalid option for --lag, must be a positive integer.');

			}
			// current time minus number of days specified through lag
			$rev = date('{"Y-m-d H:i"}', time() - $input->getOption('lag') * 60 * 60 * 24);
			$rev = '-r ' . $rev;
		}else if ($input->getOption('revision')){
			$rev = '-r'.$input->getOption('revision');
		}

		// die gracefully if shell_exec is not enabled;
		if (!is_callable('shell_exec')) {
			$output->writeln('<error>Translation string update Failed. Could not execute shell_exec()</error>');
			die();
		}

		$max = 3;

		$progress = new ProgressBar($output, $max);
		if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE)
			$progress->setOverwrite(false);
		$progress->setFormatDefinition('custom', ' %current%/%max% [%bar%] -- %message%');
		$progress->setFormat('custom');


		$progress->setMessage('Getting Diffs');
		$progress->start();

		$raw = shell_exec("svn diff $rev | egrep  \"^([+-]\s|@)\" 2>&1");

		$progress->setMessage('Finding Updated Strings');
		$progress->advance();

		// strip any empty translation strings now to avoid complexities later
		$raw = preg_replace('/tra?\(["\'](\s*?)[\'"]\)/m','',$raw);

		// add missing end-mark to positively match next regex
		$raw .= "\n@@";

		$output->writeln($raw, OutputInterface::VERBOSITY_DEBUG);


		/**
		 * @var $pairedMatches array any changes that took away and added lines.
		 */
		preg_match_all('/@@$([\w\W]*?)^@@/m',$raw,$diffs);

		unset ($raw);

		$count = 0;
		$pairedMatches = array();
		foreach ($diffs[1] as $diff){
			if (preg_match_all('/^-(.*)/m',$diff,$negativeMatch)) {
				if (preg_match_all('/^\+(.*)/m',$diff,$positiveMatch)){
					$pairedMatches[$count]['-']= implode('', $negativeMatch[1]);
					$pairedMatches[$count]['+']= implode('', $positiveMatch[1]);
					$count++;
				}
			}
		}
		unset ($diffs);

		/**
		 * @var $pairedStrings array sets of translation strings found.
		 */

		$count = 0;
		$pairedStrings = array();
		foreach ($pairedMatches as $pair){
			if (preg_match_all('/tra?\(["\']([\S\s]*?)[\'"]\)/m',$pair['-'],$negativeMatch)){
				if (preg_match_all('/tra?\(["\']([\S\s]*?)[\'"]\)/m',$pair['+'],$positiveMatch)){
					// strip out any changes that have a dissimilar number of translation strings. No way to match them properly :(
					if (count($negativeMatch[1]) === count($positiveMatch[1])) {
						$pairedStrings[$count]['-'] = $negativeMatch[1];
						$pairedStrings[$count]['+'] = $positiveMatch[1];
						$count++;
					}
				}
			}
		}
		unset ($pairedMatches);

		/**
		 * @var $updateStrings array final list of translation strings to update
		 */

		$updateStrings = array();
		$overCount = 0;
		foreach ($pairedStrings as $strings){
			$count = 0;
			while (isset($strings['-'][$count])){
				if ($strings['-'][$count] !== $strings['+'][$count]){
					$updateStrings[$overCount]['-'] = $strings['-'][$count];
					$updateStrings[$overCount]['+'] = $strings['+'][$count];
					$overCount++;
				}
				$count++;
			}
		}
		unset ($pairedStrings);

		// send email if that option is selected, then die
		if ($input->getOption('email')) {
			mail($input->getOption('email'), $overCount . ' Undated Translation Strings Found', wordwrap(var_export($updateStrings,true), 70, "\r\n"));
			$progress->setMessage("$overCount translation strings available for update. Email Sent. Strings not updated.");
			$progress->finish();
			echo "\n";
			return false;
		}

		$progress->setMessage("Updating $overCount translation strings");
		$progress->advance();

		$directories = glob($tikiBase . '/lang/*' , GLOB_ONLYDIR);

		// update the language files with the new strings
		$count = 0;
		if ($overCount)
			foreach ($directories as $directory){
				$hash = '';
				$endHash = '';
				if (is_writable($directory.'/language.php')) {
					$file = file_get_contents($directory . '/language.php');
					$hash = hash('crc32b', $file);
					foreach ($updateStrings as $entry) {
						$file = str_replace('"'.$entry['-'].'"', '"'.$entry['+'].'"', $file);
					}
					// check if anything has changed and advance the counter if so.
					$endHash = hash('crc32b', $file);
					if ($hash !== $endHash)
						$count++;
					file_put_contents($directory . '/language.php', $file);
				}
				$output->writeln($directory . ' ' . $hash . ' -> '. $endHash,OutputInterface::VERBOSITY_DEBUG);
			}
		$progress->setMessage('Updated '.$overCount .' strings in '.$count.' out of '.count($directories).' language files. ');
		$progress->finish();
		echo "\n";
		$output->writeln('Verify before committing.');
		echo "\n";
		return true;
	}
}

// create the application and new console
$console = new Application;
$console->add(new SvnUpCommand);
$console->setDefaultCommand('englishupdate');
$console->run();


