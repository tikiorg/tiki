<?php
/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_lang.php,v 1.8 2007-10-12 07:55:48 nyloth Exp $
 * Displays the content data only if the language matched the current language or the language is the URI
 * {LANG(lang=fr)}Bon appétit{LANG}
 * tra("Displays the text only if the requested language matches (lang) or not (notlang) the + separated list of lang codes")
 */
function wikiplugin_lang_help() {
	return tra("Displays the text only if the language matchs")." lang/notlang:<br />~np~{LANG([not]lang=>code[+code+...])}".tra("text")."{LANG}~/np~";
}

function wikiplugin_lang($data, $params) {
	global $prefs;

	$reqlang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : $prefs['language'];
	extract ($params,EXTR_SKIP);
	if (isset($lang)) {
		return in_array($reqlang, explode('+', $lang)) ? $data : '';
	}
	if (isset($notlang)) {
		return in_array($reqlang, explode('+', $notlang)) ? '' : $data;
	}
	return $data;
}
?>
