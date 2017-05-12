#!/usr/bin/php
<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Input\ArrayInput;

if (isset($_SERVER['REQUEST_METHOD'])) {
	die('Only available through command-line.');
}
$tikiBase = realpath(dirname(__FILE__). '/../..');

// will output db errors if 'php svnup.php dbtest' is called
if (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] === 'dbtest') {
	include($tikiBase . '/db/tiki-db.php');
	die();
}

// setup includes based on database mode.
if (!in_array('--no-db',$_SERVER['argv'])) {
	$path = escapeshellarg($tikiBase . '/doc/devtools/svnup.php');
	$error = shell_exec('php ' . $path . ' dbtest');

	if ($error) {
		echo('Running in no-db mode, Database errors: ' . $error . "\n");
		$_SERVER['argv'][] = '--no-db';
	}
}


if (in_array('--no-db',$_SERVER['argv'])){
	require_once ($tikiBase.'/tiki-filter-base.php');

}else{
	require_once ($tikiBase.'/tiki-setup_base.php');
	require_once ($tikiBase.'/lib/setup/timer.class.php');    // needed for index rebuilding
	require_once ($tikiBase.'/lib/cache/cachelib.php');
}

require ($tikiBase.'/doc/devtools/svntools.php');



/**
 * Add a singleton command "svnup" using the Symfony console component just for this script
 *
 * Class SvnUpCommand
 * @package Tiki\Command
 */

class SvnUpCommand extends Command{

	protected function configure()
	{
		$this
			->setName('svnup')
			->setDescription("Updates SVN repository to latest version and performs necessary tasks in Tiki for a smooth upgrade. Suitable for both development and production.")
			->addOption(
				'no-secdb',
				's',
				InputOption::VALUE_NONE,
				'Skip updating the secdb database.'
			)
			->addOption(
				'no-reindex',
				'r',
				InputOption::VALUE_NONE,
				'Skip re-indexing Tiki.'
			)
			->addOption(
				'no-db',
				'd',
				InputOption::VALUE_NONE,
				'Make no changes to the database. (Disables Logging, Database Update, Re-Indexing & Sec-DB'
			);
	}


	/**
	 * @param string $command			The bash command to be executed
	 * @param string $errorMessage		Error message to log-display upon failure
	 * @param array  $errors			Error messages to check for, sending a '' will produce an error if no output is
	 * 													produced, handy as an extra check when output is expected.
	 * @param bool 	$log				If errors shoud be logged.
	 */
	public function OutputErrors(ConsoleLogger &$logger, $return, $errorMessage = '', $errors=array(),$log = true){

		$logger->info($return);

		// check for errors.
		foreach ($errors as $error){
			if (($error === '' && !$return) || ($error && strpos($return,$error))) {
				$logger->error($errorMessage);
				if ($log) {
					$logs = new \LogsLib();
					$logs->add_action('svn update', $errorMessage, 'system');
				}
			}
		}
	}

	protected function rebuildIndex(ConsoleLogger $logger, OutputInterface $output){

		$console = new Application;
		$console->add(new IndexRebuildCommand);
		$console->setAutoExit(false);
		$console->setDefaultCommand('index:rebuild',false);
		$input = null;
		if ($output->getVerbosity() <= OutputInterface::VERBOSITY_VERBOSE) {
			$input = new ArrayInput(array('-q' => null));
		}elseif ($output->getVerbosity() == OutputInterface::VERBOSITY_DEBUG) {
			$input = new ArrayInput(array('-vvv' => null));
		}
		$console->run($input);

		$errors = \Feedback::get();
		if (is_array($errors)) {
			$logger->error('Search index rebuild failed.');
		}
	}


	protected function dbUpdate(OutputInterface $output)
	{

		$console = new Application;
		$console->add(new UpdateCommand);
		$console->setAutoExit(false);
		$console->setDefaultCommand('database:update', false);
		$input = null;
		if ($output->getVerbosity() <= OutputInterface::VERBOSITY_VERBOSE) {
			$input = new ArrayInput(array('-q' => null));
		}elseif ($output->getVerbosity() == OutputInterface::VERBOSITY_DEBUG) {
			$input = new ArrayInput(array('-vvv' => null));
		}
		$console->run($input);
	}


