<?php # $CVSHeader$

# Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once('tiki-setup.php');
require_once('lib/ircbot/ircbotlib.php');

$r_log	= !empty($_REQUEST['log']) ? $_REQUEST['log'] : '';
$r_channel	= !empty($_REQUEST['channel']) ? $_REQUEST['channel'] : '';
$r_date		= !empty($_REQUEST['date']) ? $_REQUEST['date'] : '';

$files = array();
$d = opendir(TIKI_IRCBOT_LOG_DIR);
while ($file = readdir($d)) {
	if ($file == '.' || $file == '..')
		continue;
	list($date, $channel) = split('_', $file);
	list($channel, $junk) = split('\.', $channel);

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
	
	$revdate = 1000000 - $date;

	$files[$file] = '' . $revdate . $channel;
}
closedir($d);

arsort($files);

$irc_log_options = array();
foreach ($files as $file => $junk) {
	list($yymmdd, $channel) = split('_', $file);
	list($channel, $junk) = split('\.', $channel);
	$time = strtotime($yymmdd);
	$date = strftime('%a %d %b %Y', $time);
	$fullname = TIKI_IRCBOT_LOG_DIR . '/' . $file;
	$irc_log_options[$file] = $date . ' #' . $channel . ' (' . filesize($fullname) . ')';
}

#\TODO sort by date, then by channel

arsort($irc_log_options);
$file = '';
if ($irc_log_options) {
	foreach($irc_log_options as $file => $dummy) {
		break;
	}
}

$irc_log_selected = $r_log ? $r_log : $file;

$fullname = TIKI_IRCBOT_LOG_DIR . '/' . $irc_log_selected;

# \TODO rewrite dates to be local to the user

$action_map = array(
	'Action'	=> 'a',
	'Join'		=> 'v',
	'Log'		=> 'v',
	'Nick'		=> 'v',
	'Part'		=> 'v',
	'Privmsg'	=> 'n',
	'Topic'		=> 'v',
);

$irc_log_rows = array();

$name_hash = array();

$irc_log_channel = '';
$irc_log_time	 = '';

if (is_file($fullname)) {
	$file = basename($fullname);
	list($yymmdd, $irc_log_channel) = split('_', $file);
	$irc_log_time = mktime(12, 0, 0, substr($yymmdd, 2, 2), substr($yymmdd, 4, 2), substr($yymmdd, 0, 2));
	list($irc_log_channel, $junk) = split('\.', $irc_log_channel);
	$irc_log_channel = '#' . $irc_log_channel;

	$yy = substr($yymmdd, 0, 2);
	$mm = substr($yymmdd, 2, 2);
	$dd = substr($yymmdd, 4, 2);
	
	$lines = file($fullname);
	foreach($lines as $line) {
		$line = trim($line);
		if (preg_match('/\[([^\]]*)\](.*)/', $line, $matches)) {
			$time = $matches[1];
			$line = trim($matches[2]);
		} else {
			$time = '';
		}

		$localtime = time();
		$name = '';
		if (preg_match('/(\d\d):(\d\d):(\d\d)/', $time, $matches)) {
			$hhmm = $matches[1] . $matches[2];
			$localtime = mktime($matches[1], $matches[2], $matches[3], $mm, $dd, $yy);
			if (!isset($name_hash[$hhmm])) {
				$name = '<a name="' . $hhmm . '">';
				$hash_hash[$hhmm] = 1;
			}
		}

		if (preg_match('/^([^: ]*):(.*)/', $line, $matches)) {
			$action = trim($matches[1]);
			$line	= trim($matches[2]);
			switch ($action) {
				case 'Join':
					$line = 'joined: ' . $line;
					break;
				case 'Log':
					$line = 'log ' . $line;
					break;
				case 'Part':
					$line = 'signoff: ' . $line;
					break;
			}
		} else {
			$action = 'Privmsg';
		}

		$foundnick = preg_match('/^<([^>]*)>(.*)/', $line, $matches);
		if ($foundnick) {
			$nick = trim($matches[1]);
			$line = trim($matches[2]);
		} else {
			$nick = '';
		}

		if (!$line)
			continue;

		if (preg_match('/(.*)((https?|ftp):\/\/[-_\.a-zA-Z0-9\/\?\&=%]*)(.*)/', $line, $matches)) {
			$line = htmlspecialchars($matches[1], ENT_NOQUOTES) .
				'<a href="' . $matches[2] . '">' . $matches[2] . '</a>' .
				htmlspecialchars($matches[4], ENT_NOQUOTES);
		} else {
			$line = htmlspecialchars($line, ENT_NOQUOTES);
		}

		$irc_log_rows[] = array(
			'action'	=> @$action_map[$action],
			'data'		=> $line,
			'name'		=> $name,
			'nick'		=> $nick,
			'time'		=> $time,
			'localtime'	=> $localtime,
		);
	}
}

$smarty->assign('irc_log_channel',	$irc_log_channel);
$smarty->assign('irc_log_time', 	$irc_log_time);
$smarty->assign('irc_log_options',	$irc_log_options);
$smarty->assign('irc_log_rows',		$irc_log_rows);
$smarty->assign('irc_log_selected',	$irc_log_selected);

// Display the template
$smarty->assign('mid','tiki-view_irc.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
