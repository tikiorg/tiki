<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_banner($params, &$smarty)
{
    global $tikilib;
    global $dbTiki;
    include_once('lib/banners/bannerlib.php');
    if(!isset($bannerlib)) {
      $bannerlib = new BannerLib($dbTiki);
    }

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
