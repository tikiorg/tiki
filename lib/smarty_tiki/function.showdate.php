<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_showdate($params, &$smarty)
{
    
    extract($params);
    // Param = zone

    if (empty($mode)) {
        $smarty->trigger_error("assign: missing 'mode' parameter");
        return;
    }
    print(date($mode));
}

/* vim: set expandtab: */

?>
