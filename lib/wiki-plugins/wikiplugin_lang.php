<?php
/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_lang.php,v 1.2 2003-11-09 13:56:14 sylvieg Exp $
 * Displays the content data only if the language matched the current language
 * {LANG(lang=fr)}Bon appétit{LANG}
 * 
 */
function wikiplugin_lang_help() {
	return tra("Displays the text only if the language matchs").":<br :>~np~{LANG(lang=>)}".tra("text")."{LANG}~/np~";
}

function wikiplugin_lang($data, $params) {
	global $language;

	extract ($params);
	if (!isset($lang) || $lang == $language)
		return $data;
	else
		return "";
}

?>