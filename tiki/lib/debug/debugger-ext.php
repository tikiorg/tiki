<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/debug/debugger-ext.php,v 1.1 2003-07-13 00:35:40 zaufi Exp $
 *
 * \brief Base class for external debugger command
 *
 * \author zaufi <zaufi@sendmail.ru>
 *
 */

require_once('lib/debug/debugger-common.php');

/**
 * \brief Base class for external debugger command
 */
class DebuggerCommand extends ResultType
{
  /// \b Must have function to announce command name in debugger console
  function name()
  {
    return "";
  }
  /// \b Must have function to provide help to debugger console
  function help()
  {
    return "No help available";
  }
  /// \b Must have functio to show exampla of usage of given command
  function example()
  {
    return "No example available";
  }
  /// Execute command with given set of arguments. Must return string of result.
  function execute($params)
  {
    return "No result";
  }
}
?>
