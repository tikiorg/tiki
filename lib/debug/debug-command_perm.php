<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/debug/debug-command_perm.php,v 1.1 2003-07-13 00:35:40 zaufi Exp $
 *
 * \brief Show current permissions in a convenient way
 *
 * \author zaufi <zaufi@sendmail.ru>
 *
 */
require_once('lib/debug/debugger-ext.php');

/**
 * \brief Debugger command to print smatry vars
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
  /// \b Must have function to show exampla of usage of given command
  function example()
  {
    return 'perm'."\n".'perm admin'."\n".'perm .*_comments$';
  }
  /// Execute command with given set of arguments. Must return string of result.
  function execute($params)
  {
    $this->set_result_type(TPL_RESULT);
    $this->set_result_tpl('debug/tiki-debug_permissions.tpl');
    // Is regex to match against var name given?
    $p = explode(" ", trim($params));
    $mask = count($p) > 0 ? str_replace('$', '', trim($p[0])) : '';
    // Get list of all vars
    global $smarty;
    $tpl_vars = $smarty->get_template_vars();
    // convert to vector of names, filter permissions only, and sort
    $perms = array();
    $len = strlen($mask);
    foreach ($tpl_vars as $key => $val)
    {
      if ((!$len || $len && preg_match('/'.$mask.'/', $key))
       && preg_match('/tiki_p_/', $key))
        $perms[] = array('name' => $key, 'value' => $val);
    }
    return $perms;
  }
}
/// Class factory to create instances of defined commands
function dbg_command_factory_perm()
{
  return new DbgPermissions();
}

?>
