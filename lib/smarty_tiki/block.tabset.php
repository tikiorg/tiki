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
 * params: name (optional but unique per page if set)
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

static $tabset_index = 0;

function smarty_block_tabset($params, $content, &$smarty, &$repeat) {
	global $prefs, $smarty_tabset_name, $smarty_tabset, $smarty_tabset_i_tab, $cookietab, $headerlib, $smarty, $tabset_index, $tikilib;


	if ($smarty->get_template_vars('print_page') == 'y' || $prefs['layout_tabs_optional'] === 'n') {
		$params['toggle'] = 'n';
	}
	if ( $repeat ) {
		// opening 
		$tabset_index++;
		if ( isset($params['name']) and !empty($params['name']) ) {
			$smarty_tabset_name = $params['name'];	// names have to be unique
		} else {
			$short_name = str_replace(array('tiki-', '.php'), '', basename($_SERVER['SCRIPT_NAME']));
			$smarty_tabset_name = 't_' . $short_name . $tabset_index;
		}
		$smarty_tabset_name = preg_replace('/[\s,\/\|]+/', '_', $tikilib->take_away_accent( $smarty_tabset_name ));	// TODO refactor into clean_string - see e.g. toolbarslib?
		if (!is_array($smarty_tabset)) {
			$smarty_tabset = array();
		}
		$smarty_tabset[$tabset_index] = array( 'name' => $smarty_tabset_name, 'tabs' => array());
		if (!isset($smarty_tabset_i_tab)) {
			$smarty_tabset_i_tab = 1;
		}

		$cookietab = getCookie($smarty_tabset_name, 'tabs', 1);
		// work out cookie value if there
		if( isset($_REQUEST['cookietab']) && $tabset_index === 1) {	// overrides cookie if added to request as in tiki-admin.php?page=look&cookietab=6
			$cookietab = empty($_REQUEST['cookietab']) ? 1 : $_REQUEST['cookietab'];
			setCookieSection( $smarty_tabset_name, $cookietab, 'tabs' );
		}


		$smarty_tabset_i_tab = 1;

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
				if ($cookietab == 'n') {
					$button_params['_text'] = tra('Tab View');
				} else {
					$button_params['_text'] = tra('No Tabs');
				}
				$button_params['_auto_args']='*';
				$button_params['_onclick'] = "setCookie('$smarty_tabset_name','".($cookietab == 'n' ? 1 : 'n' )."', 'tabs') ;";
				$notabs = smarty_function_button($button_params,$smarty);
				$notabs = "<div class='tabstoggle floatright'>$notabs</div>";
				$content_class = '';
			} else {
				$content_class = ' full_width';	// no no-tabs button
			}
		} else {
			return $content;
		}
		if ( $cookietab == 'n' ) {
			return $ret.$notabs.$content;
		}

		$ret .= '<div class="clearfix tabs" data-name="' . $smarty_tabset_name . '">' . $notabs;

		$count = 1;
		if ($prefs['mobile_feature'] === 'y' && $prefs['mobile_mode'] === 'y') {
			$mobile_div_data = ' data-role="controlgroup" data-type="horizontal"';
			$mobile_a_data = ' data-role="button"';
		} else {
			$mobile_div_data = '';
			$mobile_a_data = '';
		}
		$ret .= '<div class="container' . $content_class . '"'. $mobile_div_data.'>';
		foreach ($smarty_tabset[$tabset_index]['tabs'] as $value) {
			$ret .= '<span class="tabmark tab'.$count.' '.($count == $cookietab ? 'tabactive' : '').'">'.
				'<a href="#' . ( empty($mobile_a_data) ? 'content'.$count.'"' : '"' ) .
				' onclick="tikitabs('.$count.',this); return false;"'.$mobile_a_data.'>'.$value.'</a></span>';
			++$count;
		}
		$ret .= "</div></div>$content";

		// add some jq to initialize the tab, needed when page is cached
		$headerlib->add_jq_onready('tikitabs(getCookie("'.$smarty_tabset_name.'","tabs",1), $("div[data-name='.$smarty_tabset_name.'] .tabmark:first"));');

		$tabset_index--;
		if ($tabset_index > 0) {
			$smarty_tabset_name = $smarty_tabset[$tabset_index]['name'];
			$cookietab = getCookie($smarty_tabset_name, 'tabs', 1);
		}
		return $ret;
	}
}
