<?php
include_once('lib/dcs/dcslib.php');
function smarty_function_rcontent($params, &$smarty)
{
    global $tikilib;
    extract($params);
    // Param = zone

    if (empty($id)) {
        $smarty->trigger_error("assign: missing 'zone' parameter");
        return;
    }
    $data = $dcslib->get_random_content($id);
    print($data);
}

/* vim: set expandtab: */

?>
