<?php
function smarty_function_cookie($params, &$smarty)
{
    global $tikilib;
    extract($params);
    // Param = zone
   
    $data = $tikilib->pick_cookie();
    print($data);
}

/* vim: set expandtab: */

?>
