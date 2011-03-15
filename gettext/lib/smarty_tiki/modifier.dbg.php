<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/** \file
 * $Id$
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
