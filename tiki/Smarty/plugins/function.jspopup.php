<?php


function smarty_function_jspopup($params, &$smarty)
{
    extract($params);
    // Param = zone
    if(empty($href)) {
        $smarty->trigger_error("assign: missing href parameter");
        return;
    }
    if(!isset($scrollbars)) $scrollbars='yes';
    if(!isset($scrollbars)) $menubar='no';
    if(!isset($resizable))  $resizable='yes';
    if(!isset($height)) $height='600';
    if(!isset($width)) $width='400';
    print("href='#' onClick='javascript:window.open(\"$href\",\"\",\"menubar=$menubar,scrollbars=$scrollbars,resizable=$resizable,height=$height,width=$width\");' ");
}

/* vim: set expandtab: */

?>
