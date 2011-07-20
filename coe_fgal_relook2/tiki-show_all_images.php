<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$fontfile = "/usr/share/fonts/truetype/ttf-bitstream-vera/VeraBd.ttf";
if (!isset($_REQUEST['view'])) {
	if (isset($_REQUEST['id'])) {
		$ex = "&amp;id=" . urlencode($_REQUEST['id']);
	} else {
		$ex = '';
	}
	echo "<html><frameset rows='30,*' border='0'><frame src='tiki-show_all_images.php?view=nav$ex'><frame name='meat' src='tiki-show_all_images.php?view=image$ex'></frameset></html>";
} elseif ($_REQUEST['view'] == 'nav') {
	include_once ("tiki-setup.php");
	echo "<html><body><form target='meat'>";
	if (isset($_REQUEST['id'])) {
		echo "<a href='tiki-browse_gallery.php?galleryId=" . urlencode($_REQUEST['id']) . "' target='_top'>Gallery " . $_REQUEST['id'] . "</a> ";
		$id = $_REQUEST['id'];
	} else {
		echo "<a href='tiki-galleries.php' target='_top'>list</a> ";
		$id = NULL;
	}
	echo "<input type='hidden' name='view' value='image' />";
	echo "<select name='id'>";
	$query = "select `galleryId`,`name` from `tiki_galleries` order by created desc";
	$res = $tikilib->query($query, array());
	while ($r = $res->fetchRow()) {
		echo "<option value='" . $r['galleryId'] . "'";
		if ($r['galleryId'] == $id) {
			echo " selected='selected'";
		}
		echo ">" . $r['name'] . "</option>";
	}
	echo "</select>";
	echo "<input type='submit' value='view' />";
	echo "</form></body>";
	die;
} elseif ($_REQUEST['view'] == 'image') {
	include_once ("tiki-setup.php");
	include_once ("lib/imagegals/imagegallib.php");
	if ($prefs['feature_galleries'] != 'y') {
		header("HTTP/1.0 404 Not Found");
		die;
	}
	if ($tiki_p_admin_galleries != 'y') {
		header("HTTP/1.0 404 Not Found");
		die;
	}
	if (!$_REQUEST['id']) {
		header("HTTP/1.0 404 Not Found");
		die;
	}
	$galleryId = $_REQUEST['id'];
	$itype = 't';
	$scalesize = 0;
	$query = "select `imageId` from `tiki_images` where `galleryId`=?";
	$res = $tikilib->query($query, array((int)$galleryId));
	$numrows = $res->numRows();
	$numcols = 10;
	$gap = 4;
	$heading = 30;
	$square = 80;
	$width = $numcols * $square + ($numcols + 1) * $gap;
	$height = ((ceil($numrows / $numcols)) * ($square + $gap)) + $heading;

	$im = imagecreatetruecolor($width, $height);
	$text_color = imagecolorallocate($im, 0, 0, 0);
	$wh_color = imagecolorallocate($im, 255, 255, 255);
	$bg_color = imagecolorallocate($im, 235, 235, 235);
	$frame_color = imagecolorallocate($im, 0, 0, 0);
	$frame_color_soft = imagecolorallocate($im, 200, 200, 200);
	imagefill($im, 0, 0, $bg_color);
	imagettftext($im, 16, 0, $gap, $gap + 16, $frame_color_soft, "/usr/share/fonts/truetype/ttf-bitstream-vera/VeraBd.ttf", "All images in /gal$galleryId");
	$position_x = $gap;
	$position_y = $heading;
	while ($r = $res->fetchRow()) {
		$id = $r['imageId'];
		$imagegallib->get_image($id, $itype, $scalesize);
		$type = $imagegallib->filetype;
		$thatim = imagecreatefromstring($imagegallib->image);
		$x = $position_x + floor($square - $imagegallib->xsize);
		$y = $position_y;
		imagefilledrectangle($im, $position_x, $position_y, $position_x + $square, $position_y + $square, $wh_color);
		imagecopy($im, $thatim, $x, $y, 0, 0, $imagegallib->xsize, $imagegallib->ysize);
		imagerectangle($im, $position_x, $position_y, $position_x + $square, $position_y + $square, $frame_color_soft);
		imagerectangle($im, $x, $y, $x + $imagegallib->xsize, $y + $imagegallib->ysize, $frame_color);
		if ($x == $position_x) {
			imagestring($im, 2, $x + 4, $y + $imagegallib->ysize + 4, "$id", $text_color);
		} else {
			imagestringup($im, 2, $position_x + 4, $y + $square - 4, "$id", $text_color);
		}
		if (($position_x + $square + $gap) > $width) {
			$position_x = $gap;
			$position_y = $position_y + $square + $gap;
		} else {
			$position_x = $position_x + $square + $gap;
		}
		imagedestroy($thatim);
	}
	session_write_close();
	header("Content-type: image/png");
	header("Content-Disposition: inline; filename=\"allimages_$galleryId.png\"");
	imagepng($im);
	imagedestroy($im);
} else {
	echo "wrong.";
}
