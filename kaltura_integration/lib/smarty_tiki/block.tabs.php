<?php
/* $Id$ */

// this script may only be included - so it's better to die if called directly
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * \brief smarty_block_tabs : add tabs to a template
 *
 * params: TODO
 *
 * usage: 
 * \code
 *	{tabs}
 * 		{tr}First Tab{/tr}|
 *		{tr}Second Tab{/tr}|
 *		{tr}Third Tab{/tr}
 *	{/tabs}
 * \endcode
 *
 */

function smarty_block_tabs($params, $content, &$smarty) {
	global $prefs;
	
	if ( $content == '' ) {
		return;
	} else {
		
		$tabs = split("\|", trim($content));
		$ret = '<div class="tabs">
			';
		$i = 1;
		$max = 0;
		foreach ($tabs as $value) {
			$max++;
		}
		foreach ($tabs as $value) {
			$ret .= '	<span id="tab'.$i.'" class="tabmark tabinactive"><a href="#content'.$i.'" onclick="javascript:tikitabs('.$i.','.$max.'); return false;">'.$value.'</a></span>
			';
			$i++;
		}
		$ret .= '</div>';
		return $ret;
	}
}
