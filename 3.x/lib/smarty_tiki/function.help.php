<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_help($params, &$smarty)
{
    extract($params);
    // Param = zone
    if(empty($url) && empty($desc) && empty($crumb)) {
        $smarty->trigger_error("assign: missing parameter: help (url desc)|crumb");
        return;
    }
    print help_doclink($params);
}



?>
