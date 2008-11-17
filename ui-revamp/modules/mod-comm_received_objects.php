<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$ranking = $tikilib->list_received_pages(0, -1, $sort_mode = 'pageName_asc', '');

$smarty->assign('modReceivedPages', $ranking["cant"]);

?>