	protected function execute(InputInterface $input, OutputInterface $output){
		$tikiBase = realpath(dirname(__FILE__). '/../..');

		$verbosityLevelMap = array(
			LogLevel::CRITICAL   => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::ERROR      => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::NOTICE     => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::INFO       => OutputInterface::VERBOSITY_VERY_VERBOSE
		);

		$logger = new ConsoleLogger($output, $verbosityLevelMap);

		// if were using a db, then configure it.
		if (!$input->getOption('no-db')){
			$logslib = new \LogsLib();
		}



		// die gracefully if shell_exec is not enabled;
		if (!is_callable('shell_exec')){
			if (!$input->getOption('no-db'))
				$logslib->add_action('svn update', 'Automatic update failed. Could not execute shell_exec()', 'system');
			$logger->critical('Automatic update failed. Could not execute shell_exec()');
			die();
		}


		$max = 6;
		if ($input->getOption('no-db')) {
			$max -= 4;
		}else{
			if ($input->getOption('no-secdb'))
				$max --;
			if ($input->getOption('no-reindex'))
				$max --;
		}

		$progress = new ProgressBar($output, $max);
		if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE)
			$progress->setOverwrite(false);
		$progress->setFormatDefinition('custom', ' %current%/%max% [%bar%] -- %message%');
		$progress->setFormat('custom');


		$progress->setMessage('Pre-Update Checks');
		$progress->start();

		// set revision number beginning with.
		$raw = shell_exec('svn info');
		$output->writeln($raw,OutputInterface::VERBOSITY_DEBUG);
		preg_match('/Revision: (\d+)/',$raw,$startRev);
		$startRev = $startRev[1];

		$progress->setMessage('Updating SVN');
		$progress->advance();
		$errors = array('','Text conflicts');
		$this->OutputErrors($logger,shell_exec('svn update --accept p'),'Problem with svn up, check for conflicts.',$errors,!$input->getOption('no-db'));

		// set revision number updated to.
		$raw = shell_exec('svn info');
		$output->writeln($raw,OutputInterface::VERBOSITY_DEBUG);
		preg_match('/Revision: (\d+)/',$raw,$endRev);
		$endRev = $endRev[1];

		if (!$input->getOption('no-db')) {
			$progress->setMessage('Clearing all caches');
			$progress->advance();
			$cache = new \Cachelib();
			$cache->empty_cache();
		}

		$progress->setMessage('Updating dependencies & setting file permissions');
		$progress->advance();
		$errors = array('', 'Please provide an existing command', 'you are behind a proxy', 'Composer failed', 'Wrong PHP version');
		$this->OutputErrors($logger,shell_exec('sh setup.sh -n fix 2>&1'),'Problem running setup.sh',$errors,!$input->getOption('no-db'));   // 2>&1 suppresses all terminal output, but allows full capturing for logs & verbiage

		// generate a secbb database so when database:update is run, it also gets updated.
		if (!$input->getOption('no-secdb') && $input->getOption('no-db')) {
			if (svn_files_identical($tikiBase)) {
				$progress->setMessage('<comment>Working copy differs from repository, skipping SecDb Update.</comment>');
				$progress->advance();
			} else {
				$progress->setMessage('Updating secdb');
				$progress->advance();
				$errors = array('is not writable', '');
				$this->OutputErrors($logger, shell_exec('php doc/devtools/release.php --only-secdb --no-check-svn'), 'Problem updating secdb', $errors);
			}
		}

		if (!$input->getOption('no-db')) {

			// note: running database update also clears the cache
			$progress->setMessage('Updating database');
			$progress->advance();
			$this->dbUpdate($output);


			// rebuild tiki index. Since this could take a while, make it optional.
			if (!$input->getOption('no-reindex')) {
				$progress->setMessage('Rebuilding search index');
				$progress->advance();
				$this->rebuildIndex($logger, $output);
			}
		}

		if ($logger->hasErrored()) {
			if (!$input->getOption('no-db'))
				$logslib->add_action('svn update', "Automatic update completed with errors, r$startRev -> r$endRev, Try again or debug.", 'system');
			$progress->setMessage("Automatic update completed with errors, r$startRev -> r$endRev, Try again or ensure update functioning.");
		}elseif ($input->getOption('no-db')){
			$progress->setMessage("<comment>Automatic update completed in no-db mode, r$startRev -> r$endRev, Database not updated.</comment>");
		}else{
			$logslib->add_action('svn update', "Automatic update completed, r$startRev -> r$endRev", 'system');
			$progress->setMessage("<comment>Automatic update completed r$startRev -> r$endRev</comment>");
		}
		$progress->finish();
		echo "\n";
	}
}

// create the application and new console
$console = new Application;
$console->add(new SvnUpCommand);
$console->setDefaultCommand('svnup');
$console->run();


