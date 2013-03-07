<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_countdown_info()
{
	return array(
		'name' => tra('Countdown'),
		'documentation' => 'PluginCountdown',
		'description' => tra('Display a countdown to a specified date.'),
		'prefs' => array('wikiplugin_countdown'),
		'icon' => 'img/icons/clock.png',
		'body' => tra('Text to append to the countdown.'),
		'tags' => array( 'basic' ),
		'params' => array(
			'enddate' => array(
				'required' => true,
				'name' => tra('End Date'),
				'description' => tra('Target date and time. Multiple formats accepted.'),
				'default' => '',
			),
			'show' => array(
				'required' => false,
				'name' => tra('Items to Show'),
				'description' => tra(
					'Select: y=years, o=months, d=days, h=hours, m=minutes, s=seconds.
					Enter multiple values as: yodhms. Must be in the order of descending length, and a time unit should not be skipped.
					If blank, all non-zero date parts are shown.'
				),
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
				'description' => tra(
					'Will use calendar day difference when set to Yes (y) and time units are not shown.
					Result is that tomorrow, for example, is always shown as a one day away even if less than 24 hours from now. No (n) is the default.'
				),
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
				'description' => tra(
					'If Yes (y), will display amount of time since the event (default).
					If No (n) and if there is body text, will display "is over" or custom text set in text parameter after body text.'
				),
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
				'description' => tra(
					'Text that will show with the countdown and body text.
					Set to "default" or leave empty to show "xxx days until/since [body text]", except that if
					 the since parameter is set to No (n), "[body text] is over" will show after the end date has passed.
					Also, if no time is shown because of the time units being displayed (for example, only years are shown and it\'s
					less than a year before/after the end date) then "[body text] will happen in less than a year/happened in the last year" will show.
					Or set pipe-separated custom text as follows: before date|before date by less than shortest time unit shown|after date
					|after date by less than shortest time unit shown|after date and since set to No (n).
					Set to silent for no text.'
				),
				'accepted' => tra('default, silent, custom: before event|after event|after event when time not shown'),
				'filter' => 'text',
				'default' => '',
			),
			'thousands' => array(
				'required' => false,
				'name' => tra('Thousands Separator'),
				'description' => tra(
					'Set the thousands separator for results of 1,000 or more.
					Choices are comma (c), decimal (d), space (s), or leave blank for no separator.'
				),
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
	global $tikilib, $tikidate;
	extract($params, EXTR_SKIP);
	$ret = '';
	//must have an enddate
	if (!isset($enddate)) {
		return '<strong>' . tra('COUNTDOWN: Missing "enddate" parameter for plugin') . '</strong><br />';
	}
	//set user timezone
	$tz = $tikilib->get_display_timezone();
	$tikidate->setTZbyID($tikilib->get_display_timezone());
	//set now date & time
	$nowobj = $tikidate->date;
	$now = $tikidate->getTime();
	$nowstring = $nowobj->format('Y-m-d H:i:s');
	//can replace the above line with the below when 5.3 becomes a minimum requirement
//	$now = $nowobj->getTimestamp();

	//set then date & time
	$tikidate->setDate(strtotime($enddate));
	$thenobj = $tikidate->date;
	$then = $tikidate->getTime();
	//can replace the above line with the below when 5.3 becomes a minimum requirement
//	$then = $thenobj->getTimestamp();

	$difference = $then - $now;
	//get difference in time of day for use in determining calendar days
	$tikidate->setDate(strtotime($nowstring));
	$thenadj = $tikidate->date;
	$thenadj->setTime($thenobj->format('H'), $thenobj->format('i'), $thenobj->format('s'));
	$thentime = $tikidate->getTime();
	//can replace the above line with the below when 5.3 becomes a minimum requirement
//	$thentime2 = $thenadj->getTimestamp();
	$timediff = $thentime - $now;

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
	if (phpversion() >= 5.3) {
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
	//TODO the following else section can be removed once PHP 5.3 becomes a minimum requirement
	} else {
		if ($difference > 0) {
			$diff = array(
				0 => $thenobj->format('Y') - $nowobj->format('Y'),
				1 => $thenobj->format('m') - $nowobj->format('m'),
				2 => $thenobj->format('d') - $nowobj->format('d'),
				3 => $thenobj->format('H') - $nowobj->format('H'),
				4 => $thenobj->format('i') - $nowobj->format('i'),
				5 => $thenobj->format('s') - $nowobj->format('s')
			);
		} else {
			$diff = array(
				0 => $nowobj->format('Y') - $thenobj->format('Y'),
				1 => $nowobj->format('m') - $thenobj->format('m'),
				2 => $nowobj->format('d') - $thenobj->format('d'),
				3 => $nowobj->format('H') - $thenobj->format('H'),
				4 => $nowobj->format('i') - $thenobj->format('i'),
				5 => $nowobj->format('s') - $thenobj->format('s')
			);
		}

		//units for each date part used in next section
		$units = array(
			0 => 0,		//years
			1 => 12,	//months
			2 => (cal_days_in_month(CAL_GREGORIAN, $nowobj->format('m'), $nowobj->format('Y'))),	//days
			3 => 24,	//hours
			4 => 60,	//minutes
			5 => 60		//seconds
		);
		//adjust raw time part differences when necessary
		foreach ($diff as $k => $d) {
			if ($d != 0) {
				if ($d < 0) {
					$diff[$k] = $k == 0 ? abs($d) : $d + $units[$k];
				}
				if (isset($diff[$k + 1]) && $diff[$k + 1] < 0) {
					--$diff[$k];
				} elseif (isset($diff[$k + 1]) && $diff[$k + 1] == 0 && $k != 5) {
					if (isset($diff[$k + 2]) && $diff[$k + 2] < 0) {
						--$diff[$k];
						$diff[$k + 1] = --$units[$k + 1];
					} elseif (isset($diff[$k + 2]) && $diff[$k + 2] == 0 && $k != 5) {
						if (isset($diff[$k + 3]) && $diff[$k + 3] < 0) {
							--$diff[$k];
							$diff[$k + 2] = --$units[$k + 2];
							$diff[$k + 1] = --$units[$k + 1];
						} elseif (isset($diff[$k + 3]) && $diff[$k + 3] == 0 && $k != 5) {
							if (isset($diff[$k + 4]) && $diff[$k + 4] < 0) {
								--$diff[$k];
								$diff[$k + 3] = --$units[$k + 3];
								$diff[$k + 2] = --$units[$k + 2];
								$diff[$k + 1] = --$units[$k + 1];
							}
						}
					}
				}
			}
		}
		$diff = array(
			'y' => $diff[0],
			'o' => $diff[1],
			'd' => $diff[2],
			'h' => $diff[3],
			'm' => $diff[4],
			's' => $diff[5],
			'invert' => $difference > 0 ? 1 : 0,
			'days' => abs(intval($difference/86400)),
		);
	}

	if (empty($show)) {
		// Set default. If no explicit SHOW, then show everything.
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
	if ($diff['invert'] == 1 || $since == 'y') {
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
		if (!isset($text) || ($text && $text != 'silent')) {
			$word = '';
			if (isset($text) && ($text == 'default' || strpos($text, '|') === false)) {
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
								$nowtext = tra('will happen in less than a hour');
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
					if ($diff['invert'] == 1) {
						$word = tra('until');
					} else {
						$word = tra('since');
					}
				}
			} elseif (!empty($text) && strpos($text, '|') !== false) {
				$custom = explode('|', $text);
				//if $ret is empty here, it means the time before/after the event is shorter than the unit of time being shown
				if (empty($ret)) {
					if ($diff['invert'] == 1) {
						//$custom[1] = text to display before event occurs
						$data = $data . ' ' . $custom[1];
					} else {
						//$custom[3] = text to display after event occurs
						$data = $data . ' ' . $custom[3];
					}
				} else {
					if ($diff['invert'] == 1) {
						//$custom[0] = text to display before event occurs
						$word = $custom[0];
					} else {
						//$custom[2] = text to display after event occurs
						$word = $custom[2];
					}
				}
			}
			$ret .= ' ' . $word . ' ' . $data;
		}
	//if after the event and no countdown shown
	} elseif (!empty($data)) {
		if (strpos($text, '|') === false) {
			$word = tra('is over');
		} else {
			$custom = explode('|', $text);
			//$custom[4] = text to display after event occurs when no countdown is shown because since set to n
			$word = $custom[4];
		}
		$ret = $data . ' ' . $word;
	}

	return $ret;
}
