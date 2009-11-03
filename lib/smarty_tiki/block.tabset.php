<?php
/* $Id:  $ */

// this script may only be included - so it's better to die if called directly
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * \brief smarty_block_tabs : add tabs to a template
 *
 * params: name
 * params: toggle=y on n default
 *
 * usage: 
 * \code
 *	{tabset name='tabs}
 * 		{tab name='tab1'}tab content{/tab}
 * 		{tab name='tab2'}tab content{/tab}
 * 		{tab name='tab3'}tab content{/tab}
 *	{/tabset}
 * \endcode
 *
 */

function smarty_block_tabset($params, $content, &$smarty, &$repeat) {
	global $prefs, $smarty_tabset_name, $smarty_tabset;
	static $i_tabset;

	if ( $repeat ) {
		// opening 
		$smarty_tabset = array();
		if ( isset($params['name']) and !empty($params['name']) ) {
			$smarty_tabset_name = $params['name'];
		} else {
			$smarty_tabset_name = "tiki_tabset";
		}
		global $smarty_tabset_name, $smarty_tabset;
		return;
	} else {
		$ret = '';
		//closing
		if ( $prefs['feature_tabs'] == 'y') {
			if (empty($params['toggle']) || $params['toggle'] != 'n') {
				require_once $smarty->_get_plugin_filepath('function','button');
				if (isset($_COOKIE["tabbed_$smarty_tabset_name"]) and $_COOKIE["tabbed_$smarty_tabset_name"] == 'n') {
					$button_params['_text'] = tra('Tab View');
				} else {
					$button_params['_text'] = tra('No Tabs');
				}
				$button_params['_auto_args']='*';
				$button_params['_onclick'] = "setCookie('tabbed_$smarty_tabset_name','".((isset($_COOKIE["tabbed_$smarty_tabset_name"]) && $_COOKIE["tabbed_$smarty_tabset_name"] == 'n') ? 'y' : 'n' )."') ;";
				$notabs = smarty_function_button($button_params,$smarty);
				$ret = "<div class='tabstoggle floatright'>$notabs</div><br class='clear'/>";
			}
		} else {
			return $content;
		}
		if ( isset($_COOKIE["tabbed_$smarty_tabset_name"]) && $_COOKIE["tabbed_$smarty_tabset_name"] == 'n' ) {
			return $ret.$content;
		}
		$ret .= '<div class="tabs">
			';
		$max = sizeof($smarty_tabset);
		if (empty($i_tabset)) {
			$i_tabset = 1;
		}
		foreach ($smarty_tabset as $value) {
			$ret .= '	<span id="tab'.$i_tabset.'" class="tabmark tabinactive"><a href="#content'.$i_tabset.'" onclick="javascript:tikitabs('.$i_tabset.','.$max.'); return false;">'.$value.'</a></span>
				';
			$i_tabset++;
		}
		//$ret .= '<span class="tabmark tabinactive"><a href="#">'.$notabs.'</a></span></div>'.$content;
		$ret .= "</div>$content";
		return $ret;
	}
}
