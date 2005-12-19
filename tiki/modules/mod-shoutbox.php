<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once ('lib/tikilib.php'); # httpScheme()

global $shoutboxlib;include_once ('lib/shoutbox/shoutboxlib.php');
global $feature_shoutbox, $tiki_p_view_shoutbox, $tiki_p_admin_shoutbox, $tiki_p_post_shoutbox, $feature_ticketlib2;

if ($feature_shoutbox == 'y' && $tiki_p_view_shoutbox == 'y') {
	$setup_parsed_uri = parse_url($_SERVER["REQUEST_URI"]);

	if (isset($setup_parsed_uri["query"])) {
		parse_str($setup_parsed_uri["query"], $sht_query);
	} else {
		$sht_query = array();
	}

	$shout_father = $setup_parsed_uri["path"];

	if (isset($sht_query) && count($sht_query) > 0) {
		$sht = array();
		foreach ($sht_query as $sht_name => $sht_val) {
			$sht[] = $sht_name . '=' . $sht_val;
		}
		$shout_father.= "?".implode("&amp;",$sht)."&amp;";
	} else {
		$shout_father.= "?";
	}

	global $smarty;
	$smarty->assign('shout_ownurl', $shout_father);

	if ($tiki_p_admin_shoutbox == 'y') {
		if (isset($_REQUEST["shout_remove"])) {
			if ($feature_ticketlib2 =='y') {
				$area = 'delshoutboxentry';
				if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
					key_check($area);
					$shoutboxlib->remove_shoutbox($_REQUEST["shout_remove"]);
				} else {
					key_get($area);
				}
			} else {
				$shoutboxlib->remove_shoutbox($_REQUEST["shout_remove"]);
			}
		}
	}

	if ($tiki_p_post_shoutbox == 'y') {
		if (isset($_REQUEST["shout_send"])) {
			$shoutboxlib->replace_shoutbox(0, $user, $_REQUEST["shout_msg"]);
		}
	}

	$shout_msgs = $shoutboxlib->list_shoutbox(0, $module_rows, 'timestamp_desc', '');
	$smarty->assign('shout_msgs', $shout_msgs["data"]);

	// Subst module parameter 'tooltip'
	$smarty->assign('tooltip', isset($module_params["tooltip"]) ? $module_params["tooltip"] : 0);
}

?>
