<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/smarty_tiki/modifier.dbg.php,v 1.2 2004-07-08 12:50:37 damosoft Exp $
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
