<?php


function smarty_function_helplink($params, &$smarty)
{
    extract($params);
    // Param = zone
    if(empty($page)) {
        $smarty->trigger_error("assign: missing page parameter");
        return;
    }
    print(" title='help' href='#' onClick='javascript:window.open(\"tiki-index_p.php?page=$page\",\"\",\"menubar=no,scrollbars=yes,resizable=yes,height=600,width=500\");' ");
}

/* vim: set expandtab: */

?>
