<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_countdown_info()
{
	return array(
		'name' => tra('Countdown'),
		'documentation' => 'PluginCountdown',
		'description' => tra('Display the time until or after a date and time'),
		'prefs' => array('wikiplugin_countdown'),
		'iconname' => 'history',
		'body' => tra('Text to append to the countdown.'),
		'tags' => array( 'basic' ),
		'introduced' => 1,
		'params' => array(
			'enddate' => array(
				'required' => true,
				'name' => tra('End Date'),
				'description' => tra('Target date and time. Multiple formats accepted.'),
				'since' => '1',
				'filter' => 'date',
				'default' => '',
			),
			'show' => array(
				'required' => false,
				'name' => tra('Items to Show'),
				'description' => tr(
					'Select: %0y%1=years, %0o%1=months, %0d%1=days, %0h%1=hours, %0m%1=minutes, %0s%1=seconds.
					Enter multiple values as: %0yodhms%1. Must be in the order of descending length, and a time unit should not be skipped.
					If blank, the time is shown down to the hour if not zero.', '<code>', '</code>'
				),
				'since' => '4.2',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Years'), 'value' => 'y'),
					array('text' => tra('Years, Months'), 'value' => 'yo'),
					array('text' => tra('Years, Months, Days'), 'value' => 'yod'),
					array('text' => tra('Years, Months, Days, Hours'), 'value' => 'yodh'),
					array('text' => tra('Years, Months, Days, Hours, Minutes'), 'value' => 'yodhm'),
					array('text' => tra('Years, Months, Days, Hours, Minutes & Seconds'), 'value' => 'yodhms'),
					array('text' => tra('Months'), 'value' => 'o'),
					array('text' => tra('Months, Days'), 'value' => 'od'),
					array('text' => tra('Months, Days, Hours'), 'value' => 'odh'),
					array('text' => tra('Months, Days, Hours, Minutes'), 'value' => 'odhm'),
					array('text' => tra('Months, Days, Hours, Minutes & Seconds'), 'value' => 'odhms'),
					array('text' => tra('Days'), 'value' => 'd'),
					array('text' => tra('Days & Hours'), 'value' => 'dh'),
					array('text' => tra('Days, Hours & Minutes'), 'value' => 'dhm'),
					array('text' => tra('Days, Hours, Minutes & Seconds'), 'value' => 'dhms'),
					array('text' => tra('Hours'), 'value' => 'h'),
					array('text' => tra('Hours & Minutes'), 'value' => 'hm'),
					array('text' => tra('Hours, Minutes & Seconds'), 'value' => 'hms'),
					array('text' => tra('Minutes'), 'value' => 'm'),
					array('text' => tra('Minutes & Seconds'), 'value' => 'ms'),
					array('text' => tra('Seconds'), 'value' => 's'),
				),
			),
			'caldays' => array(
				'required' => false,
				'name' => tra('Calendar Days'),
				'description' => tr(
					'Will use calendar day difference when set to Yes (%0y%1) and time units are not shown.
					Result is that tomorrow, for example, is always shown as a one day away even if less than 24 hours
					from now. No (%0n%1) is the default.', '<code>', '</code>'
				),
				'since' => '9.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'since' => array(
				'required' => false,
				'name' => tra('Handle Past Events'),
				'description' => tr(
					'If Yes (%0y%1), will display amount of time since the event (default).
					If No (%0n%1) and if there is body text, will display "is over" or custom text set in text parameter
					after body text.', '<code>', '</code>'
				),
				'since' => '4.2',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'text' => array(
				'required' => false,
				'name' => tra('Text'),
				'description' => tr(
					'Text that will show with the countdown and body text.
					Set to %0default%1 or leave empty to show "xxx days until/since [body text]", except that if
					 the since parameter is set to No (%0n%1), "[body text] is over" will show after the end date has passed.
					Also, if no time is shown because of the time units being displayed (for example, only years are shown and it\'s
					less than a year before/after the end date) then "[body text] will happen in less than a year/happened in the last year" will show.
					Or set pipe-separated custom text as follows: %0before date|before date by less than shortest time unit shown|after date
					|after date by less than shortest time unit shown|after date and since set to No (n)%1.
					Set to %0silent%1 for no text.', '<code>', '</code>'
				),
				'accepted' => tra('default, silent, custom: before event|after event|after event when time not shown'),
				'since' => '9.0',
				'filter' => 'text',
				'default' => '',
			),
			'thousands' => array(
				'required' => false,
				'name' => tra('Thousands separator'),
				'description' => tr(
					'Set the thousands separator for results of 1,000 or more.
					Choices are comma (%0c%1), decimal (%0d%1), space (%0s%1), or leave blank for no separator.', '<code>', '</code>'
				),
				'since' => '9.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Comma'), 'value' => 'c'),
					array('text' => tra('Period'), 'value' => 'p'),
					array('text' => tra('Space'), 'value' => 's'),
				),
			),
		),
	);
}

