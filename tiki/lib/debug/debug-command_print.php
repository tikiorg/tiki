<?php
//
// $Header: /cvsroot/tikiwiki/tiki/lib/debug/debug-command_print.php,v 1.1 2003-07-13 00:35:40 zaufi Exp $
//
// Command to print PHP variables to debug console
//
require_once('lib/debug/debugger-ext.php');

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
  /// Execute command with given set of arguments. Must return string of result.
  function execute($params)
  {
    $this->set_result_type(TEXT_RESULT);
    $result = '';
    $vars = explode(" ", $params);
    foreach ($vars as $v)
    {
      $vv = str_replace("$","",trim($v));
      if (strlen($vv) == 0) continue;
      // Make var global... strip [] if needed
      $global = (($pos = strpos($v, '[')) == false) ? $v : substr($v, 0, $pos);
      global $$global;
      //
      $expr = "\$result .= print_r($v, true);";
      $result .= $v.' = ';
      $php_errormsg='';
      @eval($expr);
      if (strlen($php_errormsg)) $result .= "\n\t".$php_errormsg;
      $result .= "\n";
    }
    return $result;
  }
};

/// Class factory to create instances of defined commands
function dbg_command_factory_print()
{
  return new DbgPrint();
}
