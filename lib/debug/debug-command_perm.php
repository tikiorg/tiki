<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * \brief Show current permissions in a convenient way
 * \author zaufi <zaufi@sendmail.ru>
 */
require_once ('lib/debug/debugger-ext.php');

/**
 * \brief Show current permissions in a convenient way
 */
class DbgPermissions extends DebuggerCommand
{
	/// \b Must have function to announce command name in debugger console
	function name()
	{
		return 'perm';
	}

	/// \b Must have function to provide help to debugger console
	function description()
	{
		return 'Show current permissions in a convenient way';
	}

	/// \b Must have function to provide help to debugger console
	function syntax()
	{
		return 'perm [partial-name]';
	}

	/// \b Must have function to show example of usage of given command
	function example()
	{
		return 'perm' . "\n" . 'perm admin' . "\n" . 'perm .*_comments$';
	}

	/// Execute command with given set of arguments.
	function execute($params)
	{
		$userlib = TikiLib::lib('user');
		$smarty = TikiLib::lib('smarty');

		$this->set_result_type(TPL_RESULT);

		$this->set_result_tpl('debug/tiki-debug_permissions.tpl');
		// Is regex to match against var name given?
		$p = explode(' ', trim($params));
		$mask = count($p) > 0 ? str_replace('$', '', trim($p[0])) : '';
		// Get list of all vars
		$tpl_vars = $smarty->getTemplateVars();
		// Get descriptions for all permissions
		$pd = $userlib->get_permissions();
		$descriptions = array();

		foreach ($pd['data'] as $p)
			$descriptions[$p['permName']] = $p['permDesc'];

		// convert to vector of names, filter permissions only
		$perms = array();
		$len = strlen($mask);

		foreach ($tpl_vars as $key => $val) {
			if ((!$len || $len && preg_match('/' . $mask . '/', $key)) && preg_match('/tiki_p_/', $key))
				$perms[] = array(
					'name' => $key,
					'value' => $val,
					'description' => isset($descriptions[$key]) ? $descriptions[$key] : 'No description'
				);
		}

		return $perms;
	}
}

/// Class factory to create instances of defined commands
function dbg_command_factory_perm()
{
	return new DbgPermissions();
}
