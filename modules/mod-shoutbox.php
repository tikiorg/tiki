<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

require_once ('lib/tikilib.php'); # httpScheme()

include_once ('lib/shoutbox/shoutboxlib.php');

if ($feature_shoutbox == 'y' && $tiki_p_view_shoutbox == 'y') {
	$setup_parsed_uri = parse_url($_SERVER["REQUEST_URI"]);

	if (isset($setup_parsed_uri["query"])) {
		parse_str($setup_parsed_uri["query"], $sht_query);
	} else {
		$sht_query = array();
	}

	// I don't think httpPrefix is needed here (Luis)
	$shout_father =/*httpPrefix().*/ $setup_parsed_uri["path"];

	if (isset($sht_query) && count($sht_query) > 0) {
		$sht_first = 1;

		foreach ($sht_query as $sht_name => $sht_val) {
			if ($sht_first) {
				$sht_first = false;

				$shout_father .= '?' . $sht_name . '=' . $sht_val;
			} else {
				$shout_father .= '&amp;' . $sht_name . '=' . $sht_val;
			}
		}

		$shout_father .= '&amp;';
	} else {
		$shout_father .= '?';
	}

	global $smarty;
	$smarty->assign('shout_ownurl', $shout_father);

	if ($tiki_p_admin_shoutbox == 'y') {
		if (isset($_REQUEST["shout_remove"])) {
			$shoutboxlib->remove_shoutbox($_REQUEST["shout_remove"]);
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
