<?php
/* $Header: /cvsroot/tikiwiki/tiki/lib/tikiticketlib.php,v 1.3 2004-01-03 00:28:56 mose Exp $

Tikiwiki CSRF protection.
also called : anti-banana-skin (oops)

Install:
- copy tikiticketlib.php in lib/tikiticketlib.php (or anywhere to your taste)
- add at the very top of setup.php, under session_start();
    include "lib/tikiticketlib.php"; 
  or anywhere your taste previous stated something else


= CLOSING

  ask_ticket('something');

- for marking where begins an edit or admin area 
  that requires protection.

- on most administrative pages that ticket request 
  should occur AFTER all active tests for modification

- some pages include the edit and display part in same 
  place. In such case use the ask_ticket at the end of 
  conditionnal block that determines we are in edit mode.


= OPENING

  check_ticket('something');

- for testing if the right ticket have been generated

- that call should occur just after the test of $_REQUEST
  variables to see if something is due to be modified.

- if the check fails, it sends a mail to apache admin with 
  faulty link and referer so the problem can be tracked.

= EXAMPLE

# file tiki-admin_cookies.php
<?php
require_once ('tiki-setup.php');
include_once ('lib/taglines/taglinelib.php');

if ($tiki_p_edit_cookies != 'y') {
  $smarty->assign('msg', tra("You dont have permission to use this feature"));
  $smarty->display("error.tpl");
  die;
}

# insert protection here, with arbitrary string "admin_coojie"
ask_ticket('admin_cookie');

# ... snip ...

if (isset($_REQUEST["remove"])) {
	# verify the protection before action
	check_ticket('admin_cookie'); //   <--------------- protected
	$taglinelib->remove_cookie($_REQUEST["remove"]);
}

if (isset($_REQUEST["removeall"])) {
	# verify the protection before action
	check_ticket('admin_cookie'); //   <--------------- protected
  $taglinelib->remove_all_cookies();
}

if (isset($_REQUEST["upload"])) {
	# verify the protection before action
	check_ticket('admin_cookie'); //   <--------------- protected
  if (isset($_FILES['userfile1']) && 
	  is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
    $fp = fopen($_FILES['userfile1']['tmp_name'], "r");
# ... snip ...

if (isset($_REQUEST["save"])) {
	# verify the protection before action
	check_ticket('admin_cookie'); //   <--------------- protected
  $taglinelib->replace_cookie($_REQUEST["cookieId"], $_REQUEST["cookie"]);
# ... snip ...

$smarty->assign('mid', 'tiki-admin_cookies.tpl');
$smarty->display("tiki.tpl");
?>

please ask admins@tikiwiki.org if you are lost with a complicated case.

*/
function ask_ticket($area) {
	$_SESSION['antisurf'] =  $area; 
}
function check_ticket($area) { 
	if ($_SESSION['antisurf'] != $area) { 
		/* that part is optionnal, it sends a mail of alert
		$body = "\nCSRF: ";
		if (isset($_SERVER["SCRIPT_URI"]) and $_SERVER["SCRIPT_URI"]) {
			$body.= $_SERVER["SCRIPT_URI"];
		} else {
			$body.= $_SERVER["HTTP_HOST"];
		}
		if (isset($_SERVER["QUERY_STRING"]) and $_SERVER["QUERY_STRING"]) {
			$body.= "?".$_SERVER["QUERY_STRING"];
		}
		$body.= "\nfrom: ".$_SERVER["HTTP_REFERER"]."\n";
		@mail($_SERVER['SERVER_ADMIN'],"[CSRF] alert",$body);
		*/
		global $smarty;
		$_SESSION['antisurf'] =  $area; 
		$smarty->assign('post',$_POST);
		$smarty->assign('query',$_SERVER["QUERY_STRING"]);
		$smarty->assign('self',$_SERVER["PHP_SELF"]);
		$smarty->assign('msg',tra("Sea Surfing (CSRF) detected. Operation blocked."));
		$smarty->display('error_ticket.tpl');
		die;
	}
}
?>
