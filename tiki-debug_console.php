<?php
//
// $Header: /cvsroot/tikiwiki/tiki/tiki-debug_console.php,v 1.1 2003-07-13 00:18:56 zaufi Exp $
//

// Get current URL
$console_parsed = parse_url($_SERVER["REQUEST_URI"]);
$smarty->assign('console_father', $console_parsed["path"]);

// Set default value
$smarty->assign('result_type', NO_RESULT);

// Exec user command in internal debugger
if (isset($_REQUEST["command"]))
{
  require_once('lib/debug/debugger.php');
  // Exec command in debugger
  $command_result = $debugger->execute($_REQUEST["command"]);

  $smarty->assign('command', $_REQUEST["command"]);
  $smarty->assign('result_type', $debugger->result_type());

  // If result need temlate then we have $command_result array...
  if ($debugger->result_type() == TPL_RESULT)
  {
    $smarty->assign('result_tpl', $debugger->result_tpl());
    $smarty->assign_by_ref('command_result', $command_result);
  }
  else $smarty->assign('command_result', $command_result);
}

?>
