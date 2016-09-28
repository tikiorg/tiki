<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_function_jscalendar($params, $smarty)
{
	global $prefs;
	$headerlib = TikiLib::lib('header');
	$tikilib = TikiLib::lib('tiki');

	$uiCalendarInstance = uniqid();

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
	if (!isset($params['date'])) {
		// if date is provided empty then show a blank date (for filters)
		$params['date'] = $tikilib->now;
	}
	$datepicker_options = '{ altField: "#' . $params['id'] . '"';
	if (!empty($params['goto'])) {
		$datepicker_options .= ', onSelect: function(dateText, inst) { window.location="' .
														$params['goto'] . '".replace("%s",$("#' . $params['id'] . '").val()/1000); }';
	}
	static $datepicker_options_common;

	if (! $datepicker_options_common) {
		$calendarlib = TikiLib::lib('calendar');
		$first = $calendarlib->firstDayofWeek();

		if (!is_numeric($first) || !in_array($first, array(0, 1, 2, 3, 4, 5, 6))) {
			$first = 0;
		}

		$datepicker_options_common .= ', firstDay: '.$first;
		$datepicker_options_common .= ", closeText: '" . smarty_function_jscalendar_tra('Done') . "'";
		$datepicker_options_common .= ", prevText: '" . smarty_function_jscalendar_tra('Prev') . "'";
		$datepicker_options_common .= ", nextText: '" . smarty_function_jscalendar_tra('Next') . "'";
		$datepicker_options_common .= ", currentText: '" . smarty_function_jscalendar_tra('Today') . "'";
		$datepicker_options_common .= ", weekHeader: '" . smarty_function_jscalendar_tra('Wk') . "'";

		$datepicker_options_common .= ", dayNames: ['" . smarty_function_jscalendar_tra('Sunday') . "','" .
																										 smarty_function_jscalendar_tra('Monday') . "','" .
																										 smarty_function_jscalendar_tra('Tuesday') . "','" .
																										 smarty_function_jscalendar_tra('Wednesday') . "','" .
																										 smarty_function_jscalendar_tra('Thursday') . "','" .
																										 smarty_function_jscalendar_tra('Friday') . "','" .
																										 smarty_function_jscalendar_tra('Saturday') . "']";

		$datepicker_options_common .= ", dayNamesMin: ['" . smarty_function_jscalendar_tra('Su') . "','" .
																												smarty_function_jscalendar_tra('Mo') . "','" .
																												smarty_function_jscalendar_tra('Tu') . "','" .
																												smarty_function_jscalendar_tra('We') . "','" .
																												smarty_function_jscalendar_tra('Th') . "','" .
																												smarty_function_jscalendar_tra('Fr') . "','" .
																												smarty_function_jscalendar_tra('Sa') . "']";

		$datepicker_options_common .= ", dayNamesShort: ['" . smarty_function_jscalendar_tra('Sun') . "','" .
																													smarty_function_jscalendar_tra('Mon') . "','" .
																													smarty_function_jscalendar_tra('Tue') . "','" .
																													smarty_function_jscalendar_tra('Wed') . "','" .
																													smarty_function_jscalendar_tra('Thu') . "','" .
																													smarty_function_jscalendar_tra('Fri') . "','" .
																													smarty_function_jscalendar_tra('Sat') . "']";

		$datepicker_options_common .= ", monthNames: ['" . smarty_function_jscalendar_tra('January') . "','" .
																											 smarty_function_jscalendar_tra('February') . "','" .
																											 smarty_function_jscalendar_tra('March') . "','" .
																											 smarty_function_jscalendar_tra('April') . "','" .
																											 smarty_function_jscalendar_tra('May') . "','" .
																											 smarty_function_jscalendar_tra('June') . "','" .
																											 smarty_function_jscalendar_tra('July') . "','" .
																											 smarty_function_jscalendar_tra('August') . "','" .
																											 smarty_function_jscalendar_tra('September') . "','" .
																											 smarty_function_jscalendar_tra('October') . "','" .
																											 smarty_function_jscalendar_tra('November') . "','" .
																											 smarty_function_jscalendar_tra('December')."']";

		$datepicker_options_common .= ", monthNamesShort: ['" . smarty_function_jscalendar_tra('Jan') . "','" .
																														smarty_function_jscalendar_tra('Feb') . "','" .
																														smarty_function_jscalendar_tra('Mar') . "','" .
																														smarty_function_jscalendar_tra('Apr') . "','" .
																														smarty_function_jscalendar_tra('May') . "','" .
																														smarty_function_jscalendar_tra('Jun') . "','" .
																														smarty_function_jscalendar_tra('Jul') . "','" .
																														smarty_function_jscalendar_tra('Aug') . "','" .
																														smarty_function_jscalendar_tra('Sep') . "','" .
																														smarty_function_jscalendar_tra('Oct') . "','" .
																														smarty_function_jscalendar_tra('Nov') . "','" .
																														smarty_function_jscalendar_tra('Dec')."']";
		$datepicker_options_common .= '}';
	}

	$datepicker_options .= $datepicker_options_common;

	$html = '<input type="hidden" id="' . $params['id'] . '"' . $name  . ' value="'.$params['date'].'">';
	$html .= '<input type="hidden" id="tzoffset" name="tzoffset" value="">';
	$headerlib->add_jq_onready('$("input[name=tzoffset]").val((new Date()).getTimezoneOffset());');
	if( isset($params['isutc']) && $params['isutc'] )
		$headerlib->add_jq_onready('$("#' . $params['id'] . '").val(' . intval($params['date']) . ' + (new Date()).getTimezoneOffset()*60);');
	$html .= '<input type="text" style="width:225px" class="form-control" id="' . $params['id'] . '_dptxt" value="">';	// text version of datepicker date

	$display_tz = $tikilib->get_display_timezone();
	if ( $display_tz == '' ) {
		$display_tz = 'UTC';
	}
	if (strpos($display_tz, 'Etc/GMT+') !== false) {
		$display_tz = str_replace('Etc/GMT+', 'GMT-', $display_tz);
	} else if (strpos($display_tz, 'Etc/GMT-') !== false) {
		$display_tz = str_replace('Etc/GMT-', 'GMT+', $display_tz);
	}

	// TODO use a parsed version of $prefs['short_date_format']
	// Note: JS timestamp is in milliseconds - php is seconds
	// Note: adding local browser offset if php date is in UTC, see JsCalendar tracker field
	if (!isset($params['showtime']) || $params['showtime'] === 'n') {

		$command = 'datepicker';
		$js_val = empty($params['date']) ? '""' : '$.datepicker.formatDate( "' .
			$prefs['short_date_format_js'] . '", new Date('.$params['date'].'* 1000 + '.(isset($params['isutc']) && $params['isutc'] ? '(new Date()).getTimezoneOffset()*60*1000' : '0').'))';
		$headerlib->add_jq_onready(
			'$("#' . $params['id'] . '_dptxt").val(' . $js_val . ').tiki("' .
			$command . '", "jscalendar", ' . $datepicker_options . ');'
		);

	} else {
		// add timezone info if showing the time
		$html .= '<span class="description">' . tra('Time zone') . ': ' . $display_tz . '</span>';
		// datetime picker

		$command = 'datetimepicker';

		$js_val1 = empty($params['date']) ? '' : '
var dt = new Date('.$params['date'].'* 1000 + '.(isset($params['isutc']) && $params['isutc'] ? '(new Date()).getTimezoneOffset()*60*1000' : '0').');
var tm = { hour: dt.getHours(), minute: dt.getMinutes(), second: dt.getSeconds() };
';
		$js_val2 = empty($params['date']) ? '""' : '$.datepicker.formatDate( "' .
					$prefs['short_date_format_js'] . '", dt) + " " + $.datepicker.formatTime("' .
					$prefs['short_time_format_js'] . '", tm)';

		$headerlib->add_jq_onready(
			$js_val1 . '$("#' . $params['id'] . '_dptxt").val(' . $js_val2 . ').tiki("' . 
			$command . '", "jscalendar", ' . $datepicker_options . ');'
		);
	}

	$smarty->loadPlugin('smarty_function_icon');
	$icon = smarty_function_icon(['name' => 'calendar'], $smarty);
	$headerlib->add_jq_onready("$('#" . $params['id'] . "').closest('div.jscal').find(' button.ui-datepicker-trigger').empty().append('$icon').addClass('btn btn-sm btn-link').css({'padding' : '0px', 'font-size': '16px'});");

	return '<div class="jscal" style="margin-bottom:10px">' . $html . '</div>';
}

function smarty_function_jscalendar_tra($str)
{
	return str_replace("'", "\\'", tra($str));
}

