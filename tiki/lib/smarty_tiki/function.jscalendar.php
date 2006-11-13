<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_jscalendar($params, &$smarty) {
	global $headerlib,$firstDayOfWeek;

	$headerlib->add_cssfile('lib/jscalendar/calendar-system.css');
	$headerlib->add_jsfile('lib/jscalendar/calendar.js');
	global $language;
	if (is_file('lib/jscalendar/calendar-'.$language.'.js')) {
		$headerlib->add_jsfile('lib/jscalendar/calendar-'.$language.'.js');
	} else {
		$headerlib->add_jsfile('lib/jscalendar/calendar-en.js');
	}
	$headerlib->add_jsfile('lib/jscalendar/calendar-setup.js');


	if (isset($params['format'])) {
		$format = preg_replace('/"/','\"',$params['format']);
	} else {
		$format = "%A %e %B %Y, %H:%M";
	}

	if (isset($params['date'])) {
		$date = preg_replace('/[^0-9]/','',$params['date']);
	} else {
		$date = date('U');
	}
	$formatted_date = strftime($format,(int)$date);
	
	if (isset($params['id'])) {
		$id =  preg_replace('/"/','\"',$params['id']);
	} else {
		$id = $_GLOBALS['now'];
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
	if (isset($params['showtime']) and $params['showtime'] == 'y') {
		$showtime = true;
	} else {
		$showtime = false;
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
	$back.= "  date : \"$formatted_date\",\n";
	if ($fieldname) {
		$back.= "  inputField : \"id_$id\",\n";
	}
	$back.= "  ifFormat : \"%s\",\n";
	$back.= "  displayArea : \"disp_$id\",\n";
	$back.= "  daFormat : \"$format\",\n";
	$back.= "  singeClick : true,\n";
	//$back.= "  firstDay : $firstDayOfWeek,\n";
	if ($showtime) {
		$back.= "  showtime : true,\n";
	}
	if ($goto) {
		$back.= "  onUpdate : goto_url,\n";
	}
	$back.= "  align : \"$align\"\n";
	
	$back.= "} );\n";
	$back.= "</script>\n";

	echo $back;

}

?>
