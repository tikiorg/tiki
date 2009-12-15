<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_helplink($params, &$smarty)
{
    extract($params);
    // Param = zone
    if(empty($page)) {
        $smarty->trigger_error("assign: missing page parameter");
        return;
    }
    print("<a title='help' href='#' onclick='javascript:window.open(\"tiki-index_p.php?page=$page\",\"\",\"menubar=no,scrollbars=yes,resizable=yes,height=600,width=500\");'><img border='0' src='pics/icons/help.png' alt='".tra("help","",true)."' width='16' height='16' /></a>");
}



?>
