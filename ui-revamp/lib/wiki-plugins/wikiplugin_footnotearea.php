<?php
/* by robferguson
 *
 * FOOTNOTEAREA plugin. Inserts a section for collected footnotes created with the FOOTNOTES plugin .
 * 
 * Syntax:
 * 
 * {FOOTNOTEAREA()/}
 */
function wikiplugin_footnotearea_help() {
	return tra("Inserts a section for collected footnotes within the wiki page").":<br />~np~{FOOTNOTEAREA()/}~/np~";
}

function wikiplugin_footnotearea($data, $params) {

	extract ($params,EXTR_SKIP);
	foreach($GLOBALS["footnotesData"] as $key => $value){
		$footnoteOuput .= "<sup>". ($key + 1) ."</sup>".$value."<br />";
	}
	return "<div style=\"border-top:2px solid #999;float:left;min-width:300px;font-size:11px;\">$footnoteOuput</div>";
}
?>


