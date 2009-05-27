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
 * params: TODO
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


	if ( $repeat ) {
		// opening 
		$smarty_tabset = array();
		if ( isset($params['name']) and !empty($params['name']) ) {
			$smarty_tabset_name = $params['name'];
		} else {
			$smarty_tabset_name = "tiki_tabset";
		}
		global $smarty_tabset_name, $smarty_tabset;
		if (isset($_REQUEST['tabbed_'.$smarty_tabset_name])) {
			$_SESSION["tabbed_$smarty_tabset_name"] = $_REQUEST['tabbed_'.$smarty_tabset_name] ;
		}
		return;
	} else {
		//closing
		if ( $prefs['feature_tabs'] == 'y') {
			require_once $smarty->_get_plugin_filepath('function','button');
			if (isset($_SESSION["tabbed_$smarty_tabset_name"]) and $_SESSION["tabbed_$smarty_tabset_name"] == 'n') {
				$button_params['_text'] = tra('Tab View');
			} else {
				$button_params['_text'] = tra('No Tab');
			}
			$notabs = '<input type="hidden" name="tabbed_'.$smarty_tabset_name.'" value="'.($_SESSION["tabbed_$smarty_tabset_name"] == 'n' ? 'n' : 'y').'"/>';
			$button_params['_onclick'] = "tabbed_input = document.getElementsByName('tabbed_$smarty_tabset_name')[0]; tabbed_input.value ='".($_SESSION["tabbed_$smarty_tabset_name"] == 'n' ? 'y' : 'n' )."' ; tabbed_input.form.submit();";
			$notabs .= smarty_function_button($button_params,$smarty);
		} else {
			return $content;
		}
		$ret = "<div class='floatright'>$notabs</div><br class='clear'/>";
		if ( $_SESSION["tabbed_$smarty_tabset_name"] == 'n' ) {
			return $ret.$content;
		}
		$ret .= '<div class="tabs">
			';
		$max = sizeof($smarty_tabset);
		$i = 1;
		foreach ($smarty_tabset as $value) {
			$ret .= '	<span id="tab'.$i.'" class="tabmark tabinactive"><a href="#content'.$i.'" onclick="javascript:tikitabs('.$i.','.$max.'); return false;">'.$value.'</a></span>
				';
			$i++;
		}
		//$ret .= '<span class="tabmark tabinactive"><a href="#">'.$notabs.'</a></span></div>'.$content;
		$ret .= "</div>$content";
		return $ret;
	}
}
