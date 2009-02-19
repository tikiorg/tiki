/*! 
pmc feb 19, 2009
used in  tiki-admin_include_gal.php
to verify install of gd library in /tiki-admin.php?page=gal
*/
<?php
header("Content-type: image/png");
$im = @imagecreate(110, 20)
or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 0, 0, 255);
$text_color = imagecolorallocate($im, 255, 0, 255);
imagestring($im, 1, 5, 5,  "test GD image", $text_color);
imagepng($im);
imagedestroy($im);
?>
