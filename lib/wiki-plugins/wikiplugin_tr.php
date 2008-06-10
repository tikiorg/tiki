<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_trackerlist.php,v 1.40.2.12 2008-03-22 12:13:54 sylvieg Exp $

function wikiplugin_tr() {
	$help = tra("Translate a string");
	$help .= "~np~{TR()}string{TR}~/np~";
	return $help;
}
function wikiplugin_tr($data) {
	return tra($data);
}