<?php
function smarty_function_ed($params, &$smarty)
{
    global $tikilib;
    extract($params);
    // Param = zone

    if (empty($id)) {
        $smarty->trigger_error("ed: missing 'id' parameter");
        return;
    }
    
    print($banner);
}

/* vim: set expandtab: */

?>
