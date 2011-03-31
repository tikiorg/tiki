<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_jscalendar($params, &$smarty) {
	global $headerlib, $prefs, $tikilib;
	
	if ($prefs['feature_jquery_ui'] === 'y' && (!isset($params['showtime']) || $params['showtime'] === 'n')) {	// override jscalendar with jQuery UI datepicker
		static $uiCalendarInstance = 0;
		$uiCalendarInstance++;
		
		if (!isset($params['id'])) {
			$params['id'] = 'uiCal_' . $uiCalendarInstance;
		}
		$id = '';
		$selector = "#$id";
		if (isset($params['fieldname'])) {
			$name = ' name="' . $params['fieldname'] . '"';
		} else {
			$name = '';
		}
		if (empty($params['date'])) {
			$params['date'] = $tikilib->now;
		}
		$datepicker_options = '{ altField: "#' . $params['id'] . '"';
		if (!empty($params['goto'])) {
			$datepicker_options .= ', onSelect: function(dateText, inst) { window.location="'.$params['goto'].'".replace("%s",$("#'.$params['id'].'").val()/1000); }';
		}
		$first = $prefs['calendar_firstDayofWeek'] == 'user'? tra('First day of week: Sunday (its ID is 0) - translators you need to localize this string!'): $prefs['calendar_firstDayofWeek'];
		if (!is_numeric($first) || !in_array($first, array(0,1,2,3,4,5,6))) {
			$first = 0;
		}
		$datepicker_options .= ', firstDay: '.$first;
		$datepicker_options .= ", dayNames: ['".tra('Sunday')."','".tra('Monday')."','".tra('Tuesday')."','".tra('Wednesday')."','".tra('Thursday')."','".tra('Friday')."','".tra('Saturday')."']";
		$datepicker_options .= ", dayNamesMin: ['".substr(tra('Sunday'),0,2)."','".substr(tra('Monday'),0,2)."','".substr(tra('Tuesday'),0,2)."','".substr(tra('Wednesday'),0,2)."','".substr(tra('Thursday'),0,2)."','".substr(tra('Friday'),0,2)."','".substr(tra('Saturday'),0,2)."']";
		$datepicker_options .= ", monthNames: ['".tra('January')."','".tra('February')."','".tra('March')."','".tra('April')."','".tra('May')."','".tra('June')."','".tra('July')."','".tra('August')."','".tra('September')."','".tra('October')."','".tra('November')."','".tra('December')."']";
		$datepicker_options .= '}';
		$html = '<input type="hidden" id="' . $params['id'] . '"' . $name  . ' value="'.$params['date'].'" />';
		$html .= '<input type="text" id="' . $params['id'] . '_dptxt" value="" />';	// text version of datepicker date
		// TODO use a parsed version of $prefs['short_date_format']
		// Note: JS timestamp is in milliseconds - php is seconds
		$headerlib->add_jq_onready('$("#'.$params['id'].'_dptxt").val($.datepicker.formatDate( "yy-mm-dd", new Date('.$params['date'].'* 1000))).tiki("datepicker", "jscalendar", '.$datepicker_options.');');
		return $html;
		
	} else {
		echo smarty_function_jscalendar_body($params, $smarty);
	}
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
	$js = '';
	if ($goto) {
		$js .= "function goto_url() { window.location='".sprintf($goto,"'+document.getElementById('id_$id').value+'")."'; }\n";
	}
	$js .= "Calendar.setup( {\n";
	$js .= "date : \"$formatted_date\",\n";
	if ($fieldname) {
		$js .= "inputField : \"id_$id\",\n";
	}
	$js .= "ifFormat : \"$ifFormat\",\n";
	$js .= "displayArea : \"disp_$id\",\n";
	$js .= "daFormat : \"$format\",\n";
	// $back.= "singleClick : true,\n";
	if ($showtime) {
		$js .= "showsTime : true,\n";
	}
	if ($goto) {
		$js .= "onUpdate : goto_url,\n";
	}
	$js .= "align : \"$align\"\n";
	$js .= "} );\n";
	$headerlib->add_jq_onready($js);

	return $back;

}
