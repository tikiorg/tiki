<?php
/*
 * $Header$
 *
 * Tiki-Wiki BACK plugin.
 * 
 * Syntax:
 * 
 *  {BACK()}{BACK}
 * 
 */
function wikiplugin_back_help() {
    return tra("Insert back link on wiki page").":<br />~np~{BACK()/}~/np~";
}

function wikiplugin_back_info() {
    return array(
        'name' => tra('Back'),
		'documentation' => 'PluginBack',
        'description' => tra('Displays a link that allows to go back in the browser history'),
        'prefs' => array( 'wikiplugin_back' ),
        'params' => array(),
        );
}

function wikiplugin_back($data, $params) {
    global $tikilib;
    
    // Remove first <ENTER> if exists...
    // if (substr($data, 0, 2) == "\r\n") $data = substr($data, 2);
    
    extract ($params,EXTR_SKIP);
    
    $begin = "<a href=\"javascript:history.go(-1)\">";
            
    $content = tra('Back');

    $end = "</a>";
    
    return $begin . $content  . $end;
}
?>
