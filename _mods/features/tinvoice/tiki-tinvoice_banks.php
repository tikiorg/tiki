<?php
// Initialization
$section = 'tinvoice';
require_once ('tiki-setup.php');
require_once ('lib/tinvoice/tinvoicelib.php');




// Display the template
$smarty->assign('mid', 'tiki-tinvoice_banks.tpl');
$smarty->display("tiki.tpl");

?>
