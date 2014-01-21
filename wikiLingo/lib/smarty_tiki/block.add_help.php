<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * \brief Add help via icon to a page
 * @author: Stéphane Casset
 * @date: 06/11/2008
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_add_help($params, $content, $smarty, &$repeat)
{
	global $prefs;
	global $help_sections;

	if (!isset($content)) return ;
	
	if ($prefs['javascript_enabled'] != 'y') {
		return $content;
	}
	
	if (isset($params['title'])) $section['title'] = $params['title'];
	if (isset($params['id'])) {
		$section['id'] = $params['id'];
	} else {
		$section['id'] = $params['id'] = 'help_section_'.count($help_sections);
	}
	$section['content'] = $content;

	$help_sections[$params['id']] = $section;

	if (!isset($params['show']) or $params['show'] == 'y') {
		$smarty->loadPlugin('smarty_block_self_link');
		$smarty->loadPlugin('smarty_function_icon');
		$self_link_params['_alt'] = tra('Click for Help');
		$self_link_params['_ajax'] = 'n';
		$self_link_params['_anchor'] = $section['id'];
		$self_link_params['_title'] = $section['title'];
		$self_link_params['_class'] = 'help';

		$self_link_params['_onclick'] = '$.openEditHelp('.(count($help_sections)-1).');return false;';

		$link = '';
		if (empty($params['icononly']) || $params['icononly'] === 'n') {
			$link = $section['title'].'&nbsp;';
		}
		$link .= smarty_function_icon(array('_id' => 'help'), $smarty);

		return smarty_block_self_link($self_link_params, $link, $smarty);
	} else {
		return ;
	}
}
