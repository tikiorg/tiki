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
    global $bannerlib;include_once('lib/banners/bannerlib.php');

    extract($params);
    // Param = zone

    if (empty($zone)) {
        $smarty->trigger_error("assign: missing 'zone' parameter");
        return;
    }
	if (empty($target)) {
		$banner = $bannerlib->select_banner($zone);
	} else {
		$banner = $bannerlib->select_banner($zone, $target);
	}
    print($banner);
}



?>
