<?php


function smarty_function_helplink($params, &$smarty)
{
    extract($params);
    // Param = zone
    if(empty($page)) {
        $smarty->trigger_error("assign: missing page parameter");
        return;
    }
    print("<a title='help' href='#' onClick='javascript:window.open(\"tiki-index_p.php?page=$page\",\"\",\"menubar=no,scrollbars=yes,resizable=yes,height=600,width=500\");'><img border='0' src='lib/Galaxia/img/icons/question.gif' alt='help' /></a>");
}

/* vim: set expandtab: */

?>
