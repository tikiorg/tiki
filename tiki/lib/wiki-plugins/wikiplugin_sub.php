<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_sub.php,v 1.2 2004-09-08 19:52:38 mose Exp $

// Wiki plugin to output <sub>...</sub>
// - rlpowell

function wikiplugin_sub_help() {
        return tra("Displays stuff in subscript.").":<br :>~np~{SUB(stuff)/}~/np~";
}

function wikiplugin_sub($data, $params)
{
	return "<sub>$params</sub>";
}

?>
