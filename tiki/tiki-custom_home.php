<?php
// Initialization
require_once('tiki-setup.php');

/*
require_once "lib/NNTP.php";
$nntp = new Net_NNTP;
$ret = $nntp->connect("news.php.net");
$headers = $nntp->getHeaders($msg_id);
if( PEAR::isError($headers)) {
 // handle error
} else {
 // success - print the header
 echo $headers;
}
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