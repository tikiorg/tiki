<?php


function smarty_function_jspopup($params, &$smarty)
{
    extract($params);
    // Param = zone
    if(empty($href)) {
        $smarty->trigger_error("assign: missing href parameter");
        return;
    }
    print("href='#' onClick='javascript:window.open(\"$href\",\"\",\"menubar=no,scrollbars=yes,resizable=yes,height=600,width=400\");' ");
}

/* vim: set expandtab: */

?>
