<?php
include_once('lib/banners/bannerlib.php');

function smarty_function_banner($params, &$smarty)
{
    global $tikilib;
    extract($params);
    // Param = zone

    if (empty($zone)) {
        $smarty->trigger_error("assign: missing 'zone' parameter");
        return;
    }
    $banner = $bannerlib->select_banner($zone);
    print($banner);
}

/* vim: set expandtab: */

?>