function wikiplugin_countdown($data, $params)
{
	extract($params, EXTR_SKIP);
	//must have an enddate
	if (!isset($enddate)) {
		return '<strong>' . tra('Countdown: Missing "enddate" parameter for plugin') . '</strong><br />';
	}
	//set now date and time
	global $tikilib;
	$tz = $tikilib->get_display_timezone();
	$nowobj = new DateTime(null, new DateTimeZone($tz));
	//set then date & time
	try {
		$thenobj = new DateTime($enddate, new DateTimeZone($tz));
	} catch (Exception $e) {
		return "<span class=\"error\">{$e->getMessage()}</span>";
	}

	$difference = $thenobj->getTimestamp() - $nowobj->getTimestamp();

	//get difference in time of day for use in determining calendar days
	$nowadj = new DateTime($nowobj->format('Y-m-d H:i:s'), new DateTimeZone($tz));
	$nowadj->setTime($thenobj->format('H'), $thenobj->format('i'), $thenobj->format('s'));
	$timediff = $nowadj->getTimestamp() - $nowobj->getTimestamp();

	//Set thousands separator
	if (!empty($thousands)) {
		switch ($thousands) {
			case 'c':
				$thousands = ',';
    			break;
			case 'p':
				$thousands = '.';
    			break;
			case 's':
				$thousands = ' ';
    			break;
			default:
				$thousands = '';
		}
	} else {
		$thousands = '';
	}

	//Calculate the date interval
	$interval = $thenobj->diff($nowobj);
	// put into an array to maintain compatibility with prior versions
	$diff = array(
		'y' => $interval->y,
		//using o for backwards compatibility since plugin had used m for minutes
		'o' => $interval->m,
		'd' => $interval->d,
		'h' => $interval->h,
		//using m for backwards compatibility since plugin had used m for minutes
		'm' => $interval->i,
		's' => $interval->s,
		'invert' => $interval->invert,
		'days' => $interval->days,
	);

	if (empty($show)) {
		// Set default. If no explicit SHOW, then show down to the hour.
		$show_years = true;
		$show_months =  true;
		$show_days = true;
		$show_hours = true;
		$show_minutes = false;
		$show_seconds = false;
	} else {
		$show_years = (strpos($show, 'y') === false) ? false : true;
		$show_months = (strpos($show, 'o') === false)  ? false : true;
		$show_days = strpos($show, 'd') === false ? false : true;
		$show_hours = strpos($show, 'h') === false ? false : true;
		$show_minutes = strpos($show, 'm') === false ? false : true;
		$show_seconds = strpos($show, 's') === false ? false : true;
	}

	if ($show_days) {
		if ($show_months) {
			$days = $diff['d'];
		} else {
			$days = $diff['days'];
		}
		if ((($timediff < 0 && $diff['invert'] == 1) || ($timediff > 0 && $diff['invert'] == 0))
			&& isset($caldays) && $caldays == 'y' && $show_hours === false) {
			$int = is_int($days);
			$diff['caldays'] = $days + 1;
		} else {
			$diff['caldays'] = $days;
		}
	}

	//create the countdown string
	$ret = '';
	$word = '';
	if ($diff['invert'] == 1 || (isset($since) && $since == 'y')) {
	//either before the event or if countdown also shown after the event
		//calculate total time in hours, minutes or seconds
		$diff['months'] = abs(($diff['y']*12) + $diff['o']);
		$diff['hours'] = abs(intval($difference/60/60));
		$diff['minutes'] = abs(intval($difference/60));
		$diff['seconds'] = abs($difference);
		$comma = ', ';
		$and = ' ' . tra('and') . ' ';
		//years
		if ($show_years && $diff['y']) {
			$y_label = $diff['y'] == 1 ? tra('year') : tra('years');
			$ret = number_format($diff['y'], 0, '', $thousands) . ' ' . $y_label;
		}
		//months
		if ($show_months && ($diff['o'] || $diff['months'])) {
			if ($show_years) {
				if ($diff['o']) {
					if (!empty($ret)) {
						//if not the last item, precede with comma, otherwise and
						$sep = ($show_days && $diff['caldays']) || ($show_hours && $diff['h']) || ($show_minutes && $diff['m'])
							|| ($show_seconds && $diff['s']) ? $comma : $and;
					} else {
						$sep = '';
					}
					$o_label = $diff['o'] == 1 ? tra('month') : tra('months');
					$ret .= $sep . $diff['o'] . ' ' . $o_label;
				}
			} else {
				//use months as largest unit if years not shown
				$o_label = $diff['months'] == 1 ? tra('month') : tra('months');
				$ret = number_format($diff['months'], 0, '', $thousands) . ' ' . $o_label;
			}
		}
		//days
		if ($show_days && $diff['caldays']) {
			$d_label = $diff['caldays'] == 1 ? tra('day') : tra('days');
			if ($show_months) {
				if ($diff['caldays']) {
					if (!empty($ret)) {
						//if not the last item, precede with comma, otherwise and
						$sep = ($show_hours && $diff['h']) || ($show_minutes && $diff['m']) || ($show_seconds && $diff['s'])
								? $comma : $and;
					} else {
						$sep = '';
					}
					$ret .= $sep . $diff['caldays'] . ' ' . $d_label;
				}
			} else {
				$ret = number_format($diff['caldays'], 0, '', $thousands) . ' ' . $d_label;
			}
		}
		//hours
		if ($show_hours && ($diff['h'] || $diff['hours'])) {
			if ($show_days) {
				if ($diff['h']) {
					if (!empty($ret)) {
						//if not the last item, precede with comma, otherwise and
						$sep = ($show_minutes && $diff['m']) || ($show_seconds && $diff['s']) ? $comma : $and;
					} else {
						$sep = '';
					}
					$h_label = $diff['h'] == 1 ? tra('hour') : tra('hours');
					$ret .= $sep . $diff['h'] . ' ' . $h_label;
				}
			} else {
				$h_label = $diff['hours'] == 1 ? tra('hour') : tra('hours');
				$ret = number_format($diff['hours'], 0, '', $thousands)  . ' ' . $h_label;
			}
		}
		//minutes
		if ($show_minutes && ($diff['m'] || $diff['minutes'])) {
			if ($show_hours) {
				if ($diff['m']) {
					if (!empty($ret)) {
						//if not the last item, precede with comma, otherwise and
						$sep = $show_seconds && $diff['s'] ? $comma : $and;
					} else {
						$sep = '';
					}
					$m_label = $diff['m'] == 1 ? tra('minute') : tra('minutes');
					$ret .= $sep . $diff['m'] . ' ' . $m_label;
				}
			} else {
				$m_label = $diff['minutes'] == 1 ? tra('minute') : tra('minutes');
				$ret = number_format($diff['minutes'], 0, '', $thousands) . ' ' . $m_label;
			}
		}
		//seconds
		if ($show_seconds && ($diff['s'] || $diff['seconds'])) {
			if ($show_minutes) {
				if ($diff['s']) {
					if (!empty($ret)) {
						$sep = $and;
					} else {
						$sep = '';
					}
					$s_label = $diff['s'] == 1 ? tra('second') : tra('seconds');
					$ret .= $sep . $diff['s'] . ' ' . $s_label;
				}
			} else {
				$s_label = $diff['seconds'] == 1 ? tra('second') : tra('seconds');
				$ret = number_format($diff['seconds'], 0, '', $thousands) . ' ' . $s_label;
			}
		}
		//add text
		if (empty($text) || (!empty($text) && $text != 'silent')) {
			if (empty($text) || $text == 'default' || strpos($text, '|') === false) {
				//if $ret is empty here, it means the time before/after the event is shorter than the smallest unit of time being shown
				if (empty($ret)) {
					if ($diff['invert'] == 1) {
						switch (substr($show, -1)) {
							case 'y':
								$nowtext = tra('will happen in less than a year');
    							break;
							case 'o':
								$nowtext = tra('will happen in less than a month');
    							break;
							case 'd':
								$nowtext = tra('will happen in less than a day');
    							break;
							case 'h':
								$nowtext = tra('will happen in less than an hour');
    							break;
							case 'm':
								$nowtext = tra('will happen in less than a minute');
    							break;
							case 's':
								$nowtext = tra('is happening this second');
    							break;
						}
					} else {
						switch (substr($show, -1)) {
							case 'y':
								$nowtext = tra('happened in the last year');
    							break;
							case 'o':
								$nowtext = tra('happened in the last month');
    							break;
							case 'd':
								$nowtext = tra('happened in the last day');
    							break;
							case 'h':
								$nowtext = tra('happened in the last hour');
    							break;
							case 'm':
								$nowtext = tra('happened in the last minute');
    							break;
							case 's':
								$nowtext = tra('is happening this second');
    							break;
						}
					}
					$data = $data . ' ' . $nowtext;
				} else {
					if (empty($text) || $text == 'default') {
						if ($diff['invert'] == 1) {
							$word = tra('until');
						} else {
							$word = tra('since');
						}
					} else {
						$word = $text;
					}
				}
			} elseif (!empty($text) && strpos($text, '|') !== false) {
				$custom = explode('|', $text);
				//if $ret is empty here, it means the time before/after the event is shorter than the unit of time being shown
				if (empty($ret)) {
					if ($diff['invert'] == 1) {
						//$custom[1] = text to display before event occurs when closer than the unit of time being shown
						$cust1 = !empty($custom[1]) ? $custom[1] : '';
						$data = $data . ' ' . $cust1;
					} else {
						//$custom[3] = text to display after event occurs when closer than the unit of time being shown
						$cust3 = !empty($custom[3]) ? $custom[3] : '';
						$data = $data . ' ' . $cust3;
					}
				} else {
					if ($diff['invert'] == 1) {
						//$custom[0] = text to display before event occurs
						$word = !empty($custom[0]) ? $custom[0] : '';
					} else {
						//$custom[2] = text to display after event occurs
						$word = !empty($custom[2]) ? $custom[2] : '';
					}
				}
			}
			$ret .= ' ' . $word . ' ' . $data;
		}
	//if after the event and no countdown shown
	} elseif (!empty($data)) {
		if (empty($text) || strpos($text, '|') === false) {
			$word = tra('is over');
		} elseif (strpos($text, '|') !== false) {
			$custom = explode('|', $text);
			//$custom[4] = text to display after event occurs when no countdown is shown because since set to n
			$word = !empty($custom[4]) ? $custom[4] : '';
		}
		$ret = $data . ' ' . $word;
	}

	return $ret;
}
