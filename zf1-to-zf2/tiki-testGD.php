<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*!
pmc feb 19, 2009
used in tiki-admin_include_gal.php
to verify that the GD library is working in /tiki-admin.php?page=gal
*/
header("Content-type: image/png");
$im = @imagecreate(68, 12) or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 0, 95, 170);
$text_color = imagecolorallocate($im, 255, 255, 255);
imagestring($im, 1, 2, 2, "test GD image", $text_color);
imagepng($im);
imagedestroy($im);
