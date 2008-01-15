<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/utils_graphic.php,v 1.1 2008-01-15 09:21:14 mose Exp $

function do_image_open($filename) {
  // Disable interlacing for the generated images, as we do not need progressive images 
  // if PDF files (futhermore, FPDF does not support such images)
  $image = do_image_open_wrapped($filename);
  if (!is_null($image)) {
    imageinterlace($image, 0);
  };
  return $image;
}

function do_image_open_wrapped($filename) {
  // FIXME: it will definitely cause problems;
  global $g_config;
  if (!$g_config['renderimages']) return null;

  // get the information about the image
  if (!$data = @getimagesize($filename)) { return null; };
  switch ($data[2]) {
  case 1: // GIF
    // Handle lack of GIF support in older versions of PHP
    if (function_exists('imagecreatefromgif')) {
      return @imagecreatefromgif($filename);
    } else {
      return null;
    };
  case 2: // JPG
    return @imagecreatefromjpeg($filename);
  case 3: // PNG
    return @imagecreatefrompng($filename);
  case 15: // WBMP
    return @imagecreatefromwbmp($filename);
  };
  return null;
};
?>