<?php

// HAWIMCONV
// HAWHAW image converter - converts gif, jpg and png images on-the-fly into wbmp format
// (C) 2003 Norbert Huffschmid
// Last modified: 28. June 2003

define("IMG_MAXSIZE", 100000);   // maximum file size of dynamically retrieved image

if (isset($_REQUEST['img']))
{
  // img-parameter contains the input url

  $im_url = $_REQUEST['img'];
  unset($im);

  if (preg_match('/.gif$/i', $im_url))
    $im = @ImageCreateFromGIF($im_url); // not all PHP installations support GIF!
  elseif (preg_match('/.jpg$/i', $im_url))
    $im = @ImageCreateFromJPEG($im_url);
  elseif (preg_match('/.png$/i', $im_url))
    $im = @ImageCreateFromPNG($im_url);
  else
  {
    // no wellknown extension - maybe some dynamically created image ...
    $fp = fopen($im_url, "rb");
    $im_string = fread($fp, IMG_MAXSIZE);

    if (!feof($fp))
      die; // image too large!

    fclose($fp);

    $im = @ImageCreateFromString($im_string);
  }

  if ($im)
  {
    // convert to wbmp and throw out

    header("content-type: image/vnd.wap.wbmp");
    ImageWBMP($im);
  }
}
