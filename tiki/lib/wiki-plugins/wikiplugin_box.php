<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_box.php,v 1.12 2004-08-20 07:14:16 chealer Exp $
 *
 * Tiki-Wiki BOX plugin.
 * 
 * Syntax:
 * 
 *  {BOX([title=>Title],[bg=>color|#999fff],[width=>num[%]],[align=>left|right|center])}
 *   Content inside box
 *  {BOX}
 * 
 */

function wikiplugin_box_help() {
	return tra("Insert theme styled box on wiki page").":<br />~np~{BOX(title=>Title, bg=>color, width=>num[%], align=>left|right|center)}".tra("text")."{BOX}~/np~";
}

function wikiplugin_box($data, $params) {
	global $smarty;
	
	/* set default values for some args */
	$title = tra("Message box");
	
	// Remove first <ENTER> if exists...
	if (substr($data, 0, 2) == "\r\n") 
	$data = substr($data, 2);
	
	extract ($params);
	
	if (isset($align)) $smarty->assign('plugin_box_align', $align ? $align : '');
	if (isset($width)) $smarty->assign('plugin_box_width', $width ? $width : '');
	if (isset($bg)) $smarty->assign('plugin_box_bg',  $bg ? $bg : '');
	
	$smarty->assign('plugin_box_title', $title);
	
	// Prepend any newline char with br
	$data = preg_replace("/\\n/", "<br />\n", $data);
	// Insert "\n" at data begin if absent (so start-of-line-sensitive syntaxes will be parsed OK)
	if (substr($data, 0, 1) != "\n") 
    	$data = "\n".$data;
	
	$smarty->assign('plugin_box_data', $data);
	
	return $smarty->fetch('plugins/plugin-box.tpl');
}

?>
