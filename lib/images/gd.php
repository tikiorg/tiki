<?php

class Image {
  function __construct ($image) {
    $this->data = imagecreatefromstring($image);
    $exts=get_loaded_extensions();
    $this->format = "";

// Which GD Version do we have?
    if (in_array('gd',$exts)) {
      $this->havegd = true;
/* copied from lib/imagegallib.php */
      if (function_exists("gd_info")) {
        $this->gdinfo = gd_info();
        preg_match("/[0-9]+\.[0-9]+/", $this->gdinfo["GD Version"], $gdversiontmp);
        $this->gdversion = $gdversiontmp[0];
      } else {
        //next try
        ob_start();
        phpinfo (INFO_MODULES);
        if (preg_match('/GD Version.*2.0/', ob_get_contents())) {
          $this->gdversion = "2.0";
        } else {
          // I have no experience ... maybe someone knows better
          $this->gdversion = "1.0";
        }

        $this->gdinfo["JPG Support"] = preg_match('/JPG Support.*enabled/', ob_get_contents());
        $this->gdinfo["PNG Support"] = preg_match('/PNG Support.*enabled/', ob_get_contents());
        $this->gdinfo["GIF Create Support"] = preg_match('/GIF Create Support.*enabled/', ob_get_contents());
        $this->gdinfo["WBMP Support"] = preg_match('/WBMP Support.*enabled/', ob_get_contents());
        $this->gdinfo["XBM Support"] = preg_match('/XBM Support.*enabled/', ob_get_contents());
        ob_end_clean();
      }
    } else {
      $this->havegd = false;
    }
    $this->format = "jpeg";
  }

  function Image ($image) {
    self::__construct($image);
  }

  function resize($x=0,$y=0) {
    $x0 = imagesx($this->data);
    $y0 = imagesy($this->data);
    if ($x > 0 and $y > 0 ) {
      $t = imagecreatetruecolor($x, $y);
      imagecopyresampled($t, $this->data, 0, 0, 0, 0, $x, $y, $x0, $y0);
      $this->data = $t;
    } else if ($x > 0) {
      $r = $x / $x0 ;
      $t = imagecreatetruecolor($x, $y0*$r);
      imagecopyresampled($t, $this->data, 0, 0, 0, 0, $x, $y0*$r, $x0, $y0);
      $this->data = $t;
    } else if ($y > 0) {
      $r = $y / $y0 ;
      $t = imagecreatetruecolor($x0*$r, $y);
      imagecopyresampled($t, $this->data, 0, 0, 0, 0, $x0*$r, $y, $x0, $y0);
      $this->data = $t;
    }
    unset($t);
  }

  function scale($r) {
    $y0 = imagesx($this->data);
    $x0 = imagesy($this->data);
    $t = imagecreatetruecolor($newx, $newy);
    imagecopyresampled($t, $this->data, 0, 0, 0, 0, $x0*$r, $y0*$r, $x0, $y0);
    $this->data = $t;
    unset($t);
  }

  function get_mimetype() {
    return "image/".$this->format;
  }

  function get_format() {
    if ($this->format == "") {
      return "jpeg";
    } else {
      return $this->format;
    }
  }

  function display() {
  
    ob_start();
    switch($this->format) {
      case 'jpeg':
      case 'jpg':
        imagejpeg($this->data);
        break;
      case 'gif':
        imagegif($this->data);
        break;
      case 'png':
        imagepng($this->data);
        break;
      case 'wbmp':
        imagewbmp($this->data);
        break;
      default:
        ob_end_clean();
        return NULL;
    }
    $image = ob_get_contents();
    ob_end_clean();
 
    return $image;
  }

  function convert($format) {
    if (self::is_supported($format)) {
      $this->format = $format;
      return true;
    } else {
      return false;
    }
  }

  function rotate($angle) {
    $this->data = imagerotate($this->data, $angle, 0);
    return true;
  }

  function is_supported($format) {

/* copied from lib/imagegallib.php */
    if (function_exists("gd_info")) {
      $gdinfo = gd_info();
      preg_match("/[0-9]+\.[0-9]+/", $gdinfo["GD Version"], $gdversiontmp);
      $gdversion = $gdversiontmp[0];
    } else {
      //next try
      ob_start();
      phpinfo (INFO_MODULES);
      if (preg_match('/GD Version.*2.0/', ob_get_contents())) {
        $gdversion = "2.0";
      } else {
        // I have no experience ... maybe someone knows better
        $gdversion = "1.0";
      }

      $gdinfo["JPG Support"] = preg_match('/JPG Support.*enabled/', ob_get_contents());
      $gdinfo["PNG Support"] = preg_match('/PNG Support.*enabled/', ob_get_contents());
      $gdinfo["GIF Create Support"] = preg_match('/GIF Create Support.*enabled/', ob_get_contents());
      $gdinfo["WBMP Support"] = preg_match('/WBMP Support.*enabled/', ob_get_contents());
      $gdinfo["XBM Support"] = preg_match('/XBM Support.*enabled/', ob_get_contents());
      ob_end_clean();
    }
    switch(strtolower($format)) {
      case 'jpeg':
      case'jpg':
        if ($gdinfo["JPG Support"]) {
          return true;
        };
      case 'png':
        if ($gdinfo["PNG Support"]){
          return true;
        };
      case 'gif':
        if ($gdinfo["GIF Create Support"]) {
          return true;
        };
      case 'wbmp':
        if ($gdinfo["WBMP Support"]) {
          return true;
        };
      case' xbm':
        if ($gdinfo["XBM Support"]) {
          return true;
        };
      default:
        return false;
    }
  }

  function icon($extension,$xsize = 0,$ysize = 0) {
    $name = "lib/images/icons/$extension.png";
    if (!file_exists($name)) {
      $name = "lib/images/icons/unknown.png";
    }
    $f = fopen($name,'rb');
    $size = filesize($name);
    $image = fread($f,$size);
    fclose($f);
    $fimage = imagecreatefromstring($image);
    $x0 = imagesx($fimage);
    $y0 = imagesy($fimage);
    if ($xsize > 0 and $ysize > 0 ) {
      $t = imagecreatetruecolor($xsize, $ysize);
      imagecopyresampled($t, $fimage, 0, 0, 0, 0, $xsize, $ysize, $x0, $y0);
      $fimage = $t;
    } else if ($xsize > 0) {
      $r = $xsize / $x0 ;
      $t = imagecreatetruecolor($xsize, $y0*$r);
      imagecopyresampled($t, $fimage, 0, 0, 0, 0, $xsize, $y0*$r, $x0, $y0);
      $fimage = $t;
    } else if ($ysize > 0) {
      $r = $ysize / $y0 ;
      $t = imagecreatetruecolor($x0*$r, $ysize);
      imagecopyresampled($t, $fimage, 0, 0, 0, 0, $x0*$r, $ysize, $x0, $y0);
      $fimage = $t;
    }
    unset($t);
    ob_start();
    imagepng($fimage);
    $image = ob_get_contents();
    ob_end_clean();
    return $image;
  } 

  function get_height() {
		// Not yet implemented
    return NULL;
  }
  function get_width() {
		// Not yet implemented
    return NULL;
  }

}


?>
