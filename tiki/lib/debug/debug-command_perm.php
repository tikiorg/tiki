<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/debug/debug-command_perm.php,v 1.3 2003-08-07 04:34:03 rossta Exp $
 *
 * \brief Show current permissions in a convenient way
 *
 * \author zaufi <zaufi@sendmail.ru>
 *
 */
require_once ('lib/debug/debugger-ext.php');

/**
 * \brief Show current permissions in a convenient way
 */
class DbgPermissions extends DebuggerCommand {
	/// \b Must have function to announce command name in debugger console
	function name() {
		return 'perm';
	}

	/// \b Must have function to provide help to debugger console
	function description() {
		return 'Show current permissions in a convenient way';
	}

	/// \b Must have function to provide help to debugger console
	function syntax() {
		return 'perm [partial-name]';
	}

	/// \b Must have function to show example of usage of given command
	function example() {
		return 'perm' . "\n" . 'perm admin' . "\n" . 'perm .*_comments$';
	}

	/// Execute command with given set of arguments.
	function execute($params) {
		$this->set_result_type(TPL_RESULT);

		$this->set_result_tpl('debug/tiki-debug_permissions.tpl');
		// Is regex to match against var name given?
		$p = explode(" ", trim($params));
		$mask = count($p) > 0 ? str_replace('$', '', trim($p[0])) : '';
		// Get list of all vars
		global $smarty;
		$tpl_vars = $smarty->get_template_vars();
		// Get descriptions for all permissions
		global $userlib;
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
function dbg_command_factory_perm() {
	return new DbgPermissions();
}

?>