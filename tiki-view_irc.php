<?php # $CVSHeader$

# Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# \TODO rewrite dates to be local to the user

#error_reporting(E_ALL);

require_once('tiki-setup.php');
require_once('lib/ircbot/ircbotlib.php');

$r_log		= !empty($_REQUEST['log']) ? $_REQUEST['log']			: null;
$r_channel	= !empty($_REQUEST['channel']) ? $_REQUEST['channel']	: null;
$r_date		= !empty($_REQUEST['date']) ? $_REQUEST['date']			: null;

if ($r_log) {
	if (preg_match('/(.*)\/(.*)\/(.*)/', $r_log, $m)) {
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
closedir($d);

$last_date_by_file = array();

$files = array();
foreach ($dfiles as $file) {
	$fullname = IRC_LOG_DIR . '/' . $file;

	$channel 	= '';
	$start_date	= '';
	$end_date	= '';

	$a = IRC_Log_Parser::getChannelAndDate($file);

	$channel		= $a['channel'];
	$start_date		= $a['date'];
	$end_date	= $start_date;

	if (!$channel) {
		$channel = 'unknown';
	}

	if (!$start_date) {
		$dates = IRC_Log_Parser::getDates($fullname);
		if ($dates) {
			$start_date		= $dates['start'];
			$end_date		= $dates['end'];
		}
	}		

#echo "<pre>\n";		
	$date = $start_date;
#echo '1date=',$date,"\n";
	$a = getdate($date);
#echo '2date=',$date,"\n";
	while ($date <= $end_date) {
#echo '3date=',$date,"\n";
		$key = (1000000 - date('ymd', $date)) . $channel;
#echo '4key=',$key,"\n";
#echo '5date=',$date,"\n";

		$e = array(
			'file'		=> $file,
			'date'		=> $date,
			'channel'	=> $channel,
		);

		$files[$key] = $e;
		if (!isset($last_date_by_file[$file]) || $date > $last_date_by_file[$file]) {
			$last_date_by_file[$file] = $date;
		}		

#echo '6date=',$date,"\n";
		$a['mday']++;
#echo '7date=',$date,"\n";
		
		$date = mktime($a['hours'], $a['minutes'], $a['seconds'], 
			$a['mon'], $a['mday'], $a['year']);
#echo '8date=',$date,"\n";
	}
		
/*
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
*/
}

#print_r($files);

ksort($files);

#print_r($files);

$irc_log_options = array();
foreach ($files as $key => $value) {
	$file = $value['file'];
	$date = $value['date'];
	$channel = $value['channel'];
	
	$yymmdd = date('ymd', $date);
	
	$fullname = IRC_LOG_DIR . '/' . $file;
	$irc_log_options[$file . '/' . $yymmdd . '/' . $channel] = strftime('%a %d %b %Y', $date) . ' #' . $channel . 
		' (' . @filesize($fullname) . ')';
}

$file = '';
if ($irc_log_options) {
	foreach($irc_log_options as $file => $dummy) {
		break;
	}
}

$irc_log_selected = $r_log ? $r_log : $file;

if (!$r_date) {
	$r_date = @$last_date_by_file[$irc_log_selected];
}

$fullname = IRC_LOG_DIR . '/' . $irc_log_selected;

$irc_log_selected = $irc_log_selected . '/' . $r_date . '/' . $r_channel;

$irc_log_rows = array();
if (is_file($fullname)) {
	$irc_log_rows = IRC_Log_Parser::parseFile($fullname, $r_date);
}

$irc_log_channel = '#' . $r_channel;
$irc_log_time	 = mktime(12, 0, 0, substr($r_date, 2, 2), substr($r_date, 4, 2), substr($r_date, 0, 2));

$smarty->assign('irc_log_channel',	$irc_log_channel);
$smarty->assign('irc_log_time', 	$irc_log_time);
$smarty->assign('irc_log_options',	$irc_log_options);
$smarty->assign('irc_log_rows',		$irc_log_rows);
$smarty->assign('irc_log_selected',	$irc_log_selected);

// Display the template
$smarty->assign('mid','tiki-view_irc.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
