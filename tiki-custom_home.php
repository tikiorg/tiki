<?php
// Initialization
require_once('tiki-setup.php');


/*
hfd
require_once "lib/NNTP.php";
$nntp = new Net_NNTP;
$ret = $nntp->connect("news.php.net");
$groups = $nntp->getGroups();
//print_r($groups);
$z = $nntp->selectGroup('php.announce');
print_r($z);
$h = $nntp->splitHeaders(1);
print_r($h);
$b = $nntp->getBody(1);
print_r($b);
*/


if($feature_custom_home != 'y') {
    $smarty->assign('msg',tra("This feature has been disabled"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}


// Display the template
$smarty->assign('mid','tiki-custom_home.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>