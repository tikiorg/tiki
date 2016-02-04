<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.var_dump.php 53803 2015-02-06 00:42:50Z jyhem $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


/**
 * \brief Smarty plugin to explode a string into an array
 * Usage format {explode assign="testArr" delimiter=":" string="abc:123:zyx"}
 *
 * Adapted to do more than string for tiki 5
 */
function smarty_function_explode($params, &$smarty)
{
	if (empty($params['assign']) || empty($params['delimiter']) || empty($params['string'])) {
		return;
	}

	$result = explode($params['delimiter'],$params['string']);

	$smarty->assign($params['assign'], $result);
}
