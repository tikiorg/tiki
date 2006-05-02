<?php
/*
 *
 * THUMBDIV plugin. Inserts a Mediawiki/Wikipedia-type thumbnail that links to a full-size image.
 *
 * Syntax:
 *
 *  {THUMBDIV(some parameters)}{THUMBDIV}
 *
 * Syntax:
 *
 * {THUMBDIV(align=>, width=>, imgid=>, title=>, alt=>, thumbwidth=>, thumbheight=>)}caption{THUMBDIV}
 *
 */

function wikiplugin_thumbdiv_help() {
	return tra("Inserts a Mediawiki/Wikipedia-look thumbnail with caption that links to a full-size image.").":<br />~np~{THUMBDIV(align=>[right|left], width=>[number and px|%], imgid=>, title=>, alt=>, thumbwidth=>[number only], thumbheight=>[number only])}".tra("text")."{THUMBDIV}~/np~";
}

function wikiplugin_thumbdiv($data,$params) {
	global $tikilib;

	global $replacement;
	extract ($params,EXTR_SKIP);

	if (!isset($align)) {
		return ("<b>Align parameter(tleft, tright) is missing.</b><br />");
}
	if (!isset($width)) {
	return ("<b>Box width parameter is missing.</b><br />");
}
	if (!isset($imgid)) {
	return ("<b>image ID parameter is missing.</b><br />");
}
	if (!isset($title)) {
	return ("<b>Thumb title parameter is missing.</b><br />");
}
	if (!isset($alt)) {
	return ("<b>Thumb alt parameter is missing.</b><br />");
}
	if (!isset($thumbwidth)) {
	return ("<b>Thumb image width parameter is missing.</b><br />");
}
	if (!isset($thumbheight)) {
	return ("<b>Thumb image height parameter is missing.</b><br />");
}
	if (!isset($data)) {
	return ("<b>Thumb caption is missing.</b><br />");
}


$one = "<div class=\"thumb t$align\"><div style=\"width:$width;\"><a href=\"tiki-browse_image.php?imageId=";

$two = "\" class=\"internal\" title=\"$title\"><img src=\"show_image.php?id=";

$three = "&thumb=1\" alt=\"$alt\" width=\"$thumbwidth\" height=\"$thumbheight\"  /></a><div class=\"thumbcaption\">";
$four = "<div class=\"magnify\" style=\"float:right\"><a href=\"tiki-browse_image.php?imageId=";

$five = "\" class=\"internal\" title=\"Enlarge\"><img src=\"./img/icons2/icn_view.gif\" width=\"12\" height=\"12\" alt=\"Enlarge\" /></a></div>$data</div></div></div>";

return $one.$imgid.$two.$imgid.$three.$four.$imgid.$five;
}

?>