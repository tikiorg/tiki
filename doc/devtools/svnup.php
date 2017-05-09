#!/usr/bin/php
<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (isset($_SERVER['REQUEST_METHOD'])) {
	die('Only available through command-line.');
}

class svnupdate {

	public $log;
	public $error;
	private $verbose;

	function __construct()
	{
		$this->log ='';
		$this->verbose = true;
		$this->error = false;
	}

	/**
	 * @param string $command			The bash command to be executed
	 * @param string $errorMessage		Error message to log-display upon failure
	 * @param array  $errors			Error messages to check for, sending a '' will produce an error if no output is
	 * 													produced, handy as an extra check when output is expected.
	 */


	public function execute($command, $errorMessage = '', $errors=array()){

		$output = shell_exec($command);
		$this->log .= $output;

		if ($this->verbose)
			echo $output."\n";

		// check for errors.
		foreach ($errors as $error){
			if (($error === '' && !$output) || strpos($output,$error)) {
				echo color($errorMessage,'red',true);
				$this->error = true;
				TikiLib::lib('logs')->add_action('svn update', $errorMessage, 'system');
			}

		}
	}
}

$tikiBase = realpath(dirname(__FILE__). '/../..');

require_once($tikiBase.'/tiki-setup_base.php');
require_once ($tikiBase.'/doc/devtools/svntools.php');
$logslib = TikiLib::lib('logs');
$update = new svnupdate();


// die gracefully if shell_exec is not enabled;
if (!is_callable('shell_exec')){
	$logslib->add_action('svn update', '{tr}Automatic update failed.{/tr} {tr}Could not execute shell_exec(){/tr}', 'system');
	error('Automatic update failed. Could not execute shell_exec()');
}



# Perform a dry-run to test For SVN Conflicts,  i.e. files modified locally, that have also been modified in the official source
echo color('Testing for SVN conflicts','yellow',true);
$update->execute('svn merge --dry-run -r BASE:HEAD .');


if (strpos($update->log,'Text conflicts')) {
	$logslib->add_action('svn update', '{tr}Automatic update failed.{/tr} {tr}There are some SVN conflicts you need to fix.{/tr}', 'system');
	error('Automatic update has failed. There are some SVN conflicts you need to fix.');
} else {

	// set revision number beginning with.
	preg_match('/Revision: (\d+)/',shell_exec('svn info'),$startRev);
	$startRev = $startRev[1];

	$errors = array('');
	echo color('No conflicts, updating SVN','yellow')."\n";
	$update->execute('svn update','Problem with svn up');

	// set revision number updated to.
	preg_match('/Revision: (\d+)/',shell_exec('svn info'),$endRev);
	$endRev = $endRev[1];

	$errors = array('','Invalid cache','Missing parameter');
	echo color('Clearing cache - first time','yellow')."\n";
	$update->execute('php console.php cache:clear --all','Problem clearing cache');

	$errors = array('', 'Please provide an existing command', 'you are behind a proxy', 'Composer failed', 'Wrong PHP version');
	echo color('Updating dependencies & setting file permissions','yellow')."\n";
	$update->execute('sh setup.sh -n fix 2>&1','Problem running setup.sh');   // 2>&1 suppresses all terminal output, but allows full capturing for logs & verbiage

		// generate a seclib database so when database:update is run, it also gets updated.
	if (svn_files_identical($tikiBase)){
		echo color('Working copy differs from repository, skipping SecDb Update.','green')."\n";
	}else {
		$errors = array('is not writable', '');
		echo color('Updating secdb', 'yellow') . "\n";
		$update->execute('php doc/devtools/release.php --only-secdb --no-check-svn', 'Problem updating secdb');
	}
	$errors = array('','Error');
	echo color('Updating database','yellow')."\n";
	$update->execute('php console.php database:update','Problem updating database');

	$errors = array('','Invalid cache','Missing parameter');
	echo color('Clearing cache - second time','yellow')."\n";
	$update->execute('php console.php cache:clear --all','Problem clearing cache');

	$errors = array('','Search index rebuild failed');
	echo color('Rebuilding search index','yellow')."\n";
	$update->execute('php console.php index:rebuild','Problem rebuilding index');

	if ($update->error) {
		$logslib->add_action('svn update', "{tr}Automatic update completed with errors{/tr}, r$startRev -> r$endRev, {tr}Try again or ensure update functioning.{/tr}", 'system');
		error("Automatic update completed with errors, r$startRev -> r$endRev, Try again or ensure update functioning.");
	}else{
		$logslib->add_action('svn update', "{tr}Automatic update completed{/tr}, r$startRev -> r$endRev", 'system');
		important("Automatic update completed r$startRev -> r$endRev");
	}
}

