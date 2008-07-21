<?php
/** \file
 * $Id: /cvsroot/tikiwiki/tiki/lib/debug/debugger.php,v 1.6 2004-01-15 21:55:40 mose Exp $
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

  ///Index of interface extension found as external debugger command
  var $noname_cmd_idx;

  /// Array of user logged messages. Elements are stored with keys 'timestamp' and 'msg'
  var $dmsgs;

  /// Constructor
  function Debugger()
  {
    // Call base constructor
    $this->ResultType();
    // Init data members
    $this->noname_cmd_idx = 0;
    $this->dmsgs = array();
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
        if (is_subclass_of($obj, "DebuggerCommand"))
        {
          // If command have name, insert in by name, else assume that
          // it is interface only extension
	        if (strlen($obj->name()) > 0)
    	      $this->commands[$obj->name()] = $obj;
	        else
	          $this->commands[$this->noname_cmd_idx++] = $obj;
        }
        // else 
          // TODO: Must issue a warnig?
      }
      // Must issue a warning?
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
          $result = '<span class="dbgerror">No such command "'.$cmd.'"</span>';
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
            if (strlen(trim($cmdobj->name())) > 0)
              $result[0][] = array('cmd' => $cmdobj->name(), 'description' => $cmdobj->description());
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
             return '<tr><td><span class="dbgerror">No such command "'.$rawcmd.'"</span></td></tr>';
          }
    	  }
        $this->set_result_type(TPL_RESULT);
        $this->set_result_tpl('debug/tiki-debug_console_help.tpl');
      }
    }
    return $result;
  }

  /// Request from interface module to draw plugable parts...
  function background_tabs_draw()
  {
    $result = array();
    foreach ($this->commands as $cmd)
      if ($cmd->have_interface())
        $result[$cmd->caption()] = $cmd->draw_interface();
    return $result;
  }
  /// Store user messages...
  function msg($s)
  {
    $this->dmsgs[] = array("timestamp" => time(), "msg" => $s);
  }
  /** 
   * \brief Dump variable (global) into string (errors aware function)
   * \todo Need to rename all local variables to smth realy unique
   *       (smth that user never guess and try to print :)
   */
  function str_var_dump($v)
  {
    $result = '';
    $v = trim($v);
    if (strlen(str_replace("$", "", $v)) > 0)
    {
      // Need to make var global... strip [] if needed
      $global = (($pos = strpos($v, '[')) == false) ? $v : substr($v, 0, $pos);
      //
      $expr = "global $global;\n\$result .= print_r($v, true);";
      $php_errormsg='';
      @eval($expr);
      if (strlen($php_errormsg)) $result .= "ERROR: ".$php_errormsg;
    }
    return $result;
  }
  function var_dump($v)
  {
    $this->dmsgs[] = array("timestamp" => time(), "msg" => $this->str_var_dump($v));
  }
}

global $debugger;
$debugger = new Debugger();
// First message (will not appear if no user messages wil be added)
$debugger->msg("Debugger startup OK. ".count($debugger->commands)." plugable commands found");

?>
