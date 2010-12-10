<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
/*
 * Displays the content data only if the language matched the current language or the language is the URI
 * {LANG(lang=fr)}Bon appétit{LANG}
 * tra("Displays the text only if the requested language matches (lang) or not (notlang) the + separated list of lang codes")
 */
function wikiplugin_lang_help() {
	return tra("Displays the text only if the language matchs")." lang/notlang:<br />~np~{LANG([not]lang=>code[+code+...])}".tra("text")."{LANG}~/np~";
}

function wikiplugin_lang_info() {
	return array(
		'name' => tra('Language'),
		'documentation' => tra('PluginLang'),
		'description' => tra('Displays the text only if the language matches'),
		'prefs' => array( 'feature_multilingual', 'wikiplugin_lang' ),
		'body' => tra('text'),
		'params' => array(
			'lang' => array(
				'required' => false,
				'name' => tra('Language'),
				'description' => tra('List of languages for which the block is displayed. Languages use the two letter language codes (ex: en, fr, es, ...). Multiple languages can be specified by separating codes by + signs.'),
				'default' => '',
			),
			'notlang' => array(
				'required' => false,
				'name' => tra('Not Language'),
				'description' => tra('List of languages for which the block is not displayed. Languages use the two letter language codes (ex: en, fr, es, ...). Multiple languages can be specified by separating codes by + signs.'),
				'default' => '',
			),
		),
	);
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
