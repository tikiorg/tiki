<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_sup.php,v 1.1.4.2 2008-01-30 17:45:48 marclaporte Exp $

// Wiki plugin to output superscript <sup>...</sup>
// based on sub plugin

function wikiplugin_super_help() {
        return tra("Displays text in superscript.").":<br />~np~{SUP()}text{SUP}~/np~";
}

function wikiplugin_sup($data, $params)
{
        global $tikilib;

        extract ($params,EXTR_SKIP);
	return "<sup>$data</sup>";
}

?>
