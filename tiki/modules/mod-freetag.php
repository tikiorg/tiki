<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $page;

include_once ('lib/freetag/freetag.class.php');

$userid = $userlib->get_user_id($user);
$smarty->assign('userid',$userid);

$pageid = $tikilib->get_page_id_from_name($page);
$smarty->assign('page',$pageid);

$freetag = new freetag();
if (isset($_POST['tags']) && trim($_POST['tags']) != "") {
	$freetag->tag_object($userid, $_POST['page_id'], $_POST['tags']);
	}

?>
