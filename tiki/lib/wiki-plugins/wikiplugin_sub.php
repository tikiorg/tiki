<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_sub.php,v 1.3 2004-09-19 19:37:09 mose Exp $

// Wiki plugin to output <sub>...</sub>
// - rlpowell

function wikiplugin_sub_help() {
        return tra("Displays text in subscript.").":<br :>~np~{SUB()}text{SUB}~/np~";
}

function wikiplugin_sub($data, $params)
{
        global $tikilib;

        extract ($params);
	return "<sub>$data</sub>";
}

?>
