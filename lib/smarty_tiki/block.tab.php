<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
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
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_tab($params, $content, $smarty, &$repeat)
{
	global $prefs, $smarty_tabset, $cookietab, $smarty_tabset_i_tab, $tabset_index;
	
	if ( $repeat ) {
		return;
	} else {
		$print_page = $smarty->getTemplateVars('print_page');

		$name = $smarty_tabset[$tabset_index]['name'];
		$id = null;
		$active = null;
		if ($print_page != 'y') {
			$smarty_tabset_i_tab = count($smarty_tabset[$tabset_index]['tabs']) + 1;

			if (empty($params['name'])) {
				$params['name'] = "tab" . $smarty_tabset_i_tab;
			}

			if (empty($params['key'])) {
				$params['key'] = $smarty_tabset_i_tab;
			}

			if (empty($name)) {
				$name = $tabset_index;
			}

			$id = $id = "content$name-{$params['key']}";
			$active = ($smarty_tabset_i_tab == $cookietab) ? 'active' : '';
			$def = [
				'label' => $params['name'],
				'id' => $id,
				'active' => $active,
			];
			$smarty_tabset[$tabset_index]['tabs'][] = $def;
		}
		
		$ret = "<div id='{$id}' class='tab-pane $active'>$content</div>";
		
		return $ret;
	}
}
