<?php
/*
 * \brief Add help via icon to a page
 * @author: Stéphane Casset
 * @date: 06/11/2008
 * @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
 * $Id$
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_add_help($params, $content, &$smarty, &$repeat) {
	global $prefs;
	global $help_sections;

	if (!isset($content)) return ;
	
	if (isset($params['title'])) $section['title'] = $params['title'];
	if (isset($params['id'])) {
		$section['id'] = $params['id'];
	} else {
		$section['id'] = $params['id'] = 'help_section_'.sizeof($help_sections);
	}
	$section['content'] = $content;

	$help_sections[$params['id']] = $section;

	if (!isset($params['show']) or $params['show'] == 'y') {
		require_once $smarty->_get_plugin_filepath('block', 'self_link');
		$self_link_params['alt'] = $params['title'];
		$self_link_params['_icon'] = 'help';
		if ($prefs['feature_shadowbox'] == 'y' and ($prefs['feature_jquery'] == 'y' || $prefs['feature_mootools'] == 'y')) {
			require_once $smarty->_get_plugin_filepath('function', 'icon');
			$self_link_params['_id'] = 'help';
			return '<a href="#'.$section['id'].'" rel="shadowbox[add_help];title='.$params['title'].';">'.smarty_function_icon($self_link_params,$smarty).'</a>';
		} else {
			$self_link_params['_onclick'] = "javascript:show('help_sections');show('".$section['id']."');return false";
			return smarty_block_self_link($self_link_params,"",$smarty);
		}
	} else {
		return ;
	}
}
