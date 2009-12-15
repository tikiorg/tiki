<?php
chdir('../../../..');

require_once('tiki-setup.php');
$smarty->template_dir = dirname(__FILE__).'/templates/';

$smarty->display('fck_tikiimage.tpl');
?>
