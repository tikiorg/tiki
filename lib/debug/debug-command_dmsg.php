<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/debug/debug-command_dmsg.php,v 1.4 2003-08-14 01:00:30 zaufi Exp $
 *
 * \brief 'debugger command' to show user messages in tab
 *
 * \author zaufi <zaufi@sendmail.ru>
 *
 */
require_once ('lib/debug/debugger-ext.php');

global $debugger;
require_once ('lib/debug/debugger.php');

/**
 * \brief Command 'watch'
 */
class DbgCmd_DebugMessages extends DebuggerCommand {
	/// Function to create interface part of command: return ["button name"] = <html code>
	function draw_interface() {
		global $smarty;

		global $debugger;
		$smarty->assign_by_ref('messages', $debugger->dmsgs);
		return $smarty->fetch("debug/tiki-debug_dmsg_tab.tpl");
	}

	/// Function to return caption string to draw plugable tab in interface
	function caption() {
		return "debug messages";
	}

	/// Need to display button if we have smth to show
	function have_interface() {
		global $debugger;

		// At least one message is always exists ... It is debugger itself say that started :)
		return count($debugger->dmsgs) > 1;
	}
}

/// Class factory
function dbg_command_factory_dmsg() {
	return new DbgCmd_DebugMessages();
}

?>