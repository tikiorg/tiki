<?php

require_once('lib/images/abstract.php');

class Image extends ImageAbstract {

  function __construct($image, $isfile = false) {
    if ( $isfile ) {
      $blob = new Imagick();
      $blob->readImage($image);
      parent::__construct($blob, false);
    } else {
      parent::__construct($image, false);
      $this->data = new Imagick();
      $this->data->readImageBlob($image);
    }
  }

  function Image($image, $isfile = false) {
    Image::__construct($image, $isfile);
  }

  function _resize($x, $y) {
    return $this->data->scaleImage($x, $y);
  }

  function set_format($format) {
    $this->format = $format;
    $this->data->setFormat($format);
  }

  function get_format() {
    return $this->format;
  }

  function display() {
    return $this->data->getImageBlob();
  }

  function rotate($angle) {
    $this->data->rotateImage(-$angle);
    return true;
  }

  function is_supported($format) {
    $image = new Imagick();
    $format = strtoupper(trim($format));

    // Theses formats have pb if multipage document
    switch ($format) {
      case 'PDF':
      case 'PS':
      case 'HTML':
        return false;
    }
    return in_array($format, $image->queryFormats());
  }

  function get_height() {
    return $this->data->getImageHeight();
  }

  function get_width() {
    return $this->data->getImageWidth();
  }
}

?>
