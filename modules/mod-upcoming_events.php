<?php

require_once ('lib/tikilib.php'); # httpScheme()

include_once ('lib/calendar/calendarlib.php');

if ($feature_calendar == 'y' && $tiki_p_view_calendar == 'y') {
	$setup_parsed_uri = parse_url($_SERVER["REQUEST_URI"]);

	if (isset($setup_parsed_uri["query"])) {
		parse_str($setup_parsed_uri["query"], $calitemId);
	} else {
		$calitemId = array();
	}

	// I don't think httpPrefix is needed here (Luis)
	$event_father =/*httpPrefix().*/ $setup_parsed_uri["path"];

	if (isset($calitemId) && count($calitemId) > 0) {
		$sht_first = 1;

		foreach ($calitemId as $sht_name => $sht_val) {
			if ($sht_first) {
				$sht_first = false;

				$event_father .= '?' . $sht_name . '=' . $sht_val;
			} else {
				$event_father .= '&amp;' . $sht_name . '=' . $sht_val;
			}
		}

		$event_father .= '&amp;';
	} else {
		$event_father .= '?';
	}

	global $smarty;
	$smarty->assign('event_ownurl', $event_father);

	if ($tiki_p_admin_calendar == 'y') {
		if (isset($_REQUEST["event_remove"])) {
			$calendarlib->remove_calendar($_REQUEST["event_remove"]);
		}
	}

	if ($tiki_p_post_calendar == 'y') {
		if (isset($_REQUEST["event_send"])) {
			$calendarlib->replace_calendar(0, $user, $_REQUEST["event_msg"]);
		}
	}

	$event_msgs = $calendarlib->list_calendar(0, $module_rows, 'timestamp_desc', '');
	$smarty->assign('event_msgs', $event_msgs["data"]);

	// Subst module parameter 'tooltip'
	$smarty->assign('tooltip', isset($module_params["tooltip"]) ? $module_params["tooltip"] : 0);
}

?>

