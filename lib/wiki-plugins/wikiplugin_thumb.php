<?php

// Displays a graphical GAUGE
// Usage:
// {GAUGE(params)}description{GAUGE}
// Description is optional and will be displayed below the gauge if present
// Parameters:
//   color	bar color
//   bgcolor	background color
//   max	maximum possible value (default to for percentages 100)
//   value	current value (REQUIRED)
//   size	Bar size 
//   perc	If true then a percentage is displayed
//   height	Bar height
// EXAMPLE:
//
// {GAUGE(perc=>true,value=>35,bgcolor=>#EEEEEE,height=>20)}happy users over total{GAUGE}
function wikiplugin_thumb_help() {
	return tra("Displays the thumbnail for an image").":<br />~np~{THUMB(image=>,max=>,float=>)}".tra("description")."{THUMB}~/np~";
}

function wikiplugin_thumb($data, $params) {
	global $smarty, $tikidomain;
	extract ($params);

	if (!isset($data) or !$data) {
		$data = '&nbsp;';
	}

	if (!isset($max)) {
		$max = 84;
	}
	$style = '';
	if (!isset($float)) {
		$float = "none";
	} elseif ($float == 'right') {
		$style = "margin-left: 2ex;";
	} elseif ($float == 'left') { 
		$style = "margin-right: 2ex;";
	} else {
		$float = "none";
	}

	if (!isset($image)) {
		return "''no image''";
	}

	if ($tikidomain) {
		$image = preg_replace('~wiki_up/~',"wiki_up/$tikidomain/",$image);
	}

	if (!is_file($image)) {
		return "''image not found'' $image";
	}

	list($width, $height, $type, $attr) = getimagesize($image);
	if ($width > $max or $height > $max) {
		if ($width > $height) {
			$factor = $width / $max;
		} else {
			$factor = $height / $max;
		}
		$twidth = floor($width / $factor);
		$theight = floor($height / $factor);
	} else {
		$twidth = $width;
		$theight = $height;
	}
	$html = '';
	if (!$smarty->get_template_vars('overlib_loaded')) {
		$html = '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>';
		$html.= '<script type="text/javascript" language="JavaScript" src="lib/overlib.js"></script>';
		$smarty->assign('overlib_loaded',1);
	}
	$html.= "<a href='#' style='float:$float;$style' ";
	$html.= " onmouseover=\"return overlib('$data',BACKGROUND,'$image',WIDTH,'$width',HEIGHT,$height);\" onmouseout='nd();' >";
	$html.= "<img src='$image' width='$twidth' height='$theight' /></a>";

	return $html;
}

?>
