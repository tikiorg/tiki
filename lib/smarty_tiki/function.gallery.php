<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_function_gallery($params, $smarty)
{
	$tikilib = TikiLib::lib('tiki');
	$imagegallib = TikiLib::lib('imagegal');
	extract($params);
	// Param = id

	if (empty($id)) {
		trigger_error("assign: missing 'id' parameter");
		return;
	}
	$img = $imagegallib->get_random_image($id);
	print('<div style="text-align: center">');
	if (!isset($hidelink) || $hidelink != 1) {
		print('<a href="tiki-browse_image.php?galleryId=' . $img['galleryId'] . '&amp;imageId=' . $img['imageId'] . '">');
	}
	print ('<img alt="thumbnail" class="athumb" src="show_image.php?id=' . $img['imageId'] . '&amp;thumb=1" />');
	if (!isset($hidelink) || $hidelink !=1) {
		print('</a>');
	}
	if (!isset($hideimgname) || $hideimgname !=1) {
		print('<br /><b>'.$img['name'].'</b>');
	}
	if (isset($showgalleryname) && $showgalleryname == 1) {
		print(
						'<br /><small>' . 
						tra("Gallery") . 
						': <a href="tiki-browse_gallery.php?galleryId=' . $img['galleryId'] . '">' .
						$img['gallery'] . 
						'</a></small>'
		);
	}
	print('</div>');
}
