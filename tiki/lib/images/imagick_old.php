<?php

class Image {
  function __construct ($image) {
    $this->data = imagick_blob2image($image);
  }

  function Image ($image) {
    self::__construct($image);
  }

  function resize($x=0,$y=0) {
    if ($x > 0 and $y > 0 ) {
      imagick_scale(&$this->data,$x+0,$y+0);
    } else if ($x > 0) {
      $y0 = imagick_getheight($this->data);
      $x0 = imagick_getwidth($this->data);
      $r = $x / $x0 ;
      imagick_scale(&$this->data,$x, $y0*$r); 
    } else if ($y > 0) {
      $y0 = imagick_getheight($this->data);
      $x0 = imagick_getwidth($this->data);
      $r = $y / $y0 ;
      imagick_scale(&$this->data,$x0*$r,$y+0); 
    }
  }

  function scale($r) {
    $y0 = imagick_getheight($this->data);
    $x0 = imagick_getwidth($this->data);
    imagick_scale(&$this->data,$x0*$r,$y0*$r);
  }

  function get_mimetype() {
    return imagick_getmimetype($this->data);
  }

  function get_format() {
    $mime =  imagick_getmimetype($this->data);
    return substr($mime,strpos("/",$mime));
  }

  function display() {
    return imagick_image2blob($this->data);
  }

  function convert($format) {
    if (self::is_supported($format)) {
      imagick_convert(&$this->data,strtoupper(trim($format)));
    } else {
      return false;
    }
  }

  function rotate($angle) {
    imagick_rotate(&$this->data, -$angle);
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

  function icon($extension,$xsize = 0,$ysize = 0) {
    $name = "lib/images/icons/$extension.svg";
    if (!file_exists($name)) {
      $name = "lib/images/icons/unknown.svg";
    }
    $image = imagick_readimage($name);
    imagick_convert($image,"PNG");
    if ($xsize > 0 and $ysize> 0 ) {
      imagick_scale(&$image,$xsize+0,$ysize+0);
    } else if ($xsize > 0) {
      $y0 = imagick_getheight($image);
      $x0 = imagick_getwidth($image);
      $r = $xsize / $x0 ;
      imagick_scale(&$this->data,$xsize, $y0*$r); 
    } else if ($ysize > 0) {
      $y0 = imagick_getheight($image);
      $x0 = imagick_getwidth($image);
      $r = $ysize / $y0 ;
      imagick_scale(&$image,$x0*$r,$ysize+0); 
    }
    return imagick_image2blob($image);
  } 

	function get_height() {
    return imagick_getheight($this->data);	  
	}
	function get_width() {
    return imagick_getwidth($this->data);	  
	}
}



?>
