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
		static $datepicker_options_common;

		if (! $datepicker_options_common) {
			$first = $prefs['calendar_firstDayofWeek'] == 'user'? tra('First day of week: Sunday (its ID is 0) - translators you need to localize this string!'): $prefs['calendar_firstDayofWeek'];
			if (!is_numeric($first) || !in_array($first, array(0,1,2,3,4,5,6))) {
				$first = 0;
			}
			
			$datepicker_options_common .= ', firstDay: '.$first;
			$datepicker_options_common .= ", closeText: '".smarty_function_jscalendar_tra('Done')."'";
			$datepicker_options_common .= ", prevText: '".smarty_function_jscalendar_tra('Prev')."'";
			$datepicker_options_common .= ", nextText: '".smarty_function_jscalendar_tra('Next')."'";
			$datepicker_options_common .= ", currentText: '".smarty_function_jscalendar_tra('Today')."'";
			$datepicker_options_common .= ", weekHeader: '".smarty_function_jscalendar_tra('Wk')."'";
			$datepicker_options_common .= ", dayNames: ['".smarty_function_jscalendar_tra('Sunday')."','".smarty_function_jscalendar_tra('Monday')."','".smarty_function_jscalendar_tra('Tuesday')."','".smarty_function_jscalendar_tra('Wednesday')."','".smarty_function_jscalendar_tra('Thursday')."','".smarty_function_jscalendar_tra('Friday')."','".smarty_function_jscalendar_tra('Saturday')."']";
			$datepicker_options_common .= ", dayNamesMin: ['".smarty_function_jscalendar_tra('Su')."','".smarty_function_jscalendar_tra('Mo')."','".smarty_function_jscalendar_tra('Tu')."','".smarty_function_jscalendar_tra('We')."','".smarty_function_jscalendar_tra('Th')."','".smarty_function_jscalendar_tra('Fr')."','".smarty_function_jscalendar_tra('Sa')."']";
			$datepicker_options_common .= ", dayNamesShort: ['".smarty_function_jscalendar_tra('Sun')."','".smarty_function_jscalendar_tra('Mon')."','".smarty_function_jscalendar_tra('Tue')."','".smarty_function_jscalendar_tra('Wed')."','".smarty_function_jscalendar_tra('Thu')."','".smarty_function_jscalendar_tra('Fri')."','".smarty_function_jscalendar_tra('Sat')."']";
			$datepicker_options_common .= ", monthNames: ['".smarty_function_jscalendar_tra('January')."','".smarty_function_jscalendar_tra('February')."','".smarty_function_jscalendar_tra('March')."','".smarty_function_jscalendar_tra('April')."','".smarty_function_jscalendar_tra('May')."','".smarty_function_jscalendar_tra('June')."','".smarty_function_jscalendar_tra('July')."','".smarty_function_jscalendar_tra('August')."','".smarty_function_jscalendar_tra('September')."','".smarty_function_jscalendar_tra('October')."','".smarty_function_jscalendar_tra('November')."','".smarty_function_jscalendar_tra('December')."']";
			$datepicker_options_common .= ", monthNamesShort: ['".smarty_function_jscalendar_tra('Jan')."','".smarty_function_jscalendar_tra('Feb')."','".smarty_function_jscalendar_tra('Mar')."','".smarty_function_jscalendar_tra('Apr')."','".smarty_function_jscalendar_tra('May')."','".smarty_function_jscalendar_tra('Jun')."','".smarty_function_jscalendar_tra('Jul')."','".smarty_function_jscalendar_tra('Aug')."','".smarty_function_jscalendar_tra('Sep')."','".smarty_function_jscalendar_tra('Oct')."','".smarty_function_jscalendar_tra('Nov')."','".smarty_function_jscalendar_tra('Dec')."']";
			$datepicker_options_common .= '}';
		}

		$datepicker_options .= $datepicker_options_common;

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
function smarty_function_jscalendar_tra($str) {
	return str_replace("'", "\\'", tra($str));
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
