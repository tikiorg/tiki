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
		global $headerlib;
		$headerlib->include_jquery_ui();
		require_once $smarty->_get_plugin_filepath('block', 'self_link');
		$self_link_params['alt'] = $params['title'];
		$self_link_params['_icon'] = 'help';
		$self_link_params['_ajax'] = 'n';
		//$self_link_params['_onclick'] = "javascript:show('help_sections');show('".$section['id']."');return false";
		$self_link_params['_onclick'] = '$jq(\'#help_sections\').dialog({ width: 460, height: 500, title: \''.$section['title'].'\' }).dialog(\'open\');return false;';
		return smarty_block_self_link($self_link_params,"",$smarty);
	} else {
		return ;
	}
}
