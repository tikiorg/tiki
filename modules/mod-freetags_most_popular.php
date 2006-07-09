<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once("lib/freetag/freetaglib.php");
//global $freetaglib; This one helped for tiki-index.php, but not for e.g. tiki-admin_system.php 
//instantiating the object(like on the next line) helped. I'm unsure about this one - please check it!
//Initial problem was, "Call to a member-function on a non-object"
//Probably related to evaluating modules first - dunno.
global $dbTiki;
$freetaglib = new FreetagLib($dbTiki);

$most_popular_tags = $freetaglib->get_most_popular_tags('', 0, $module_rows);

$smarty->assign('most_popular_tags', $most_popular_tags);

?>
