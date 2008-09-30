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

function wikiplugin_back($data, $params) {
    global $tikilib;
    
    // Remove first <ENTER> if exists...
    // if (substr($data, 0, 2) == "\r\n") $data = substr($data, 2);
    
    extract ($params,EXTR_SKIP);
    
    $begin = "<script type=\"text/javascript\">document.write('<a href=\"javascript:history.go(-1)\">";
    
    $content = "Back";
    
    $end = "</a>');</script>";
    
    return $begin . $content  . $end;
}
?>
