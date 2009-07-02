<?php
/** \file
 * $Id: /cvsroot/tikiwiki/tiki/lib/debug/debug-command_slist.php,v 1.3 2003-08-07 04:34:03 rossta Exp $
 *
 * \brief List of Smarty vars
 *
 * \author zaufi <zaufi@sendmail.ru>
 *
 */
require_once ('lib/debug/debugger-ext.php');

/**
 * \brief Debugger command to list smatry vars
 */
class DbgSList extends DebuggerCommand {
	/// \b Must have function to announce command name in debugger console
	function name() {
		return 'slist';
	}

	/// \b Must have function to provide help to debugger console
	function description() {
		return 'Display list of Smarty variables. Better to specify partial name or very long list of vars will returns.';
	}

	/// \b Must have function to provide help to debugger console
	function syntax() {
		return 'slist [partial-name]';
	}

	/// \b Must have function to show example of usage of given command
	function example() {
		return 'slist' . "\n" . 'slist auth' . "\n" . 'slist ^wiki' . "\n" . 'slist .+admin.*';
	}

	/// Execute command with given set of arguments.
	function execute($params) {
		$this->set_result_type(HTML_RESULT);

		// Is regex to match against var name given?
		$p = explode(" ", trim($params));
		$mask = count($p) > 0 ? str_replace('$', '', trim($p[0])) : '';
		// Get list of vars
		global $smarty;
		$tpl_vars = $smarty->get_template_vars();
		// convert to vector of names and sort
		$vars = array();
		$len = strlen($mask);

		foreach ($tpl_vars as $key => $val)
			if (!$len || $len && preg_match('/' . $mask . '/', $key))
				$vars[] = $key;

		sort ($vars);
		//
		$result = '<table border=0>';
		$row = '<tr>';
		$idx = 0;

		foreach ($vars as $var) {
			if (($idx % 3) == 0) {
				$result .= $row . '</tr>';

				$row = '<tr><td>$' . $var . '</td>';
				$idx = 1;
			} else {
				$row .= '<td>$' . $var . '</td>';

				$idx++;
			}
		}

		$result .= '</table>';
		return $result;
	}
}

/// Class factory to create instances of defined commands
function dbg_command_factory_slist() {
	return new DbgSList();
}

?>