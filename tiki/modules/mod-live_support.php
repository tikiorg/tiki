<?php

include_once ('lib/live_support/lslib.php');

$smarty->assign('modsupport', $lslib->operators_online());

?>