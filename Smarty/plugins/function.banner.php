<?php

function smarty_function_banner($params, &$smarty)
{
    global $tikilib;
    global $dbTiki;
    include_once('lib/banners/bannerlib.php');
    global $bannerlib;
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
