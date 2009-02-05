<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_jscalendar($params, &$smarty) {
	echo smarty_function_jscalendar_body($params, $smarty);
}

function smarty_function_jscalendar_body($params, &$smarty) {
	global $headerlib, $tikilib, $prefs;

	$headerlib->add_cssfile('lib/jscalendar/calendar-system.css');
	$headerlib->add_cssfile('css/jscalendar.css');
	$headerlib->add_jsfile('lib/jscalendar/calendar.js');

	if (is_file('lib/jscalendar/lang/calendar-'.$prefs['language'].'-utf8.js')) {
		$headerlib->add_jsfile('lib/jscalendar/lang/calendar-'.$prefs['language'].'-utf8.js');
	} else {
		$headerlib->add_jsfile('lib/jscalendar/lang/calendar-en.js');
	}
	$headerlib->add_jsfile('lib/jscalendar/calendar-setup_stripped.js');

	if (isset($params['date'])) {
		$date = preg_replace('/[^0-9]/','',$params['date']);
	} else {
		$date = $tikilib->now;
	}

	if (isset($params['showtime']) and $params['showtime'] == 'y') {
		$showtime = true;
		if (isset($params['minutes_interval'])) {
			$minutes_interval = preg_replace('/[^0-9]/','',$params['minutes_interval']);
		} else {
			$minutes_interval = 5;
		}
		if ($minutes_interval > 1) {
			$sec = $minutes_interval*60;
			$date = (ceil($date/$sec))*$sec;
		}
	} else {
		$showtime = false;
	}

	if (isset($params['format'])) {
		$format = preg_replace('/"/','\"',$params['format']);
	} else {
		$format = tra($prefs['long_date_format']);
		if ($showtime) {
			$format.= ' '.tra($prefs['short_time_format']);
		}
	}

	$formatted_date = $tikilib->date_format($format,(int)$date);
	
	if (isset($params['id'])) {
		$id =  preg_replace('/"/','\"',$params['id']);
	} else {
		$id = $tikilib->now;
	}

	if (isset($params['ifFormat'])) {
		$ifFormat =  preg_replace('/"/','\"',$params['ifFormat']);
	} else {
	        $ifFormat = '%s';
	}

	if (isset($params['align'])) {
		$align = substr(preg_replace('/[^bBrRtTlLc]/','',$params['align']),0,2);
	} else {
		$align = "bR";
	}

	if (isset($params['fieldname'])) {
		$fieldname = preg_replace('/[^-_a-zA-Z0-9\[\]]/','',$params['fieldname']);
	} else {
		$fieldname = false;
	}
	if (isset($params['goto'])) {
		$goto = $params['goto'];
		if (!$fieldname) $fieldname = "gotoit";
	} else {
		$goto = false;
	}

	$back = '';
	if ($fieldname) {
		$back.= "<input type=\"hidden\" name=\"$fieldname\" value=\"$date\" id=\"id_$id\" />\n";
	}
	$back.= "<span title=\"".tra("Date Selector")."\" id=\"disp_$id\" class=\"daterow\">$formatted_date</span>\n";
	$back.= "<script type=\"text/javascript\">\n";
	if ($goto) {
		$back.= "function goto_url() { window.location='".sprintf($goto,"'+document.getElementById('id_$id').value+'")."'; }\n";
	}
	$back.= "Calendar.setup( {\n";
	$back.= "date : \"$formatted_date\",\n";
	if ($fieldname) {
		$back.= "inputField : \"id_$id\",\n";
	}
	$back.= "ifFormat : \"$ifFormat\",\n";
	$back.= "displayArea : \"disp_$id\",\n";
	$back.= "daFormat : \"$format\",\n";
	// $back.= "singleClick : true,\n";
	if ($showtime) {
		$back.= "showsTime : true,\n";
	}
	if ($goto) {
		$back.= "onUpdate : goto_url,\n";
	}
	$back.= "align : \"$align\"\n";
	$back.= "} );\n";
	$back.= "</script>\n";

	return $back;

}

?>
