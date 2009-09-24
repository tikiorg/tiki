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
 *	{tab name=}
 *  tab content
 *	{/tab}
 * \endcode
 *
 */

function smarty_block_tab($params, $content, &$smarty, &$repeat) {
	global $prefs, $smarty_tabset_name, $smarty_tabset, $cookietab;
	
	if ( $repeat ) {
		return;
	} else {
		if ( isset($params['name']) and !empty($params['name']) ) {
			$smarty_tabset[] = $params['name'];
		} else {
			$smarty_tabset[] = $params['name'] = "tab"+sizeof($smarty_tabset);
		}
		
		$ret = "<a name='tab".sizeof($smarty_tabset)."'></a>";
		$ret .= "<fieldset ";
		if ($prefs['feature_tabs'] == 'y' and (!isset($_COOKIE["tabbed_$smarty_tabset_name"]) or $_COOKIE["tabbed_$smarty_tabset_name"] != 'n')) {
   			$ret .= "id='content".sizeof($smarty_tabset)."' class='tabcontent' style='clear:both;display:".(sizeof($smarty_tabset) == $cookietab ? 'block' : 'none').";'>";
		} else {
			$ret .= "id='content".sizeof($smarty_tabset)."'>";
		}
		if ($prefs['feature_tabs'] != 'y' or (isset($_COOKIE["tabbed_$smarty_tabset_name"]) and $_COOKIE["tabbed_$smarty_tabset_name"] == 'n')) {
     		$ret .= '<legend class="heading"><a href="#"><span>'.$params['name'].'</span></a></legend>';
		}
	
		$ret .= "$content</fieldset>";
		
		return $ret;
	}
}
