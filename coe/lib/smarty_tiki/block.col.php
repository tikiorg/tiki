<?php
/* $Id: block.tabs.php 17175 2009-03-04 20:43:16Z sylvieg $ */

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
 * {col name="col12 title="" class="" sort="" type=(string|numeric|user|...) _link="tiki-download_file.php?%index3%"} 
 * %index5%
 * {/col}
 * \endcode
 *
 */

function smarty_block_col($params, $content, &$smarty, &$repeat) {
	global $prefs, $smarty_tables ;
	
	if ( $repeat ) {
		return;
	} else {
		end($smarty_tables);
		$current = key($smarty_tables);
		if ( !empty($params['name']) and $current !== false) {
			$col = array();
			if (isset($params['name'])) $col['name'] = $params['name'];
			if (isset($params['title'])) $col['title'] = $params['title'];
			if (isset($params['href'])) $col['href'] = $params['href'];
			if (isset($params['class'])) $col['class'] = $params['class'];
			if (isset($params['type'])) $col['type'] = $params['type'];
			if (isset($params['sort'])) $col['sort'] = $params['sort'];
			if (!empty($content)) $col['content'] = $content;
			$smarty_tables[$current][] = $col;
			//echo"TOTO:<pre>".print_r($col,true)."</pre>";
		}
		return ;
	}
}
