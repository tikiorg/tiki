<?php
/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_lang.php,v 1.4 2004-04-30 14:20:09 sylvieg Exp $
 * Displays the content data only if the language matched the current language or the language is the URI
 * {LANG(lang=fr)}Bon appétit{LANG}
 * 
 */
function wikiplugin_lang_help() {
	return tra("Displays the text only if the language matchs").":<br />~np~{LANG(lang=>)}".tra("text")."{LANG}~/np~";
}

function wikiplugin_lang($data, $params) {
	global $language;

	extract ($params);
	if (isset($_REQUEST['lang']))
		return ($lang == $_REQUEST['lang'])? $data: "";
	else
		if (!isset($lang) || $lang == $language)
			return $data;
		else
			return "";
}

?>