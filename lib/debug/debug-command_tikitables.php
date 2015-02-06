<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * \brief Show list of Tiki tables in DB schema
 * \author zaufi <zaufi@sendmail.ru>
 */
require_once ('lib/debug/debugger-ext.php');

/**
 * \brief Show list of Tiki tables in DB schema
 */
class DbgSQLTables extends DebuggerCommand
{
	/// \b Must have function to announce command name in debugger console
	function name()
	{
		return 'tikitables';
	}

	/// \b Must have function to provide help to debugger console
	function description()
	{
		return 'Show list of Tiki tables in DB schema';
	}

	/// \b Must have function to provide help to debugger console
	function syntax()
	{
		return 'tikitables [partial-name]';
	}

	/// \b Must have function to show example of usage of given command
	function example()
	{
		return 'tikitables' . "\n" . 'tikitables user' . "\n" . 'tikitables ions$';
	}

	/// Execute command with given set of arguments.
	function execute($params)
	{
		$this->set_result_type(TPL_RESULT);

		$this->set_result_tpl('debug/tiki-debug_tikitables.tpl');
		//
		global $tikilib;
		// Is regex to match against var name given?
		$p = explode(" ", trim($params));
		$mask = count($p) > 0 ? str_replace('$', '', trim($p[0])) : '';
		$len = strlen($mask);
		// Get list of all tables
		$qr = $tikilib->query("show tables;");
		$tbls = array();

		while ($res = $qr->fetchRow(DB_FETCHMODE_ASSOC)) {
			/*
				 * Sample output from MySQL. I.e. array(1) have unpredictable key
				 * (bcouse of name user defined table)...
			 *  array(163) {
			 *  [0]=>
			 *  array(1) {
			 *    ["Tables_in_tiki-devel"]=>
			 *    string(18) "galaxia_activities"
			 *  }
			 *  [1]=>
			 *  array(1) {
			 *    ["Tables_in_tiki-devel"]=>
			 *    string(22) "galaxia_activity_roles"
			 *  }
			 *  [2]=>
			 *  array(1) {
			 *    ["Tables_in_tiki-devel"]=>
			 *    string(27) "galaxia_instance_activities"
			 *  }
				 *  ...
				 */
			if (!$len || $len && preg_match('/' . $mask . '/', current($res)))
				$tbls[] = current($res);
		}

		return $tbls;
	}
}

/// Class factory to create instances of defined commands
function dbg_command_factory_tikitables()
{
	return new DbgSQLTables();
}
