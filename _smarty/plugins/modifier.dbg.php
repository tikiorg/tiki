<?php
/** \file
 * $Header: /cvsroot/tikiwiki/_smarty/plugins/modifier.dbg.php,v 1.1 2003-08-20 23:53:38 rossta Exp $
 *
 * \author zaufi <zaufi@sendmail.ru>
 */

/**
 * \brief Smarty modifier plugin to add string to debug console log w/o modify output
 * Usage format {$smarty_var|dbg}
 */
function smarty_modifier_dbg($string, $label = '')
{
  global $debugger;
  require_once('lib/debug/debugger.php');
  //
  $debugger->msg('Smarty log'.((strlen($label) > 0) ? ': '.$label : '').': '.$string);
  return $string;
}

?>
