<?php
// Initialization
require_once('tiki-setup.php');


// Display the template
$smarty->assign('mid','tiki-live_support_admin.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>