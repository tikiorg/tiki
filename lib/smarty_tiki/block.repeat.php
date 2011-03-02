<?php 
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* 
* Smarty plugin 
* ------------------------------------------------------------- 
* File: block.repeat.php 
* Type: block 
* Name: repeat 
* Purpose: repeat a template block a given number of times 
* Parameters: count [required] - number of times to repeat 
* assign [optional] - variable to collect output 
* Author: Scott Matthewman <scott@matthewman.net> 
* ------------------------------------------------------------- 
*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
 
function smarty_block_repeat($params, $content, &$smarty) 
{ 
if (!empty($content)) { 
$intCount = intval($params['count']); 
if($intCount < 0) { 
$smarty->trigger_error("block: negative 'count' parameter"); 
return; 
} 
  
$strRepeat = str_repeat($content, $intCount); 
if (!empty($params['assign'])) { 
$smarty->assign($params['assign'], $strRepeat); 
} else { 
echo $strRepeat; 
} 
} 
} 
