<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/smarty_tiki/modifier.username.php');
$access->check_feature('feature_minichat');
header("Pragma: public");
header("Pragma: no-cache");
header("Cache-Control: no-cache, must-revalidate, no-store, post-check=0, pre-check=0, max-age=0");
header("Expires: Tue, 27 Jul 1997 02:30:00 GMT"); // Date in the past
header('Content-Type: application/javascript; charset=utf-8');
$timeout_min = 1000;
$timeout_max = 15000;
$timeout_inc = 1000;
$lasttimeout = (int)$_REQUEST['lasttimeout'];
if ($lasttimeout < $timeout_min) $lasttimeout = $timeout_min;
$chans = explode(',', $_REQUEST['chans']);
/**
 * @param $channel
 * @return string
 */
function escapechannel($channel)
{
	$channel = preg_replace('/[^a-zA-Z0-9\-\_]/i', '', $channel);
	$channel = substr($channel, 0, 30);
	return '#' . $channel;
}

/**
 * @param $chans
 */
function initchannelssession($chans)
{
	$_SESSION['minichat_channels'] = array();
	foreach ($chans as $chan) {
		$vals = explode(';', $chan);
		$channel = escapechannel($vals[0]);
		$_SESSION['minichat_channels'][] = $channel;
	}
}
if (isset($_REQUEST['msg'])) {
	$msg = $_REQUEST['msg'];
	$msg = strtr($msg, "\n\r\t", "   ");
	$msgon = isset($_REQUEST['msgon']) ? $_REQUEST['msgon'] : null;
	if (empty($msg)) $msgon = null;
} else {
	$msg = '';
	$msgon = null;
}
if (substr($msg, 0, 1) == '/') {
	$words = explode(' ', $msg);
	switch ($words[0]) {
		case '/join':
			$words[1] = escapechannel($words[1]);
			echo "minichat_addchannel('" . $words[1] . "');\n";
			if (!isset($_SESSION['minichat_channels'])) initchannelssession($chans);
			$k = array_search($words[1], $_SESSION['minichat_channels']);
			if ($k === false) $_SESSION['minichat_channels'][] = $words[1];
    		break;
	}
}
foreach ($chans as $chan) {
	$vals = explode(';', $chan);
	$channel = escapechannel($vals[0]);
	$lastid = (int)$vals[1];
	$closed = false;
	if (($msgon == $channel) && (!is_null($channel))) {
		$time = time();
		if (substr($msg, 0, 1) == '/') {
			$words = explode(' ', $msg);
			switch ($words[0]) {
				case '/part':
				case '/close':
					echo "minichat_removechannel('" . $channel . "');\n";
					$closed = true;
					if (!isset($_SESSION['minichat_channels'])) initchannelssession($chans);
					$k = array_search($words[1], $_SESSION['minichat_channels']);
					if ($k !== false) unset($_SESSION['minichat_channels'][$k]);
    				break;
			}
		} else {
				$tikilib->query("INSERT INTO tiki_minichat (nick,user,ts,channel,msg) VALUES (?,?,?,?,?)", array(smarty_modifier_username($user), $user, $tikilib->now, $channel, $msg));
				$lastid = 0;
		}
			$lasttimeout = $timeout_min;
	}
	if ($closed) continue;
	if (empty($channel)) continue;
	if ($lastid > 0) {
		$result = $tikilib->query("SELECT MAX(id) AS maxid FROM tiki_minichat WHERE channel=?", array($channel));
		$res = $result->fetchRow();
		$maxid = $res['maxid'];
		if ($maxid != $lastid) {
			$lastid = 0;
			$lasttimeout = $timeout_min;
		} else $lasttimeout+= $timeout_inc;
	}
	if ($lastid == 0) {
		$result = $tikilib->query("SELECT * FROM tiki_minichat WHERE channel=? ORDER by id desc LIMIT 100", array($channel));
		$msgtotal = "";
		while (($row = $result->fetchRow($resultat))) {
			if (!$lastid) {
				$lastid = $row['id'];
				echo "minichat_updatelastid('$channel', $lastid);\n";
			}
            # if timestamp corresponds to previous days than current, show date with display_order according to the global preference
            # daytmes = day from the time stamp of the message; daytnow = current day;
            $daytmes = date("d/m/y", $row['ts']);
            $daytnow = date("d/m/y");
            if ($daytmes == $daytnow) {
                $t = date("H:i", $row['ts']);
            } else {
                if ($prefs['display_field_order'] == 'DMY') {
                    $t = date("d/m/y H:i", $row['ts']);
                } elseif ($prefs['display_field_order'] == 'DYM') {
                    $t = date("d/y/m H:i", $row['ts']);
                } elseif ($prefs['display_field_order'] == 'MDY') {
                    $t = date("m/d/y H:i", $row['ts']);
                } elseif ($prefs['display_field_order'] == 'MYD') {
                    $t = date("m/y/d H:i", $row['ts']);
                } elseif ($prefs['display_field_order'] == 'YDM') {
                    $t = date("y/d/m H:i", $row['ts']);
                } elseif ($prefs['display_field_order'] == 'YMD') {
                    $t = date("y/m/d H:i", $row['ts']);
                } else {
                    $t = date("H:i", $row['ts']);
                }
            }
            $msgtotal = "<span class='minichat_ts'>[$t]</span><span class='minichat_nick'>&lt;" . ($row['nick'] === null ? '' : $row['nick']) . "&gt;</span><span class='minichat_msg'>" . htmlentities($row['msg'], ENT_QUOTES, 'UTF-8') . "</span><br>" . $msgtotal;
		}
		$msgtotal = str_replace(":-D", "<img border='0' src='img/smiles/icon_biggrin.gif' width='15' height='15'>", $msgtotal);
		$msgtotal = str_replace(":D", "<img border='0' src='img/smiles/icon_biggrin.gif' width='15' height='15'>", $msgtotal);
		$msgtotal = str_replace(":-/", "<img border='0' src='img/smiles/icon_confused.gif' width='15' height='15'>", $msgtotal);
		$msgtotal = str_replace("8-)", "<img border='0' src='img/smiles/icon_cool.gif' width='19' height='25'>", $msgtotal);
		$msgtotal = str_replace("8)", "<img border='0' src='img/smiles/icon_cool.gif' width='19' height='25'>", $msgtotal);
		$msgtotal = str_replace(":-)", "<img border='0' src='img/smiles/icon_smile.gif' width='16' height='16'>", $msgtotal);
		$msgtotal = str_replace(":)", "<img border='0' src='img/smiles/icon_smile.gif' width='16' height='16'>", $msgtotal);
		$msgtotal = str_replace(":-(", "<img border='0' src='img/smiles/icon_sad.gif' width='40' height='15'>", $msgtotal);
		$msgtotal = str_replace(":(", "<img border='0' src='img/smiles/icon_sad.gif' width='40' height='15'>", $msgtotal);
		$msgtotal = str_replace(":-|", "<img border='0' src='img/smiles/icon_neutral.gif' width='40' height='15'>", $msgtotal);
		$msgtotal = str_replace(":|", "<img border='0' src='img/smiles/icon_neutral.gif' width='40' height='15'>", $msgtotal);
		$msgtotal = str_replace(":-p", "<img border='0' src='img/smiles/icon_razz.gif' width='15' height='15'>", $msgtotal);
		$msgtotal = str_replace(":p", "<img border='0' src='img/smiles/icon_razz.gif' width='15' height='15'>", $msgtotal);
		$msgtotal = str_replace(":-o", "<img border='0' src='img/smiles/icon_surprised.gif' width='15' height='15'>", $msgtotal);
		$msgtotal = str_replace(":o", "<img border='0' src='img/smiles/icon_surprised.gif' width='15' height='15'>", $msgtotal);
		$msgtotal = str_replace(";-)", "<img border='0' src='img/smiles/icon_wink.gif' width='15' height='15'>", $msgtotal);
		$msgtotal = str_replace(";)", "<img border='0' src='img/smiles/icon_wink.gif' width='15' height='15'>", $msgtotal);
		echo "document.getElementById('minichatdiv_'+minichat_getchanid('$channel')).innerHTML=\"$msgtotal\";\n";
		echo "document.getElementById('minichat').scrollTop=99999;\n";
	}
}
echo "minichatlasttimeout = $lasttimeout;\n";
if (!isset($_REQUEST['msg'])) echo "setTimeout('minichat_update()', $lasttimeout);\n";
	
