<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

/* $Id: taillib.php,v 1.4 2004-03-29 21:26:40 mose Exp $*/
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
	$replace[] = "<div href=\"#\" onmouseover='return overlib(\"<b>\\2</b><br/>\\1\");' onmouseout='return nd();' class=\"linkmenu\">\\3</div>";
	$search[] = "/\[|\]/";
	$replace[] = "";
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
