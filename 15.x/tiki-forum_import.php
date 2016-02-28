<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/****************************************************************************
 ** TikiWiki Forum Import Tool v1.00                            12/01/2007 **
 ****************************************************************************
 ** Written by Mike Kerr (kerrnel22)
 **
 ** This script is for importing the contents of a TikiWiki forum:
 **    - from the same database
 **    - from a different database
 **    - from a different server
 **    - from an SQL dump
 **
 ** You must have 'tiki_forum_admin' permissions to run this script, due to
 ** the sensitive and invasive nature of this activity.  Forum moderators do
 ** not count.
 **
 ** This tool will not import attachments, or retain state information such
 ** as read counters or posts waiting on moderator approval.
 **
 ** The target forum must already exist on your Tiki installation.  The forum
 ** you select to move will not be recreated.  Only the contents will be
 ** copied.
 **
 ** Roadmap
 ** -------
 ** v1.1 - Allow to migrate phpBB2 (perhaps other forum types) to Tiki forums.
 **
 ** STATE OF THE CODE
 ** -----------------
 ** - Currently only works when importing Tiki forums from an SQL file.
 ** - SQL file dump must be set to escape single quotes with \' instead of ''
 ** - If you get timeout problems, you may need to increase your php.ini
 **   'max_execution_time' to something like 120 or more, depending on the
 **   size of your SQL file.  120 is good for a file around 20Mb.
 **
 ****************************************************************************/

// Initialization

$inputConfiguration = array(
	array( 'staticKeyFilters' =>
		array(
			'step1' => 'word',
			'step2' => 'word',
			'step3' => 'word',
			'import' => 'word',
			'fForumid' => 'digits',
			'tForumid' => 'digits',
			'ftype' => 'word',
			'prefix' => 'word',
			'server' => 'striptags',
		)
	)
);

require_once ('tiki-setup.php');

$access->check_feature('feature_forums');
$access->check_permission('tiki_p_admin_forum');

include_once ('lib/importerlib.php');
$import = new Importer($dbTiki);

global $prefs;

// Which iteration of the process are we in?
// Step 0 - Select Import Method
// Step 1 - Test Import Method Succeeded
// Step 2 - Select Forum to Import From/To
// Step 3 - Migration Complete - Do Again?
if (isset($_POST["step4"])) {
} else if (isset($_POST["step3"])) {
	if ($_POST["import"] == 'same') {			// Same db and server
	} else if ($_POST["import"] == 'other') {	// Different db & server
	} else if ($_POST["import"] == 'sql') {		// Import from SQL file
		if (!$_POST["fForumid"] || !$_POST["tForumid"]) {
			$smarty->assign('failed', 'true');
		} else {
			$moo = $import->importSQLForum(
				$_POST["ftype"],
				$_POST["prefix"],
				$_POST["server"],
				$_POST["fForumid"],
				$_POST["tForumid"]
			);
			$smarty->assign('failed', 'false');
		}
	} else {										// Error
		$smarty->assign('msg', tra("Form error - no import method selected for some reason."));
		$smarty->display("error.tpl");
		die;
	}

	$smarty->assign('step', 'import');
	$smarty->assign('iMethod', $_POST["import"]);
	$smarty->assign('fi_type', $_POST["ftype"]);
	$smarty->assign('fi_prefix', $_POST["prefix"]);
	$smarty->assign('server', $_POST["server"]);
	$smarty->assign('tomove', $moo);
	$smarty->assign('fF', $_POST["fForumid"]);
	$smarty->assign('tF', $_POST["tForumid"]);
} else if (isset($_POST["step2"])) {
	if ($_POST["import"] == 'same') {			// Same db and server
	} else if ($_POST["import"] == 'other') {	// Different db & server
	} else if ($_POST["import"] == 'sql') {		// Import from SQL file
		//read sql file to create forum list
		$sqlForums = $import->parseForumList(
			$_POST["ftype"],
			$_POST["prefix"],
			$_POST["server"]
		);
		$smarty->assign('fromForums', $sqlForums);
		if (count($sqlForums) == 0) {
				$smarty->assign('noforumsF', 'true');
		} else {
				$smarty->assign('noforumsF', 'false');
		}
	} else {										// Error
		$smarty->assign('msg', tra("Form error - no import method selected for some reason."));
		$smarty->display("error.tpl");
		die;
	}

	$toForums = $import->list_forums(0, -1, 'created_asc', '');
	$smarty->assign_by_ref('toForums', $toForums["data"]);
	if (count($toForums["data"]) == 0) {
			$smarty->assign('noforumsT', 'true');
	} else {
			$smarty->assign('noforumsT', 'false');
	}

	$smarty->assign('step', 'select');
	$smarty->assign('iMethod', $_POST["import"]);
	$smarty->assign('fi_type', $_POST["ftype"]);
	$smarty->assign('fi_prefix', $_POST["prefix"]);
	$smarty->assign('server', $_POST["server"]);
} else if (isset($_POST["step1"])) {
	if (!isset($_POST["import"])) {
		$smarty->assign('msg', tra("Form error - no import method selected for some reason."));
		$smarty->display("error.tpl");
		die;
	} else if ($_POST["import"] == 'same') {		// Same db and server
	} else if ($_POST["import"] == 'other') {	// Different db & server
	} else if ($_POST["import"] == 'sql') {		// Import from SQL file

		/* Import from the SQL file will only look in $tikiroot/$tmpDir or
		 * $tikiroot/img/wiki_up for the speficied file.  Any path is
		 * stripped off the filename input by the user.  $tmpDir overrides
		 * the wiki_up directory.  If the file exists, it then gets
		 * parsed to strip out just the SQL needed for the type of system
		 * being imported.  The relevant data is stored in /tmp in two
		 * temporary flatfiles.
		 */
		if (!isset($_REQUEST["server"])) {
			$smarty->assign('msg', tra("Form error - no server-side filename entered for selected import method."));
			$smarty->display("error.tpl");
			die;
		}

		$server = basename($_REQUEST["server"]);
		if ($server == '') {
			$smarty->assign('passed', 'false');
			$smarty->assign('filecheck', '');
			$smarty->assign('server', '');
		} else if (file_exists($prefs['tmpDir'] . '/' . $server)) {
			$smarty->assign('filecheck', $prefs['tmpDir']);
			$smarty->assign('passed', 'true');
			$smarty->assign('server', $prefs['tmpDir'] . '/' . $server);
		} else if (file_exists('img/wiki_up/' . $server)) {
			$smarty->assign('filecheck', "img/wiki_up");
			$smarty->assign('passed', 'true');
			$smarty->assign('server', "img/wiki_up" . $server);
		} else {
			$smarty->assign('filecheck', $prefs['tmpDir']);
			$smarty->assign('passed', 'false');
		}
	} else {	// Error
		$smarty->assign('msg', tra("Form error - no import method selected for some reason."));
		$smarty->display("error.tpl");
		die;
	}


	$smarty->assign('step', 'test');
	$smarty->assign('iMethod', $_POST["import"]);
	$smarty->assign('fi_type', $_POST["ftype"]);
	$smarty->assign('fi_prefix', $_POST["prefix"]);
} else {
	$smarty->assign('step', 'new');
	$smarty->assign('tmpdir', isset($prefs['tmpDir']) ? $prefs['tmpDir'] : '');
	$smarty->assign('fi_types', $import->fi_types);
	$smarty->assign('fi_prefixes', $import->fi_prefixes);
}

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the page
$smarty->assign('mid', 'tiki-forum_import.tpl');
$smarty->display("tiki.tpl");

exit;
