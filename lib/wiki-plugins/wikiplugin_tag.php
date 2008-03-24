<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_tag.php,v 1.2 2005-12-12 15:18:52 mose Exp $

// Wiki plugin to output something like <a_tag style=a_style>...</a_tag>
//ex: {TAG(tag=STRIKE, style=color:#FF0000)}toto{TAG}
//	would produce <STRIKE style="color:#FF0000">toto</STRIKE>

function wikiplugin_tag_help() {
        return tra("Displays text between an html tag").":<br />~np~{TAG(tag=a_tag, style=a_style)}text{TAG}~/np~";
}

function wikiplugin_tag($data, $params) {
	extract ($params,EXTR_SKIP);
	if (!isset($tag))
		$tag = 'span';
	if (isset($style))
		$style = ' style="'.$style.'"';
	else
		$style = '';
	return "<$tag$style>$data</$tag>";
}

?>
