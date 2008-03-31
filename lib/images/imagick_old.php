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
    $format = strtoupper(trim($format));
    switch ($format) {
      case 'PDF':
      case 'PS':
      case 'HTML':
        return false;
    }
    return true;
  }

  function get_height() {
    return imagick_getheight($this->data);
  }

  function get_width() {
    return imagick_getwidth($this->data);	  
  }
}

?>
