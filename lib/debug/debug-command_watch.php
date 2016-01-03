<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * \brief Watch command for debugger
 * \author zaufi <zaufi@sendmail.ru>
 */
require_once ('lib/debug/debugger-ext.php');

/**
 * \brief Command 'watch'
 */
class DbgCmd_Watch extends DebuggerCommand
{
	/// Array of variables to watch in format: [md5hash] = var_name
	var $watches;

	/// Restore watches list at construction time
	function __construct()
	{
		global $user;

		$this->watches = array();

		if (is_readable($this->watchfile())) {
			$s = implode('', @file($this->watchfile()));

			$a = unserialize($s);

			if (count($a) > 0)
				$this->watches = (array)$a;
		}
	}

	function name()
	{
		return 'watch';
	}

	function description()
	{
		return 'Manage variables watch list';
	}

	function syntax()
	{
		return 'watch (add|rm) $php_var1 smarty_var2 $php_var3 smarty_var4 ...';
	}

	function example()
	{
		return 'watch add $user tiki_p_view' . "\n" . 'watch rm user $_REQUEST $_SERVER["HTTP_USER_AGENT"]';
	}

	function execute($params)
	{
		global $user;

		// NOTE: Don't forget to set result type! By default it is NO_RESULT.
		$this->set_result_type(TEXT_RESULT);
		$result = '';
		$args = explode(' ', trim($params));

		//
		if (count($args) > 0) {
			$cmd = trim($args[0]);

			if ($cmd == 'add' || $cmd == 'rm') {
				$a_r = ($cmd == 'add');

				array_shift($args);

				if (count($args) > 0) {
					foreach ($args as $a)
						if (strlen(trim(str_replace('$', '', $a))) > 0) { // Is there smth 'cept '$'??
							$a = trim($a);

							if ($a_r) {
								$result .= "add '" . $a . "' to watch list\n";
								$this->watches[md5($a)] = $a;
							} else {
								$result .= "remove '" . $a . "' from watch list\n";

								if (isset($this->watches[md5($a)]))
									unset ($this->watches[md5($a)]);
								else
									$result .= "ERROR: No such variable in watch list\n";
							}
						}

					// Store changes in watchlist to disk
					$this->store_watches();
				} else
					$result .= "ERROR: No variable to add given";
			}
			elseif (strlen(trim($args[0])) > 0)
				$result .= "ERROR: Unknown subcommand '$arg[0]'";
			else
				$result .= "ERROR: No subcommand for 'watch' given";
		} else
			$result .= "ERROR: No subcommand for 'watch' given";

		return $result;
	}

	/// Return the name of watches file
	function watchfile()
	{
		global $user;

		return 'temp/dbg-watch.' . $user;
	}

	/// Save watchlist for given user. If current list is empty --> remove file.
	function store_watches()
	{
		if (count($this->watches) > 0) {
			$s = serialize($this->watches);

			$fp = fopen($this->watchfile(), 'w');
			fputs($fp, $s);
			fclose($fp);
		} else {
			if (is_writable($this->watchfile()))
				unlink($this->watchfile());
		}
	}

	/// Function to create interface part of command: return ["button name"] = <html code>
	function draw_interface()
	{
		$result = array();

		// Iterate through all variables
		foreach ($this->watches as $v)
			// NOTE: PHP variables must start with '$', else assumed smarty variable
			$result[] = array(
				'var' => $v,
				'value' => ((substr($v, 0, 1) == '$') ? $this->value_of_php_var($v) : $this->value_of_smarty_var($v))
			);

		//
		$smarty = TikiLib::lib('smarty');
		$smarty->assign_by_ref('watchlist', $result);
		return $smarty->fetch("debug/tiki-debug_watch_tab.tpl");
	}

	///
	function value_of_smarty_var($v)
	{
		$smarty = TikiLib::lib('smarty');

		$result = '';

		if (strlen($v) != 0) {
			$tmp = $smarty->getTemplateVars();

			if (is_array($tmp) && isset($tmp[$v]))
				$result .= print_r($tmp[$v], true). "\n";
			else
				$result .= 'Smarty variable "' . $v . '" not found';
		}

		return $result;
	}

	///
	function value_of_php_var($v)
	{
		global $debugger;

		require_once ('lib/debug/debugger.php');
		return $debugger->str_var_dump($v);
	}

	/// Function to return caption string to draw plugable tab in interface
	function caption()
	{
		return 'watches';
	}

	/// Need to display button if we have smth to show
	function have_interface()
	{
		return count($this->watches) > 0;
	}
}

/// Class factory
function dbg_command_factory_watch()
{
	return new DbgCmd_Watch();
}
