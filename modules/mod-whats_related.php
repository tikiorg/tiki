<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $categlib; require_once ('lib/categories/categlib.php');

//test
$WhatsRelated=$categlib->get_link_related($_SERVER["REQUEST_URI"]);
$smarty->assign_by_ref('WhatsRelated', $WhatsRelated);


?>
