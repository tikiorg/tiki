<?php
function smarty_function_content($params, &$smarty)
{
    global $tikilib;
    global $dbTiki;
    include_once('lib/dcs/dcslib.php');
    extract($params);
    // Param = zone

    if (empty($id)) {
        $smarty->trigger_error("assign: missing 'zone' parameter");
        return;
    }
    $data = $dcslib->get_actual_content($id);
    print($data);
}

/* vim: set expandtab: */

?>
