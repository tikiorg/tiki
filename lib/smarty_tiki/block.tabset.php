<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
	global $prefs, $smarty_tabset_name, $smarty_tabset, $smarty_tabset_i_tab, $cookietab, $headerlib, $smarty;

	if ($smarty->get_template_vars('print_page') == 'y' || $prefs['layout_tabs_optional'] === 'n') {
		$params['toggle'] = 'n';
	}
	if ( $repeat ) {
		// opening 
		$smarty_tabset = array();
		if (!isset($smarty_tabset_i_tab)) {
			$smarty_tabset_i_tab = 1;
		}
		if ( isset($params['name']) and !empty($params['name']) ) {
			$smarty_tabset_name = $params['name'];
		} else {
			$smarty_tabset_name = "tiki_tabset";
		}
		global $smarty_tabset_name, $smarty_tabset;
		return;
	} else {
		$content = trim($content);
		if (empty($content)) {
			return '';
		}
		$ret = ''; $notabs = '';
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
				$notabs = "<div class='tabstoggle floatright'>$notabs</div>";
				$content_class = '';
			} else {
				$content_class = ' full_width';
			}
		} else {
			return $content;
		}
		if ( isset($_COOKIE["tabbed_$smarty_tabset_name"]) && $_COOKIE["tabbed_$smarty_tabset_name"] == 'n' ) {
			return $ret.$notabs.$content;
		}
		$ret .= '<div class="clearfix tabs">' . $notabs;
		$max = $smarty_tabset_i_tab - 1;
		$ini = $smarty_tabset_i_tab - count($smarty_tabset);
		$focus = $ini;
		if ($prefs['mobile_feature'] === 'y' && $prefs['mobile_mode'] === 'y') {
			$mobile_div_data = ' data-role="controlgroup" data-type="horizontal"';
			$mobile_a_data = ' data-role="button"';
		} else {
			$mobile_div_data = '';
			$mobile_a_data = '';
		}
		$ret .= '<div class="container' . $content_class . '"'. $mobile_div_data.'>';
		foreach ($smarty_tabset as $value) {
			$ret .= '<span id="tab'.$focus.'" class="tabmark '.($focus == $cookietab ? 'tabactive' : 'tabinactive').'">'.
				'<a href="#' . ( empty($mobile_a_data) ? 'content'.$focus.'"' : '"' ) .
				' onclick="tikitabs('.$focus.','.$max.','.$ini.'); return false;"'.$mobile_a_data.'>'.$value.'</a></span>';
			++$focus;
		}
		$ret .= "</div></div>$content";
		if ($cookietab < $ini || $cookietab > $max) { // todo:: need to display the first tab
			$headerlib->add_jq_onready("tikitabs($ini, $max, $ini);");
		}
		return $ret;
	}
}
