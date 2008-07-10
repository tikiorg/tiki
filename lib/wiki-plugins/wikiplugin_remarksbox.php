<?php

/* Displays a remarks box
 * Use:
 * {REMARKSBOX()}Some remarks, will be wiki parsed according to prefs{REMARKSBOX}
 *  (type=>tip|comment|note|warning)	Type (default=tip)
 *  (title=>title text)  				Title text
 *  (highlight=>n|y)  					Add highlight class (default=n)
 *  (icon=>icon_id)  					Optional icon (override defaults, use 'none' for no icon)
 * Examples:
 * 
	{REMARKSBOX(title=>Comment,type=>comment)}What's the difference between a comment and a note?{REMARKSBOX}
	{REMARKSBOX(title=>Tip,highlight=y)}Never run for a bus. There'll be another one along soon.{REMARKSBOX}
	{REMARKSBOX(title=>Tip!,highlight=y,icon=>world)}This one is highlighted for the world!{REMARKSBOX}
	{REMARKSBOX(title=>Note,type=>note)}This here is a note{REMARKSBOX}
	{REMARKSBOX(title=>Bicuits!,type=>warning)}Pay attention to this! __Ok!?__{REMARKSBOX}
 */

function wikiplugin_remarksbox_help() {
	return tra('Displays a comment, tip, note or warning box').
		':<br />~np~{REMARKSBOX(type=>tip|comment|note|warning,title=>title text,highlight=n|y,icon=optional icon_id or none )}'.
		tra('remarks text').'{REMARKSBOX}~/np~';
}

function wikiplugin_remarksbox($data, $params) {
	global $smarty;
	require_once('lib/smarty_tiki/block.remarksbox.php');
	
	// there probably is a better way @todo this
	// but for now i'm escaping the html in ~np~s as the parser is adding odd <p> tags
	$ret = '~np~'.smarty_block_remarksbox($params, '~/np~'.tra($data).'~np~', &$smarty).'~/np~';
	return $ret;
}

?>
