<?php
function smarty_function_rcontent($params, &$smarty)
{
    global $tikilib;
    extract($params);
    // Param = zone

    if (empty($id)) {
        $smarty->trigger_error("assign: missing 'zone' parameter");
        return;
    }
    $data = $tikilib->get_random_content($id);
    print($data);
}

/* vim: set expandtab: */

?>
