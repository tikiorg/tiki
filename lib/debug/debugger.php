<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/debug/debugger.php,v 1.1 2003-07-13 00:35:40 zaufi Exp $
 *
 * \brief Tiki internal debugger 
 *
 * \author zaufi <zaufi@sendmail.ru>
 *
 */

require_once('lib/debug/debugger-common.php');

/// Path to debugger's external commands
define('DBG_PLUGINS_DIR', 'lib/debug');

/**
 * \brief Class to manage debugger
 */
class Debugger extends ResultType
{
  /// Array of found command providers
  var $commands;

  /// Constructor
  function Debugger()
  {
    // build array of available commands
    $this->rescan_for_commands();
  }

  /// Rebuild commands list
  function rescan_for_commands()
  {
    $files = array();
    if (is_dir(DBG_PLUGINS_DIR))
    {
      if ($dh = opendir(DBG_PLUGINS_DIR))
      {
        while (($file = readdir($dh)) !== false)
        {
          if (preg_match("/^debug-command_.*\.php$/",$file))
            array_push($files, $file);
        }
        closedir($dh);
      }
    }
    // Refresh object in commands array
    $this->commands = array();
    foreach ($files as $file)
    {
      include_once(DBG_PLUGINS_DIR.'/'.$file);
      $func_name = preg_replace(",debug-command_([A-Za-z0-9]+)\.php,","dbg_command_factory_\\1", $file);
      if (function_exists($func_name))
      {
	$obj = $func_name();
        $this->commands[$obj->name()] = $obj;
      }
    }
  }

  /// Handle user typed command
  function execute($rawcmd)
  {
    $this->reset();
    $rawcmd = trim($rawcmd);
    $result = '';
    // Is smth else in command line 'cept spaces?
    if (strlen($rawcmd))
    {
      // Extract first word (possible the only) from command line...
      $cmd = substr($rawcmd, 0, (($pos = strpos($rawcmd, ' ')) == false ? strlen($rawcmd) : $pos));
      // Check for the only internal command: help :)
      if (strcmp($cmd, 'help') !== 0)
      {
	// No this is smth other... Is assiciated handler present?
	if (isset($this->commands[$cmd]))
	{
	  // OK. May call external command...
	  $result = $this->commands[$cmd]->execute(str_replace($cmd, '', $rawcmd));
	  $this->set_result_type($this->commands[$cmd]->result_type());
          if ($this->result_type() == TPL_RESULT)
	    $this->set_result_tpl($this->commands[$cmd]->result_tpl());
	}
	else
	{
	  // Command not found... Issue a spam!
	  $result = '<font color="red">No such command "'.$cmd.'"</font>';
	  $this->set_result_type(HTML_RESULT);
	}
      }
      else
      {
	// Handle help command. Is help for some command needed?
	$rawcmd = trim(str_replace($cmd, "", $rawcmd));
	$result = array();
        $result["action"] = 'none';
	if (strlen($rawcmd) == 0)
	{
          $result["action"] = 'list';
	  // No. This is along help on command line. Append my help...
	  $result[0][] = array('cmd' => 'help', 
            'description'=>'Display list of commands or help for specified command (<code>help print</code> for example)');
	  foreach ($this->commands as $cmdobj)
	    $result[0][] = array('cmd' => $cmdobj->name(),
                                 'description' => $cmdobj->description());
	}
	else
	{
	  // What command help requested for??
	  if (isset($this->commands[$rawcmd]))
	  {
            $result["action"] = 'one';
	    $cmdobj = $this->commands[$rawcmd];
	    $result['name'] = $cmdobj->name();
	    $result['description'] = $cmdobj->description();
	    $result['syntax'] = $cmdobj->syntax();
	    $result['example'] = $cmdobj->example();
	  }
	  else
          {
            $this->set_result_type(HTML_RESULT);
            return '<tr><td><font color="red">No such command "'.$rawcmd.'"</font></td></tr>';
          }
	}
	$this->set_result_type(TPL_RESULT);
        $this->set_result_tpl('debug/tiki-debug_console_help.tpl');
      }
    }
    return $result;
  }
}


$debugger = new Debugger();
