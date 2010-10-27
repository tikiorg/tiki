<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * \brief Add help via icon to a page
 * @author: Stéphane Casset
 * @date: 06/11/2008
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
		global $headerlib;
		require_once $smarty->_get_plugin_filepath('block', 'self_link');
		$self_link_params['_alt'] = tra('Click for Help');
		$self_link_params['_icon'] = 'help';
		$self_link_params['_ajax'] = 'n';
		
		$title = tra('Help');
		
		$headerlib->add_js('
var openEditHelp = function() {
	var opts, edithelp_pos = getCookie("edithelp_position");
	opts = { width: 460, height: 500, title: "' . $title . '", autoOpen: false, beforeclose: function(event, ui) {
		var off = $(this).offsetParent().offset();
   		setCookie("edithelp_position", parseInt(off.left,10) + "," + parseInt(off.top,10) + "," + $(this).offsetParent().width() + "," + $(this).offsetParent().height());
	}};
	if (edithelp_pos) {edithelp_pos = edithelp_pos.split(",");}
	if (edithelp_pos && edithelp_pos.length) {
		opts["position"] = [parseInt(edithelp_pos[0],10), parseInt(edithelp_pos[1],10)];
		opts["width"] = parseInt(edithelp_pos[2],10);
		opts["height"] = parseInt(edithelp_pos[3],10);
	}
	try {
		if ($("#help_sections").dialog) {
			$("#help_sections").dialog("destroy");
		}
	} catch( e ) {
		// IE throws errors destroying a non-existant dialog
	}
	$("#help_sections").dialog(opts).dialog("open");
	
};');
		$self_link_params['_onclick'] = 'openEditHelp();return false;';
 
		return smarty_block_self_link($self_link_params,"",$smarty);
	} else {
		return ;
	}
}
