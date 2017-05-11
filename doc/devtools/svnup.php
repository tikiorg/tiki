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


if (isset($_SERVER['REQUEST_METHOD'])) {
	die('Only available through command-line.');
}

$tikiBase = realpath(dirname(__FILE__). '/../..');
require_once($tikiBase.'/tiki-setup_base.php');
require_once ($tikiBase.'/doc/devtools/svntools.php');


/**
 * Add a singleton command "svnup" using the Symfony concole component just for this script
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
			);
	}


	/**
	 * @param string $command			The bash command to be executed
	 * @param string $errorMessage		Error message to log-display upon failure
	 * @param array  $errors			Error messages to check for, sending a '' will produce an error if no output is
	 * 													produced, handy as an extra check when output is expected.
	 */
	public function executeCommand(ConsoleLogger $logger, $command, $errorMessage = '', $errors=array()){

		$return = shell_exec($command);

		$logger->info($return);

		// check for errors.
		foreach ($errors as $error){
			if (($error === '' && !$return) || ($error && strpos($return,$error))) {
				$logger->error($errorMessage);
				\TikiLib::lib('logs')->add_action('svn update', $errorMessage, 'system');
			}

		}
	}
	protected function execute(InputInterface $input, OutputInterface $output){

		$tikiBase = realpath(dirname(__FILE__). '/../..');
		$logslib = \TikiLib::lib('logs');

		$verbosityLevelMap = array(
			LogLevel::CRITICAL   => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::ERROR      => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::NOTICE     => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::INFO       => OutputInterface::VERBOSITY_VERBOSE
		);

		$logger = new ConsoleLogger($output, $verbosityLevelMap);

		// die gracefully if shell_exec is not enabled;
		if (!is_callable('shell_exec')){
			$logslib->add_action('svn update', '{tr}Automatic update failed.{/tr} {tr}Could not execute shell_exec(){/tr}', 'system');
			$logger->critical('Automatic update failed. Could not execute shell_exec()');
			die();
		}
		$progress = new ProgressBar($output, 5+!$input->getOption('no-secdb')+!$input->getOption('no-reindex'));
		$progress->setFormatDefinition('custom', ' %current%/%max% [%bar%] -- %message%');
		$progress->setFormat('custom');

		# Perform a dry-run to test For SVN Conflicts,  i.e. files modified locally, that have also been modified in the official source
		$errors = array('Text conflicts');
		$progress->setMessage('Testing for SVN conflicts');
		$progress->start();
		$this->executeCommand($logger,'svn merge --dry-run -r BASE:HEAD .','Automatic update failed. There are some SVN conflicts you need to fix.',$errors);

		// if there are conflicts, don't continue.
		if ($logger->hasErrored()) {
			$progress->finish();
			echo "\n";
			die();
		}

		// set revision number beginning with.
		preg_match('/Revision: (\d+)/',shell_exec('svn info'),$startRev);
		$startRev = $startRev[1];

		$progress->setMessage('No conflicts, updating SVN');
		$progress->advance();
		$errors = array('');
		$this->executeCommand($logger,'svn update','Problem with svn up',$errors);

		// set revision number updated to.
		preg_match('/Revision: (\d+)/',shell_exec('svn info'),$endRev);
		$endRev = $endRev[1];

		$progress->setMessage('Clearing cache');
		$progress->advance();
		$errors = array('','Invalid cache','Missing parameter');
		$this->executeCommand($logger,'php console.php cache:clear --all','Problem clearing cache',$errors);

		$progress->setMessage('Updating dependencies & setting file permissions');
		$progress->advance();
		$errors = array('', 'Please provide an existing command', 'you are behind a proxy', 'Composer failed', 'Wrong PHP version');
		$this->executeCommand($logger,'sh setup.sh -n fix 2>&1','Problem running setup.sh',$errors);   // 2>&1 suppresses all terminal output, but allows full capturing for logs & verbiage

		// generate a secbb database so when database:update is run, it also gets updated.
		if (!$input->getOption('no-secdb')) {
			if (svn_files_identical($tikiBase)) {
				$progress->setMessage('<comment>Working copy differs from repository, skipping SecDb Update.</comment>');
				$progress->advance();
			} else {
				$progress->setMessage('Updating secdb');
				$progress->advance();
				$errors = array('is not writable', '');
				$this->executeCommand($logger, 'php doc/devtools/release.php --only-secdb --no-check-svn', 'Problem updating secdb', $errors);
			}
		}

		// note: running database update also clears the cache
		$progress->setMessage('Updating database');
		$progress->advance();
		$errors = array('', 'Error');
		$this->executeCommand($logger,'php console.php database:update', 'Problem updating database', $errors);

		// rebuild tiki index. Since this could take a while, make it optional.
		if (!$input->getOption('no-reindex')) {
			$progress->setMessage('Rebuilding search index');
			$progress->advance();
			$errors = array('', 'Search index rebuild failed');
			$this->executeCommand($logger,'php console.php index:rebuild', 'Problem rebuilding index', $errors);
		}

		if ($logger->hasErrored()) {
			$logslib->add_action('svn update', "Automatic update completed with errors, r$startRev -> r$endRev, {tr}Try again or debug.{/tr}", 'system');
			$progress->setMessage("Automatic update completed with errors, r$startRev -> r$endRev, Try again or ensure update functioning.");
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

// run the command
$console->run();


