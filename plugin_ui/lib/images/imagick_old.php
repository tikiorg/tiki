<?php

require_once('lib/images/abstract.php');

class Image extends ImageAbstract {

  function __construct($image, $isfile = false) {
    if ( $isfile ) {
      $blob = imagick_readimage($image);
      parent::__construct($blob, false);
    } else {
      parent::__construct($image, false);
      $this->data = imagick_blob2image($image);
    }
  }

  function Image($image, $isfile = false) {
    Image::__construct($image, $isfile);
  }

  function _resize($x, $y) {
    return imagick_scale(&$this->data, $x, $y);
  }

  function get_mimetype() {
    return imagick_getmimetype($this->data);
  }
 
  function set_format($format) {
    $this->format = $format;
    imagick_convert(&$this->data, strtoupper(trim($format)));
  }

  function get_format() {
    return $this->format;
  }

  function display() {
    return imagick_image2blob($this->data);
  }

  function rotate($angle) {
    imagick_rotate(&$this->data, -$angle);
    return true;
  }

  function is_supported($format) {
    // not handled yet: html, mpeg, pdf
    return in_array(strtolower($format), array('art', 'avi', 'avs', 'bmp', 'cin', 'cmyk', 'cur', 'cut', 'dcm', 'dcx', 'dib', 'dpx', 'epdf', 'fits', 'gif', 'gray', 'ico', 'jng', 'jpg', 'jpeg', 'mat', 'miff', 'mono', 'mng', 'mpc', 'msl', 'mtv', 'mvg', 'otb', 'p7', 'palm', 'pbm', 'pcd', 'pcds', 'pcl', 'pcx', 'pdb', 'pfa', 'pfb', 'pgm', 'picon', 'pict', 'pix', 'png', 'pnm', 'ppm', 'psd', 'ptif', 'pwp', 'rgb', 'rgba', 'rla', 'rle', 'sct', 'sfw', 'sgi', 'sun', 'tga', 'tim', 'txt', 'uil', 'uyvy', 'vicar', 'viff', 'wbmp', 'wpg', 'xbm', 'xcf', 'xpm', 'xwd', 'yuv'));
  }

  function get_height() {
    return imagick_getheight($this->data);
  }

  function get_width() {
    return imagick_getwidth($this->data);	  
  }
}

?>
