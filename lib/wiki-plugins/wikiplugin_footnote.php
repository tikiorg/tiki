<?php
/* by robferguson
 *
 * FOOTNOTE plugin. Inserts a superscripted number where the plugin is written starting with one and counting up as the additional footnotes are added.
 * 
 * Syntax:
 * 
 * {FOOTNOTE()/}
 */
function wikiplugin_footnote_help() {
	return tra("Inserts a superscripted footnote number next to text and takes in footnote as parameter").":<br />~np~{FOOTNOTE()}insert footnote here{FOOTNOTE}~/np~";
}

function wikiplugin_footnote_info() {
	return array(
		'name' => tra( 'Footnote' ),
		'documentation' => 'PluginFootnote',
		'description' => tra( 'Inserts a superscripted footnote number next to text and takes in footnote as parameter.' ),
		'prefs' => array('wikiplugin_footnote'),
		'body' => tra('The footnote'),
		'params' => array(),
	);
}

function wikiplugin_footnote($data, $params) {

	$GLOBALS["footnoteCount"]++;
	$footnoteCount = $GLOBALS["footnoteCount"];
	$GLOBALS["footnotesData"][] = $data;

	$html = '{SUP()}'
				. "<a id=\"ref_footnote$footnoteCount\" href=\"#footnote$footnoteCount\">$footnoteCount</a>"
				.	'{SUP}';

	return $html;
}
