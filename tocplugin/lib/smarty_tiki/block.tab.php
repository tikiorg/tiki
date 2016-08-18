<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
 *  
 * @param array $params - params are passed through the array params and available under their key. i.e $params['name']
 * The following params are supported via $params as keys:
 * string name - name of the tab
 * string print - 'y' this tab will be printed (by setting the class active flag) 
 * integer key  ???? 
 * @param string $content - content of the tab
 * @param object $smarty - ref to smarty instance
 * @param ref $repeat - ????
 * 

 *
 * usage:
 * \code
 *	{tab name="myname" print=1}
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
	$smarty = TikiLib::lib('smarty');
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
		} else {
			// if we print a page then then all tabs would be "not active" so hidden and we would print nothing.
			// we cannot click something so no js handler involed. thats we use the defaultActive
			// so get the cookietab as the enabled tab.
			$active =  (isset($params['print']) && $params['print'] == 'y') ? 'active' : '';
		}
		
		$ret = "<div id='{$id}' class='tab-pane $active'>$content</div>";
		
		return $ret;
	}
}
