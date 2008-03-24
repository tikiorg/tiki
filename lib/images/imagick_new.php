<?php

class Image {
  function __construct ($image) {
    $this->data = new Imagick();
    $this->data->readImageBlob($image);
  }

  function Image ($image) {
    self::__construct($image);
  }

  function resize($x=0,$y=0) {
		if ($x == 0) {
		  $x = $this->data->getImageWidth()*($y/$this->data->getImageHeight());
		}
    $this->data->scaleImage($x+0,$y+0);
  }

  function scale($r) {
    $x0 = $this->data->getImageWidth();
    $y0 = $this->data->getImageHeight();
    $this->data->scaleImage($x0*$r,$y0*$r);
  }

  function get_mimetype() {
	  if ($this->data->getFormat() == '') {
		  $this->data->setFormat("JPG");
		}
    return "image/".$this->data->getFormat();
  }

  function get_format() {
    return $this->data->getFormat();
  }

  function display() {
    return $this->data->getImageBlob();
  }

  function convert($format) {
    if (Image::is_supported($format)) {
      $this->data->setFormat($format);
      return true;
    }
    return false;
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
    return in_array($format,$image->queryFormats());
  }

  function icon($extension,$xsize = 0,$ysize = 0) {
    $name = "lib/images/icons/$extension.svg";
    if (!file_exists($name)) {
      $name = "lib/images/icons/unknown.svg";
    }
    $image = New Imagick();
    $image->readImage($name);
    $image->setFormat("PNG");
    if ($xsize >0 or $ysize >0 ) {
      $image->scaleImage($xsize,$ysize);
    }
    return $image->getImageBlob();
  }

  function get_height() {
	  return $this->data->getImageHeight();
	}
  function get_width() {
	  return $this->data->getImageWidth();
	}
}

?>
