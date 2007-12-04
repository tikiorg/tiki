<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/* $Id: taillib.php,v 1.1 2007-12-04 22:46:36 mose Exp $*/
function tail_filter_irc(&$item, $key) {
	# here is a sample line
	# [06-24-03/05:13] <mose> how is everyone ?
	# [time] <user> talks
	$search[] = "/<([^>]*)>/";

	$replace[] = "__\\1__";
	$search[] = "/^\[([^\]]*)\] (.*)$/";
	$replace[] = "<div href=\"#\" onmouseover='return overlib(\"\\1\");' onmouseout='return nd();' class=\"linkmenu\">\\2</div>";
	$search[] = "/\[|\]/";
	$replace[] = "";
	$search[] = "/((https?|ftp):\/\/[-_\.a-zA-Z0-9\/]*)/";
	$replace[] = "[\\1]";
	$item = preg_replace($search, $replace, $item);
}

function tail_filter_apache_errorlog(&$item, $key) {
	$search[] = "/\/home\/mose\/var\/tikicvs/";
	$replace[] = "";
	$search[] = "/^\[([^\]]*)\] \[[^\]]*\] \[[^\]]*\] (.*): (.*)$/";
	$replace[] = "<div href=\"#\" onmouseover='return overlib(\"<b>\\2</b><br />\\1\");' onmouseout='return nd();' class=\"linkmenu\">\\3</div>";
	$search[] = "/\[|\]/";
	$replace[] = "";
	$item = preg_replace($search, $replace, $item);
}

function tail_filter_mask(&$item,$key) {
	$search[] = "/^\[([^\]]*)\] \[[^\]]*\] \[[^\]]*\] (.*): (.*)$/";
	$replace[] = "<div href=\"#\" onmouseover='return overlib(\"<b>\\2</b><br />\\1\");' onmouseout='return nd();' class=\"linkmenu\">\\3</div>";

	$search[] = "~on line ([0-9]*)~";
	$replace[] = "on line <b'>\\1</b>";
	
	$search[] = "~\[([^\]]*)\]~";
	$replace[] = "<span style='font-size:x-small;'>\\1</span>";
	
	$search[] = "~/usr/local/tiki18/([^ ]*)~";
	$replace[] = "tikiwiki.org: <b>\\1</b>";
	$search[] = "~/home/multitiki/([^ ]*)~";
	$replace[] = "multitiki: <b>\\1</b>";
	$search[] = "~/var/www/([^ ]*)~";
	$replace[] = "www: <b>\\1</b>";
	$search[] = "~/home/mose/([^ ]*)~";
	$replace[] = "mose: <b>\\1</b>";
	
	$search[] = "~^(.*PHP Fatal error.*)$~";
	$replace[] = "<div style='background-color:#eeaa99;border-bottom:1px solid #999966;'>\\1</div>";
	$search[] = "~^(.*PHP Warning.*)$~";
	$replace[] = "<div style='background-color:#eedd99;border-bottom:1px solid #999966;'>\\1</div>";
	$search[] = "~^(.*PHP Notice.*)$~";
	$replace[] = "<div style='background-color:#eeeebb;border-bottom:1px solid #999966;'>\\1</div>";
  $item = preg_replace($search, $replace, $item);
}

function tail_read($file, $lines) {
	if (!is_file($file)) {
		return false;
	} else {
		$content = file($file);

		$back = array_slice($content, count($content) - $lines, $lines);
		return $back;
	}
}

?>
