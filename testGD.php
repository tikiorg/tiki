<?php
/*! 
pmc feb 19, 2009
used in tiki-admin_include_gal.php
to verify that the GD library is working in /tiki-admin.php?page=gal
*/
header("Content-type: image/png");
$im = @imagecreate(68, 12)
or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 0, 95, 170);
$text_color = imagecolorallocate($im, 255, 255, 255);
imagestring($im, 1, 2, 2,  "test GD image", $text_color);
imagepng($im);
imagedestroy($im);
?>