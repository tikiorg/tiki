<?php
/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_lang.php,v 1.7 2005-10-16 14:35:10 mose Exp $
 * Displays the content data only if the language matched the current language or the language is the URI
 * {LANG(lang=fr)}Bon appétit{LANG}
 * tra("Displays the text only if the requested language matches (lang) or not (notlang) the + separated list of lang codes")
 */
function wikiplugin_lang_help() {
	return tra("Displays the text only if the language matchs")." lang/notlang:<br />~np~{LANG([not]lang=>code[+code+...])}".tra("text")."{LANG}~/np~";
}

function wikiplugin_lang($data, $params) {
	global $language;
	$reqlang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : $language;
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
