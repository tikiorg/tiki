<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/directory/dirlib.php');

$tmp1 = isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : "";
$tmp2 = isset($_SERVER["PHP_SELF"]) ? $_SERVER["PHP_SELF"] : "";

// concat all, remove the // between server and path and then
// remove the name of the script itself:
$url = $tmp1.dirname($tmp2);

$info = Array();
$info["name"] = $tikilib->get_preference( "siteTitle", "" );
$info["description"] = '';
$info["url"] = $url;
$info["country"] = 'None';
$info["isValid"] = 'n';
$smarty->assign_by_ref('info',$info);

$countries=Array();
$h=opendir("img/flags");
while($file=readdir($h)) {
  if(is_file('img/flags/'.$file)) {
    $name=explode('.',$file);
    $countries[]=$name[0];
  }
}
closedir($h);
$smarty->assign_by_ref('countries',$countries);

// Display the template
$smarty->assign('mid','tiki-register_site.tpl');
$smarty->display("tiki.tpl");
?>
