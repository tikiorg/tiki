<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Made by Yoni with Toggg great help
// Parameters to set for the module
// client=pub-xxxxxxxxxxxxxxxx as it appears on the Google code
// display= global banner format as it appear in the Google code, ex: display=468*60_as
// color_border=
// color_bg=
// color_link=
// color_url=
// color_text=
// colors as you set it for you own purpose ex: color_border=edeed5
// If you don't set them the colors will be Google defaults
// Usage exemple :
// {MODULE(module=>adsense,client=pub-xxxxxxxxxxxxxxxx,display=468*60_as,color_border=edeed5,color_bg=edeed5,color_link=0000CC,color_url=008000,color_text=000000)}{MODULE}

$smarty->assign('display', isset($module_params["display"]) ? $module_params["display"] : '');
$smarty->assign('client', isset($module_params["client"]) ? $module_params["client"] : '');
$smarty->assign('ad_channel', isset($module_params["ad_channel"]) ? $module_params["ad_channel"] : '');
$smarty->assign('color_border', isset($module_params["color_border"]) ? $module_params["color_border"] : '');
$smarty->assign('color_bg', isset($module_params["color_bg"]) ? $module_params["color_bg"] : '');
$smarty->assign('color_link', isset($module_params["color_link"]) ? $module_params["color_link"] : '');
$smarty->assign('color_url', isset($module_params["color_url"]) ? $module_params["color_url"] : '');
$smarty->assign('color_text', isset($module_params["color_text"]) ? $module_params["color_text"] : '');
?>