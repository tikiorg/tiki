<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * \brief Exec SQL query on Tiki DB
 * \author zaufi <zaufi@sendmail.ru>
 */
require_once ('lib/debug/debugger-ext.php');

/**
 * \brief Debugger command to exec SQL
 */
class DbgSQLQuery extends DebuggerCommand
{
	/// \b Must have function to announce command name in debugger console
	function name()
	{
		return 'sql';
	}

	/// \b Must have function to provide help to debugger console
	function description()
	{
		return 'Exec SQL query on Tiki DB';
	}

	/// \b Must have function to provide help to debugger console
	function syntax()
	{
		return 'sql [sql-query]';
	}

	/// \b Must have function to show example of usage of given command
	function example()
	{
		return 'sql select * from tiki_preferences';
	}

	/// Execute command with given set of arguments.
	function execute($params)
	{
		//
		// Due to limitations of Smarty, I am forced to use
		// HTML_RESULT... (not fun!)
		//
		$this->set_result_type(HTML_RESULT);

		$this->set_result_tpl('debug/tiki-debug_sql.tpl');
		// Init result
		$result = '';
		//
		global $debugger;
		$debugger->msg('SQL query: "' . $params . '"');

		//
		if (strlen(trim($params)) != 0) {
			global $tikilib;

			$qr = $tikilib->db->query($params);

			if (DB::isError($qr))
				$result = '<span class="dbgerror">' . $qr->getMessage(). '</span>';
			else {
				// Check if result value an array or smth else
				if (is_object($qr)) {
					// Looks like 'SELECT...' return table to us...
					// So our result will be 2 dimentional array
					// with elements count and fields number for element
					// as dimensions...
					$first_time = true;

					$result = '<table id="sqltable">';

					while ($res = $qr->fetchRow(DB_FETCHMODE_ASSOC)) {
						if ($first_time) {
							// Form 1st element with field names
							foreach ($res as $key => $val)
								$result .= '<td class="heading">' . $key . '</td>';

							$first_time = false;
						}

						$result .= '<tr>';
						// Repack one element into result array
						$td_eo_class = true;

						foreach ($res as $val) {
							$result .= '<td class=' . ($td_eo_class ? "even" : "odd") . '>' . $val . '</td>';

							$td_eo_class = !$td_eo_class;
						}

						//
						$result .= '</tr>';
					}

					$result .= '</table>';
				} else {
					// Let PHP to dump result :)
					$result = 'Query result: ' . print_r($qr, true);
				}
			}
		} else
			$result = "Empty query to tiki DB";

		//
		return $result;
	}
}

/// Class factory to create instances of defined commands
function dbg_command_factory_sql()
{
	return new DbgSQLQuery();
}
