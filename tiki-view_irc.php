<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_irc.php,v 1.10 2004-03-27 21:23:52 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $CVSHeader$

# Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# \TODO rewrite dates to be local to the user
# \TODO max lines = 20/50/100/all
# \TODO refresh = 10/30/60/never

require_once ('tiki-setup.php');

require_once ('lib/irc/irclib.php');

$r_log = !empty($_REQUEST['log']) ? $_REQUEST['log'] : false;
$r_channel = !empty($_REQUEST['channel']) ? $_REQUEST['channel'] : false;
$r_date = !empty($_REQUEST['date']) ? $_REQUEST['date'] : false;
$r_showall = !empty($_REQUEST['showall']) ? $_REQUEST['showall'] : false;
$r_filter = !empty($_REQUEST['filter']) ? $_REQUEST['filter'] : false;

if ($r_log) {
	if (preg_match('/^([^\/]+)\/([^\/]+)\/(.*)/', $r_log, $m)) {
		$r_log = $m[1];

		if (!$r_date) {
			$r_date = $m[2];
		}

		if (!$r_channel) {
			$r_channel = $m[3];
		}
	}
}

$dfiles = array();
$d = opendir(IRC_LOG_DIR);

while ($file = readdir($d)) {
	if ($file == '.' || $file == '..') {
		continue;
	}

	$fullname = IRC_LOG_DIR . '/' . $file;

	if (is_dir($fullname)) {
		continue;
	}

	if (!@filesize($fullname)) {
		continue;
	}

	$dfiles[] = $file;
}

closedir ($d);

$last_date_by_file = array();

$files = array();

foreach ($dfiles as $file) {
	$fullname = IRC_LOG_DIR . '/' . $file;

	$channel = '';
	$start_date = '';
	$end_date = '';

	$a = IRC_Log_Parser::getChannelAndDate($file);

	$channel = $a['channel'];
	$start_date = $a['date'];
	$end_date = $start_date;

	if (!$channel) {
		$channel = 'unknown';
	}

	if (!$start_date) {
		$dates = IRC_Log_Parser::getDates($fullname);

		if ($dates) {
			$start_date = $dates['start'];

			$end_date = $dates['end'];
		}
	}

	$date = $start_date;
	$a = getdate($date);

	while ($date <= $end_date) {
		$key = (1000000 - date('ymd', $date)) . $channel;

		$e = array(
			'file' => $file,
			'date' => $date,
			'channel' => $channel,
		);

		$files[$key] = $e;

		if (!isset($last_date_by_file[$file]) || $date > $last_date_by_file[$file]) {
			$last_date_by_file[$file] = $date;
		}

		$a['mday']++;
		$date = mktime($a['hours'], $a['minutes'], $a['seconds'], $a['mon'], $a['mday'], $a['year']);
	}

	if (!$r_log) {
		if ($r_channel && !$r_date && $r_channel == $channel) {
			$r_log = $file;
		}

		if ($r_date && !$r_channel && $r_date == $date) {
			$r_log = $file;
		}

		if ($r_channel && $r_date && $r_channel == $channel && $r_date == $date) {
			$r_log = $file;
		}
	}
}

ksort ($files);

$irc_log_options = array();
$first_file = '';

foreach ($files as $key => $value) {
	$file = $value['file'];

	$date = $value['date'];
	$channel = $value['channel'];

	$yymmdd = date('ymd', $date);

	$f = IRC_LOG_DIR . '/' . $file;
	$irc_log_options[$file . '/' . $yymmdd . '/' . $channel]
		= $tikilib->get_long_date($date, $user). ' #' . $channel . ' (' .@filesize($f). ')';

	if (!$first_file) {
		$first_file = $file;
	}
}

$file = '';

if ($r_log) {
	$fullname = IRC_LOG_DIR . '/' . $r_log;

	if (@is_file($fullname)) {
		$file = $r_log;
	}
}

if (!$file) {
	$file = $first_file;
}

$fullname = IRC_LOG_DIR . '/' . $file;

if (!$r_date) {
	$a = @$last_date_by_file[$file];

	$r_date = $a['date'];
}

if (!$r_channel) {
	$a = @$last_date_by_file[$file];

	$r_channel = $a['channel'];
}

$irc_log_selected = $file . '/' . $r_date . '/' . $r_channel;

$irc_log_rows = array();

if (@is_file($fullname)) {
	$irc_log_rows = IRC_Log_Parser::parseFile($fullname, $r_date, $r_filter);
}

$irc_log_channel = '#' . $r_channel;
$irc_log_time = mktime(12, 0, 0, substr($r_date, 2, 2), substr($r_date, 4, 2), substr($r_date, 0, 2));

$smarty->assign('irc_log_channel', $irc_log_channel);
$smarty->assign('irc_log_time', $irc_log_time);
$smarty->assign('irc_log_options', $irc_log_options);
$smarty->assign('irc_log_rows', $irc_log_rows);
$smarty->assign('irc_log_selected', $irc_log_selected);
$smarty->assign('showall', $r_showall);
$smarty->assign('filter', $r_filter);

// Display the template
$smarty->assign('mid', 'tiki-view_irc.tpl');
$smarty->display("tiki.tpl");

?>