<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// \brief Command to print PHP variables to debug console
require_once('lib/debug/debugger-ext.php');

/**
 * \brief Command to print PHP variables to debug console
 */
class DbgPrint extends DebuggerCommand
{
  /// \b Must have function to announce command name in debugger console
  function name()
  {
    return 'print';
  }
  /// \b Must have function to provide help to debugger console
  function description()
  {
    return 'Print PHP variable. Indexes are OK.';
  }
  /// \b Must have function to provide help to debugger console
  function syntax()
  {
    return 'print $var1 $var2 var3 ...';
  }
  /// \b Must have functio to show exampla of usage of given command
  function example()
  {
    return 'print $_REQUEST'."\n".'print $_SERVER["REQUEST_URI"] $my_private_variable';
  }
  /// Execute command with given set of arguments.
  function execute($params)
  {
    global $debugger;
    require_once('lib/debug/debugger.php');
    //
    $this->set_result_type(TEXT_RESULT);
    $result = '';
    $vars = explode(" ", $params);
    foreach ($vars as $v)
    {
      if (strlen(str_replace("$", "", trim($v))) == 0) continue;
      $result .= $v.' = ';
      $result .= trim($debugger->str_var_dump($v))."\n";
    }
    return $result;
  }
};

/// Class factory to create instances of defined commands
function dbg_command_factory_print()
{
  return new DbgPrint();
}
