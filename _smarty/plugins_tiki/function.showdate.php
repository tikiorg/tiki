<?php
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
