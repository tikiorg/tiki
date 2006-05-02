<?php
/*
 *
 * TWOCOLORTITLEBOX plugin. Creates a Tikipedia-look box.
 *
 * Syntax:
 *
 *  {TWOCOLORTITLEBOX(title=>)}
 *   content
 *  {TWOCOLORTITLEBOX}
 *
 */
function wikiplugin_twocolortitlebox_help() {
	return tra("Insert a Tikipedia-look box on wiki page").":<br />~np~{TWOCOLORTITLEBOX(title1=>[text], title2=> [text], title1color=> [hex], title2color=> [hex])}".tra("text")."{TWOCOLORTITLEBOX}~/np~";
}

function wikiplugin_twocolortitlebox($data, $params) {

	extract ($params);

	$div1 = "<div style=\"border:0; margin:0.2em 0 0.2em 0.2em;\"><div style=\"background:#f9f9f9; padding:0px; border:1px solid #aaaaaa; margin-bottom:5px;\">";
	$div2 = "<div style=\"line-height:120%; padding:0.8em; background-color:#eeeeee; border-bottom:1px solid #aaaaaa;\">";
	$t1style = "<span style=\"font-size:200%; font-weight:bold; color: ";
/* color */
	$t1styleend = "\";>";
/* title1 */
	$t2style = "</span>&nbsp;<span style=\"font-size:200%; font-weight:bold; color: ";
/* color 2 */
	$t2styleend = "\";>";
/* title2 */
	$middle = "</span></div><div class=\"wikitext\">";
	$end = "</div></div></div>";
	return $div1 . $div2 . $t1style . $title1color .$t1styleend . $title1 .  $t2style . $title2color . $t2styleend .  $title2 . $middle.$data.$end;
}
?>