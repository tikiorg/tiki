<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $sections,$section;
global $userlib, $smarty;

$userid = $userlib->get_user_id($user);
$smarty->assign('userid',$userid);

$tagname = "";
$tagid = 0;
if (isset($sections[$section])) {
	$par = $sections[$section];
	if (isset($par['itemkey']) && !empty($_REQUEST["{$par['itemkey']}"])) {
		$tagid = $_REQUEST["{$par['itemkey']}"];
		$taglabel = "$section ".$_REQUEST[$par['key']];
	} elseif (isset($par['key']) && !empty($_REQUEST["{$par['key']}"])) {
		$tagid = $_REQUEST["{$par['key']}"];
		$taglabel = $section;
	}
}
$smarty->assign('tagid',$tagid);
$smarty->assign('tagname',$tagname);
 

