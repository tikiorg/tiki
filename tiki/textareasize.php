<?php
/* $Header: /cvsroot/tikiwiki/tiki/textareasize.php,v 1.4 2005-08-25 20:50:04 michael_davey Exp $
 * \brief: 4 buttoms to change a textArea size - works with textareaSize.tpl template
 * \param: $defaultRows - optional : the number of rows by default
 * \comment: the cols nb is managed by a javascript program to optimize the display perf (seems to work generally with browsers)
 *                  the rows nb is managed by a redisplayed of the page to be effective on different browsers and different styles (specially with floating div column)
 */

//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

$rows = isset($_REQUEST['rows'])? $_REQUEST['rows']: (isset($defaultRows)?$defaultRows: 20);
// the rows modification is managed by a javascript program
// is ok: for IE6, firebird 0.6, opera 7.23, NN7
// is doesn't work for other browser, must be managed as rows input
$smarty->assign('rows', (int) $rows);

$cols = isset($_REQUEST['cols'])? $_REQUEST['cols']: 80;
if (isset($_REQUEST['enlargeW']) || isset($_REQUEST['enlargeW_x']))
	$cols += 20;
elseif ((isset($_REQUEST['reduceW']) || isset($_REQUEST['reduceW_x'])) && $cols >= 40)
	$cols -= 20;
$smarty->assign('cols', (int) $cols);

?>
