<?php
// Initialization

require_once('tiki-setup.php');


// Display the template
$smarty->assign('msg',$_REQUEST["error"]);
$smarty->display('error.tpl');
?>