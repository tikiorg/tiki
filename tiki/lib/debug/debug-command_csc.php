<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/debug/debug-command_csc.php,v 1.1 2003-07-13 00:35:40 zaufi Exp $
 *
 * \brief Clear Smarty Cache
 *
 * \author zaufi <zaufi@sendmail.ru>
 *
 */
require_once('lib/debug/debugger-ext.php');

/**
 * \brief Debugger command to print smatry vars
 */
class DbgCSC extends DebuggerCommand
{
  /// \b Must have function to announce command name in debugger console
  function name()
  {
    return 'csc';
  }
  /// \b Must have function to provide help to debugger console
  function description()
  {
    return 'This clears the entire template cache used by Smarty';
  }
  /// \b Must have function to provide help to debugger console
  function syntax()
  {
    return 'csc';
  }
  /// \b Must have function to show exampla of usage of given command
  function example()
  {
    return 'csc';
  }
  /// Execute command with given set of arguments. Must return string of result.
  function execute($params)
  {
    $this->set_result_type(TEXT_RESULT);
    // Get list of vars
    global $smarty;
    $tpl_vars = $smarty->clear_all_cache();
    return 'OK';
  }
}
/// Class factory to create instances of defined commands
function dbg_command_factory_csc()
{
  return new DbgCSC();
}

?>
