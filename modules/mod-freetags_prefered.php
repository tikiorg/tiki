<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if ($user) {
		global $freetag;
		global $dbTiki;
    if (!isset($freetag) or !is_object($freetag)) require_once("lib/freetag/freetaglib.php");
    $most_popular_tags = $freetaglib->get_most_popular_tags($user, 0, $module_rows);
    $smarty->assign('most_popular_tags', $most_popular_tags);
}

?>
