<?php
/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_thumb.php,v 1.5 2006-08-29 20:19:11 sylvieg Exp $ */
function wikiplugin_thumb_help() {
	return tra("Displays the thumbnail for an image").":<br />~np~{THUMB(image=>,max=>,float=>,url=>)}".tra("description")."{THUMB}~/np~";
}

function wikiplugin_thumb($data, $params) {
	global $smarty, $tikidomain;
	extract ($params,EXTR_SKIP);

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

	if (!isset($url)) {
		$url = "";
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
		$html.= '<script type="text/javascript" src="lib/overlib.js"></script>';
		$smarty->assign('overlib_loaded',1);
	}
	$html.= "<a href='$url' style='float:$float;$style' ";
	$html.= " onmouseover=\"return overlib('$data',BACKGROUND,'$image',WIDTH,'$width',HEIGHT,$height);\" onmouseout='nd();' >";
	$html.= "<img src='$image' width='$twidth' height='$theight' /></a>";

	return $html;
}

?>
